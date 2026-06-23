<?php
class ApiController
{
    private $auth;

    public function __construct()
    {
        $this->auth = new Auth();
    }

    public function send()
    {
        header('Content-Type: application/json');

        $key = $_GET['api_key'] ?? $_POST['api_key'] ?? ($_SERVER['HTTP_X_API_KEY'] ?? '');
        if (!$key) {
            http_response_code(401);
            echo json_encode(['success' => false, 'error' => 'API key required']);
            exit;
        }

        $keyModel = new SecurityKey();
        $keyData = $keyModel->getByKey($key);
        if (!$keyData || $keyData['status'] !== 'active') {
            http_response_code(401);
            echo json_encode(['success' => false, 'error' => 'Invalid or inactive API key']);
            exit;
        }

        $to = $_POST['to'] ?? '';
        $subject = $_POST['subject'] ?? '';
        $body = $_POST['body'] ?? '';
        $from = $_POST['from'] ?? '';
        $fromName = $_POST['from_name'] ?? null;
        $cc = $_POST['cc'] ?? null;
        $bcc = $_POST['bcc'] ?? null;

        if (!$to || !$subject || !$body || !$from) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'to, subject, body, and from are required']);
            exit;
        }

        $smtpConfig = $keyData['smtp_config'] ?? null;
        if (!$smtpConfig) {
            $db = Database::getInstance();
            $settings = $db->fetchAll("SELECT setting_key, setting_value FROM system_settings");
            $config = [];
            foreach ($settings as $s) { $config[$s['setting_key']] = $s['setting_value']; }
            $smtpId = $config['portal_smtp_id'] ?? null;
            if (!$smtpId) {
                http_response_code(500);
                echo json_encode(['success' => false, 'error' => 'Portal SMTP not configured']);
                exit;
            }
            $smtpModel = new SmtpAccount();
            $smtp = $smtpModel->getById($smtpId);
            if (!$smtp || $smtp['status'] !== 'active') {
                http_response_code(500);
                echo json_encode(['success' => false, 'error' => 'No active portal SMTP account']);
                exit;
            }
            $smtpConfig = $smtp;
        }

        $toArray = splitRecipients($to);
        $ccArray = [];
        if ($cc) {
            $ccArray = splitRecipients($cc);
        }
        $bccArray = [];
        if ($bcc) {
            $bccArray = splitRecipients($bcc);
        }

        $attachments = [];
        if (!empty($_FILES['attachments'])) {
            $files = $_FILES['attachments'];
            $count = count($files['name']);
            for ($i = 0; $i < $count; $i++) {
                if ($files['error'][$i] === UPLOAD_ERR_OK) {
                    $attachments[] = [
                        'path' => $files['tmp_name'][$i],
                        'name' => $files['name'][$i],
                        'type' => $files['type'][$i],
                    ];
                }
            }
        }

        $result = SmtpMailer::send($smtpConfig, $toArray, $subject, $body, $from, $fromName, $bccArray, $attachments, $ccArray);

        try {
            $logModel = new EmailLog();
            $logModel->create([
                'security_key_id'  => $keyData['id'],
                'department_id'    => $keyData['department_id'],
                'smtp_account_id'  => $smtpConfig['id'],
                'sender_email'     => $from ?? $smtpConfig['sender_email'],
                'recipients'       => implode(',', $toArray),
                'cc'               => $cc ? implode(',', $ccArray) : null,
                'bcc'              => $bcc ? implode(',', $bccArray) : null,
                'has_attachment'   => !empty($_FILES['attachments']) ? 1 : 0,
                'recipient_count'  => count($toArray),
                'total_recipients' => count($toArray) + count($ccArray) + count($bccArray),
                'subject'          => $subject,
                'source_ip'        => $_SERVER['REMOTE_ADDR'] ?? '',
                'status'           => $result['status'] ? 'sent' : 'failed',
                'error_message'    => $result['status'] ? null : ($result['message'] ?? 'Failed'),
            ]);
        } catch (Exception $e) {}

        echo json_encode($result);
    }
}