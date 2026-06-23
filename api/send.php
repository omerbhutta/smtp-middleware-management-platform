<?php
/**
 * SMTP Gateway API
 *
 * Accepts email send requests via security key authentication.
 *
 * Request params: security, subject, to, body, from, cc, bcc, attachmentURL, attachmentEncoding, attachmentType
 * Response: {"status": true/false, "message": "..."}
 */

define('API_MODE', true);

// Load .env for database config
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) continue;
        $parts = explode('=', $line, 2);
        if (count($parts) === 2) {
            $_ENV[trim($parts[0])] = trim(trim($parts[1]), '"\'');
        }
    }
}

session_start();

require_once __DIR__ . '/../config/constants.php';
require_once HELPER_PATH . 'functions.php';
require_once SERVICE_PATH . 'Database.php';
require_once SERVICE_PATH . 'SmtpMailer.php';
require_once SERVICE_PATH . 'AuditService.php';
require_once MODEL_PATH . 'SecurityKey.php';
require_once MODEL_PATH . 'SmtpAccount.php';
require_once MODEL_PATH . 'EmailLog.php';
require_once MODEL_PATH . 'SuppressionCache.php';
require_once MODEL_PATH . 'Department.php';
require_once MODEL_PATH . 'SystemSetting.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $db = Database::getInstance();
} catch (Exception $e) {
    echo json_encode(['status' => false, 'message' => 'Database connection failed']);
    exit;
}

$securityKey = $_REQUEST['security'] ?? '';
$subject     = $_REQUEST['subject'] ?? '';
$to          = $_REQUEST['to'] ?? '';
$body        = $_REQUEST['body'] ?? '';
$from        = $_REQUEST['from'] ?? '';
$cc          = $_REQUEST['cc'] ?? '';
$bcc         = $_REQUEST['bcc'] ?? '';
$attachmentURL      = $_REQUEST['attachmentURL'] ?? '';
$attachmentEncoding = $_REQUEST['attachmentEncoding'] ?? 'base64';
$attachmentType     = $_REQUEST['attachmentType'] ?? 'application/pdf';
$attachmentData     = $_REQUEST['attachmentData'] ?? '';
$attachmentName     = $_REQUEST['attachmentName'] ?? '';
$attachmentDataEncoding = $_REQUEST['attachmentDataEncoding'] ?? 'base64';
$priority   = $_REQUEST['priority'] ?? null;
$replyTo    = $_REQUEST['replyTo'] ?? '';

if (empty($securityKey)) {
    echo json_encode(['status' => false, 'message' => 'Bad request']);
    exit;
}

if (empty($from)) {
    echo json_encode(['status' => false, 'message' => 'from parameter is required']);
    exit;
}

// Validate security key
$keyModel = new SecurityKey();
$keyRecord = $keyModel->getByApiKey($securityKey);
if (!$keyRecord) {
    $keyRecord = $keyModel->getBySecretKey($securityKey);
}

// Auto-create key under Legacy department if not found (backward compatibility)
if (!$keyRecord) {
    $deptModel = new Department();
    $depts = $deptModel->getAll();
    $legacyDept = null;
    foreach ($depts as $d) {
        if ($d['name'] === 'Legacy') {
            $legacyDept = $d;
            break;
        }
    }
    if (!$legacyDept) {
        $legacyDeptId = $deptModel->create(['name' => 'Legacy', 'description' => 'Auto-created for legacy key support', 'status' => 'active']);
        $legacyDept = $deptModel->getById($legacyDeptId);
    }
    $keyRecord = $keyModel->getBySecretKey($securityKey);
    if (!$keyRecord) {
        $keyId = $keyModel->create([
            'department_id' => $legacyDept['id'],
            'api_key'       => $securityKey,
            'secret_key'    => $securityKey,
        ]);
        $keyRecord = $keyModel->getById($keyId);
    }
}

if (!$keyRecord) {
    echo json_encode(['status' => false, 'message' => 'Invalid security key']);
    exit;
}

// Record key usage
$keyModel->recordUsage($keyRecord['id']);

// Load forbidden emails from system_settings (configured via UI)
$forbiddenEmails = [];
try {
    $settings = new SystemSetting();
    $raw = $settings->get('forbidden_emails');
    if ($raw) {
        $decoded = json_decode($raw, true);
        if (is_array($decoded)) {
            $forbiddenEmails = $decoded;
        }
    }
} catch (Exception $e) {
    // Silently fall back to empty list
}

$recipientArray = splitRecipients($to);

// Filter out forbidden and suppressed recipients
$suppression = new SuppressionCache();
$validRecipients = [];
$skippedRecipients = [];

foreach ($recipientArray as $recipient) {
    if (in_array($recipient, $forbiddenEmails)) {
        $skippedRecipients[] = ['email' => $recipient, 'reason' => 'forbidden'];
    } elseif ($suppression->isSuppressed($recipient)) {
        $skippedRecipients[] = ['email' => $recipient, 'reason' => 'suppressed'];
    } else {
        $validRecipients[] = $recipient;
    }
}

$totalRequested = count($recipientArray);
$totalValid = count($validRecipients);
$totalSkipped = count($skippedRecipients);

if ($totalValid === 0) {
    $reason = $skippedRecipients[0]['reason'] ?? 'invalid';
    $msg = $reason === 'forbidden'
        ? $skippedRecipients[0]['email'] . ' is a Test/Wrong/Invalid Email Address'
        : "Recipient {$skippedRecipients[0]['email']} is suppressed due to previous delivery failures";
    echo json_encode(['status' => false, 'message' => $msg]);
    exit;
}

// Get department and SMTP account
$departmentId = $keyRecord['department_id'];
$smtpModel = new SmtpAccount();
$smtpAccounts = $smtpModel->getActiveByDepartment($departmentId);

if (empty($smtpAccounts)) {
    $smtpAccounts = $smtpModel->getActiveByDepartment(null);
}

if (empty($smtpAccounts)) {
    echo json_encode(['status' => false, 'message' => 'No active SMTP account configured for this department']);
    exit;
}

$smtpAccount = $smtpAccounts[0];

// If a custom from address is provided, try to match it to the correct SMTP account
if (!empty($from)) {
    foreach ($smtpAccounts as $sa) {
        if (strcasecmp($sa['sender_email'], $from) === 0) {
            $smtpAccount = $sa;
            break;
        }
    }
    // If no exact match, try matching by domain
    if ($smtpAccount === $smtpAccounts[0]) {
        $fromDomain = substr(strrchr($from, '@'), 1);
        foreach ($smtpAccounts as $sa) {
            $saDomain = substr(strrchr($sa['sender_email'], '@'), 1);
            if (strcasecmp($saDomain, $fromDomain) === 0) {
                $smtpAccount = $sa;
                break;
            }
        }
    }
}

// Prepare attachments
$attachments = [];

// Multiple URL-based attachments (comma-separated URLs)
if (!empty($attachmentURL)) {
    $urls = splitRecipients($attachmentURL);
    $types = $attachmentType ? splitRecipients($attachmentType) : [];
    foreach ($urls as $i => $url) {
        $attachments[] = [
            'url'      => $url,
            'encoding' => $attachmentEncoding,
            'type'     => $types[$i] ?? 'application/octet-stream',
        ];
    }
}

// Raw binary attachments (base64-encoded data)
if (!empty($attachmentData)) {
    $dataItems = is_array($attachmentData) ? $attachmentData : [$attachmentData];
    $names = is_array($attachmentName) ? $attachmentName : ($attachmentName ? [$attachmentName] : []);
    $dataTypes = is_array($attachmentType) ? $attachmentType : ($attachmentType ? [$attachmentType] : []);
    foreach ($dataItems as $i => $data) {
        if (!empty($data)) {
            $decoded = $attachmentDataEncoding === 'base64' ? base64_decode($data, true) : $data;
            if ($decoded !== false) {
                $attachments[] = [
                    'data'     => $decoded,
                    'name'     => $names[$i] ?? 'attachment_' . ($i + 1),
                    'type'     => $dataTypes[$i] ?? 'application/octet-stream',
                    'encoding' => $attachmentDataEncoding,
                ];
            }
        }
    }
}

// Send email to valid recipients only
$recipientsStr = implode(',', $validRecipients);
$result = SmtpMailer::send(
    $smtpAccount,
    $recipientsStr,
    $subject,
    $body,
    $from ?: null,
    null,
    $bcc,
    $attachments,
    $cc,
    $priority,
    $replyTo ?: null
);

// Build log message with skipped recipient details
$errorMsg = $result['status'] ? null : ($result['message'] ?? null);
if ($totalSkipped > 0) {
    $skippedDetails = [];
    foreach ($skippedRecipients as $sr) {
        $skippedDetails[] = $sr['email'] . ' (' . $sr['reason'] . ')';
    }
    $skipNote = 'Skipped: ' . implode(', ', $skippedDetails);
    $errorMsg = $errorMsg ? $errorMsg . ' | ' . $skipNote : $skipNote;
}

// Log the attempt
$ccStr = $cc ? implode(',', splitRecipients($cc)) : null;
$bccStr = $bcc ? implode(',', splitRecipients($bcc)) : null;
try {
    $logModel = new EmailLog();
    $logModel->create([
        'department_id'    => $departmentId,
        'security_key_id'  => $keyRecord['id'],
        'smtp_account_id'  => $smtpAccount['id'],
        'sender_email'     => $from ?: $smtpAccount['sender_email'],
        'recipients'        => $recipientsStr,
        'cc'               => $ccStr,
        'bcc'              => $bccStr,
        'has_attachment'    => (!empty($attachmentURL) || !empty($attachmentData)) ? 1 : 0,
        'recipient_count'   => $totalValid,
        'total_recipients'  => $totalRequested,
        'subject'           => $subject,
        'source_ip'        => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        'request_path'     => $_SERVER['REQUEST_URI'] ?? '/api/send.php',
        'status'           => $result['status'] ? 'sent' : 'failed',
        'error_message'    => $errorMsg,
    ]);
} catch (Exception $e) {}

$responseMsg = $result['status'] ? 'Email sent successfully' : ($result['message'] ?? 'Failed');
if ($totalSkipped > 0) {
    $skippedNames = [];
    foreach ($skippedRecipients as $sr) {
        $skippedNames[] = $sr['email'];
    }
    $responseMsg .= '. Skipped (' . $totalSkipped . '): ' . implode(', ', $skippedNames);
}

echo json_encode(['status' => $result['status'], 'message' => $responseMsg]);
