<?php
require_once VENDOR_PATH . 'vendor/phpmailer/Exception.php';
require_once VENDOR_PATH . 'vendor/phpmailer/PHPMailer.php';
require_once VENDOR_PATH . 'vendor/phpmailer/SMTP.php';

class SmtpMailer
{
    public static function send($smtpConfig, $to, $subject, $body, $from = null, $fromName = null, $bcc = [], $attachments = [], $cc = [])
    {
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        try {
            $mail->SMTPDebug = false;
            $mail->isSMTP();
            $mail->Host       = $smtpConfig['smtp_host'];
            $mail->SMTPAuth   = true;
            $mail->Username   = $smtpConfig['smtp_username'];
            $mail->Password   = $smtpConfig['smtp_password'];

            if ($smtpConfig['encryption'] === 'tls') {
                $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            } elseif ($smtpConfig['encryption'] === 'ssl') {
                $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
            } else {
                $mail->SMTPAuth = false;
            }

            $mail->Port = (int)$smtpConfig['smtp_port'];

            $senderEmail = $from ?: $smtpConfig['sender_email'];
            $senderName  = $fromName ?: ($smtpConfig['sender_name'] ?? '');
            $mail->setFrom($senderEmail, $senderName);

            $toArray = is_array($to) ? $to : array_map('trim', explode(',', $to));
            foreach ($toArray as $email) {
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $mail->addAddress($email);
                }
            }

            if (!empty($bcc)) {
                $bccArray = is_array($bcc) ? $bcc : array_map('trim', explode(',', $bcc));
                foreach ($bccArray as $bccEmail) {
                    if (!empty($bccEmail) && filter_var($bccEmail, FILTER_VALIDATE_EMAIL)) {
                        $mail->addBCC($bccEmail);
                    }
                }
            }

            if (!empty($cc)) {
                $ccArray = is_array($cc) ? $cc : array_map('trim', explode(',', $cc));
                foreach ($ccArray as $ccEmail) {
                    if (!empty($ccEmail) && filter_var($ccEmail, FILTER_VALIDATE_EMAIL)) {
                        $mail->addCC($ccEmail);
                    }
                }
            }

            if (!empty($attachments)) {
                foreach ($attachments as $att) {
                    if (isset($att['path'])) {
                        $mail->addAttachment($att['path'], $att['name'] ?? '', $att['encoding'] ?? 'base64', $att['type'] ?? 'application/octet-stream');
                    } elseif (isset($att['url'])) {
                        $content = @file_get_contents($att['url']);
                        if ($content !== false) {
                            $filename = basename($att['url']);
                            $mail->addStringAttachment($content, $filename, $att['encoding'] ?? 'base64', $att['type'] ?? 'application/pdf');
                        }
                    }
                }
            }

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $body;

            $mail->send();
            return ['status' => true, 'message' => 'Mail delivered.'];
        } catch (Exception $e) {
            return ['status' => false, 'message' => $mail->ErrorInfo];
        }
    }

    public static function sendPortalEmail($to, $subject, $body)
    {
        try {
            $db = Database::getInstance();
            $settings = $db->fetchAll("SELECT setting_key, setting_value FROM system_settings");
            $config = [];
            foreach ($settings as $s) {
                $config[$s['setting_key']] = $s['setting_value'];
            }
            $smtpId = $config['portal_smtp_id'] ?? null;
            if (!$smtpId) return ['status' => false, 'message' => 'Portal SMTP not configured'];
            $smtp = $db->fetchOne("SELECT * FROM smtp_accounts WHERE id = :id AND is_portal_smtp = 1 AND status = 'active'", ['id' => $smtpId]);
            if (!$smtp) return ['status' => false, 'message' => 'Portal SMTP account not found'];
            return self::send($smtp, $to, $subject, $body);
        } catch (Exception $e) {
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }
}
