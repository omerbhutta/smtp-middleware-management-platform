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

        $toArray = is_array($to) ? $to : array_map('trim', explode(',', $to));
        $ccArray = [];
        if ($cc) {
            $ccArray = is_array($cc) ? $cc : array_map('trim', explode(',', $cc));
        }
        $bccArray = [];
        if ($bcc) {
            $bccArray = is_array($bcc) ? $bcc : array_map('trim', explode(',', $bcc));
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

        if ($result['status']) {
            $logModel = new EmailLog();
            $logModel->log([
                'api_key_id'    => $keyData['id'],
                'department_id' => $keyData['department_id'],
                'recipients'    => implode(',', $toArray),
                'cc'            => implode(',', $ccArray),
                'bcc'           => implode(',', $bccArray),
                'subject'       => $subject,
                'sender_email'  => $from ?? $smtpConfig['sender_email'],
                'sender_name'   => $fromName ?? $smtpConfig['sender_name'],
                'status'        => 'sent',
                'total_recipients' => count($toArray) + count($ccArray) + count($bccArray),
                'source_ip'     => $_SERVER['REMOTE_ADDR'] ?? '',
            ]);
        }

        echo json_encode($result);
    }
}