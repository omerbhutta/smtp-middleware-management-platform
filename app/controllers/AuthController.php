<?php
class AuthController
{
    private $auth;

    public function __construct()
    {
        $this->auth = new Auth();
    }

    public function login()
    {
        if ($this->auth->isLoggedIn()) {
            header('Location: ' . BASE_URL . 'dashboard');
            exit;
        }

        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

            if (empty($username) || empty($password)) {
                $error = 'Please enter username and password.';
            } else {
                $user = $this->auth->login($username, $password);
                if ($user) {
                    if ($user['mfa_enabled']) {
                        $code = $this->auth->generateMfaCode($user['id']);
                        $emailSent = SmtpMailer::sendPortalEmail($user['email'], 'Your MFA Code', "<h2>Your OTP Code</h2><p style='font-size:24px;font-weight:bold;letter-spacing:5px;'>{$code}</p><p>This code expires in 5 minutes.</p>");
                        if (!$emailSent['status']) {
                            $this->auth->completeLogin($user['id']);
                            AuditService::log($user['id'], 'Login', 'User logged in (MFA skipped - no portal SMTP)');
                            header('Location: ' . BASE_URL . 'dashboard');
                            exit;
                        }
                        header('Location: ' . BASE_URL . 'auth/mfa');
                        exit;
                    } else {
                        $this->auth->completeLogin($user['id']);
                        AuditService::log($user['id'], 'Login', 'User logged in (no MFA)');
                        header('Location: ' . BASE_URL . 'dashboard');
                        exit;
                    }
                } else {
                    $error = 'Invalid username/email or password.';
                }
            }
        }

        $app_name = 'SMTP Management Platform';
        ob_start();
        include VIEW_PATH . 'auth/login.php';
        $content = ob_get_clean();
        include VIEW_PATH . 'layouts/auth.php';
    }

    public function mfa()
    {
        if (!isset($_SESSION['pending_mfa_user_id'])) {
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }

        $error = null;
        $success = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $code = $_POST['code'] ?? '';
            if (empty($code)) {
                $error = 'Please enter the OTP code.';
            } elseif ($this->auth->verifyMfaCode($_SESSION['pending_mfa_user_id'], $code)) {
                $this->auth->completeLogin($_SESSION['pending_mfa_user_id']);
                AuditService::log($_SESSION['user_id'], 'Login', 'User logged in with MFA');
                header('Location: ' . BASE_URL . 'dashboard');
                exit;
            } else {
                $error = 'Invalid or expired OTP code.';
            }
        } else {
            $success = 'An OTP has been sent to your email.';
        }

        $app_name = 'SMTP Management Platform';
        ob_start();
        include VIEW_PATH . 'auth/mfa.php';
        $content = ob_get_clean();
        include VIEW_PATH . 'layouts/auth.php';
    }

    public function logout()
    {
        $this->auth->logout();
        header('Location: ' . BASE_URL . 'auth/login');
        exit;
    }
}
