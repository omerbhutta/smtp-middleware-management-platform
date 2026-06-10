<?php
class Auth
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function login($username, $password)
    {
        $user = $this->db->fetchOne(
            "SELECT * FROM users WHERE (username = :username OR email = :username) AND status = 'active' LIMIT 1",
            ['username' => $username]
        );

        if (!$user || !password_verify($password, $user['password'])) {
            $this->logLoginAttempt(null, false);
            return false;
        }

        $_SESSION['pending_mfa_user_id'] = $user['id'];
        $_SESSION['pending_mfa_username'] = $user['username'];
        return $user;
    }

    public function generateMfaCode($userId)
    {
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $expires = date('Y-m-d H:i:s', time() + 300);
        $this->db->query(
            "UPDATE mfa_codes SET used = 1 WHERE user_id = :user_id AND used = 0",
            ['user_id' => $userId]
        );
        $this->db->insert('mfa_codes', [
            'user_id'    => $userId,
            'code'       => $code,
            'expires_at' => $expires,
        ]);
        return $code;
    }

    public function verifyMfaCode($userId, $code)
    {
        $record = $this->db->fetchOne(
            "SELECT * FROM mfa_codes WHERE user_id = :user_id AND code = :code AND used = 0 AND expires_at > NOW() ORDER BY id DESC LIMIT 1",
            ['user_id' => $userId, 'code' => $code]
        );
        if ($record) {
            $this->db->query(
                "UPDATE mfa_codes SET used = 1 WHERE id = :id",
                ['id' => $record['id']]
            );
            $this->db->query(
                "UPDATE users SET last_login = NOW() WHERE id = :id",
                ['id' => $userId]
            );
            $this->logLoginAttempt($userId, true);
            return true;
        }
        return false;
    }

    public function completeLogin($userId)
    {
        $user = $this->db->fetchOne("SELECT * FROM users WHERE id = :id", ['id' => $userId]);
        if ($user) {
            $_SESSION['user_id']       = $user['id'];
            $_SESSION['username']      = $user['username'];
            $_SESSION['email']         = $user['email'];
            $_SESSION['role']          = $user['role'];
            $_SESSION['department_id'] = $user['department_id'];
            $_SESSION['logged_in']     = true;
            $_SESSION['theme']         = $user['theme'] ?? 'dark';
            $_SESSION['sidebar_state'] = $user['sidebar_state'] ?? 'collapsed';
            unset($_SESSION['pending_mfa_user_id']);
            unset($_SESSION['pending_mfa_username']);
            return true;
        }
        return false;
    }

    public function isLoggedIn()
    {
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }

    public function requireAuth()
    {
        if (!$this->isLoggedIn()) {
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }
    }

    public function getDepartmentId()
    {
        return $_SESSION['department_id'] ?? null;
    }

    public function getCurrentUser()
    {
        if (!$this->isLoggedIn()) return null;
        return $this->db->fetchOne("SELECT * FROM users WHERE id = :id", ['id' => $_SESSION['user_id']]);
    }

    public function logout()
    {
        AuditService::log($_SESSION['user_id'] ?? 0, 'Logout', 'User logged out');
        session_destroy();
    }

    private function logLoginAttempt($userId, $status)
    {
        try {
            $db = Database::getInstance();
            $db->insert('login_logs', [
                'user_id'    => $userId,
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                'status'     => $status ? 'success' : 'failed',
            ]);
        } catch (Exception $e) {}
    }
}
