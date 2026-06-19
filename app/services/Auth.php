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
            $_SESSION['full_name']     = $user['full_name'] ?? $user['username'];
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

    public function requireAdmin()
    {
        $this->requireAuth();
        if (($_SESSION['role'] ?? '') !== 'admin') {
            header('Location: ' . BASE_URL . 'dashboard');
            exit;
        }
    }

    public function isUser()
    {
        return ($_SESSION['role'] ?? '') === 'user';
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

    public function getMicrosoftAuthUrl()
    {
        $clientId = $_ENV['MS_CLIENT_ID'] ?? '';
        $tenant = $_ENV['MS_TENANT_ID'] ?? 'common';
        $redirectUri = $_ENV['MS_REDIRECT_URI'] ?? (portalUrl() . 'auth/microsoft/callback');

        if (empty($clientId)) {
            return null;
        }

        $state = bin2hex(random_bytes(16));
        $_SESSION['ms_oauth_state'] = $state;
        return "https://login.microsoftonline.com/{$tenant}/oauth2/v2.0/authorize"
            . "?client_id=" . urlencode($clientId)
            . "&response_type=code"
            . "&redirect_uri=" . urlencode($redirectUri)
            . "&response_mode=query"
            . "&scope=" . urlencode('openid profile email User.Read')
            . "&state=" . urlencode($state);
    }

    public function handleMicrosoftCallback($code, $state)
    {
        // Verify state
        if (!isset($_SESSION['ms_oauth_state']) || $state !== $_SESSION['ms_oauth_state']) {
            unset($_SESSION['ms_oauth_state']);
            return ['error' => 'Invalid state parameter. Please try again.'];
        }
        unset($_SESSION['ms_oauth_state']);

        $clientId = $_ENV['MS_CLIENT_ID'] ?? '';
        $clientSecret = $_ENV['MS_CLIENT_SECRET'] ?? '';
        $tenant = $_ENV['MS_TENANT_ID'] ?? 'common';
        $redirectUri = $_ENV['MS_REDIRECT_URI'] ?? (portalUrl() . 'auth/microsoft/callback');

        if (empty($clientId) || empty($clientSecret)) {
            return ['error' => 'Microsoft login is not configured. Contact an admin.'];
        }

        // Exchange code for token
        $tokenUrl = "https://login.microsoftonline.com/{$tenant}/oauth2/v2.0/token";
        $postData = [
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'code' => $code,
            'redirect_uri' => $redirectUri,
            'grant_type' => 'authorization_code',
        ];

        $ch = curl_init($tokenUrl);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($postData),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => false,
        ]);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            return ['error' => 'Failed to authenticate with Microsoft. Please try again.'];
        }

        $tokenData = json_decode($response, true);
        if (!isset($tokenData['access_token'])) {
            return ['error' => 'Invalid token response from Microsoft.'];
        }

        // Get user info from Microsoft Graph
        $ch = curl_init('https://graph.microsoft.com/v1.0/me');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $tokenData['access_token'],
                'Content-Type: application/json',
            ],
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => false,
        ]);
        $userResponse = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            return ['error' => 'Failed to get user information from Microsoft.'];
        }

        $msUser = json_decode($userResponse, true);
        $email = $msUser['mail'] ?? $msUser['userPrincipalName'] ?? '';

        if (empty($email)) {
            return ['error' => 'Could not retrieve your email from Microsoft.'];
        }

        // Look up user by email
        $user = $this->db->fetchOne(
            "SELECT * FROM users WHERE email = :email AND status = 'active' LIMIT 1",
            ['email' => $email]
        );

        if (!$user) {
            return ['error' => 'Access denied — your email is not registered. Contact an admin.'];
        }

        // Update MS info
        $avatar = $msUser['displayName'] ?? null;
        $this->db->update('users', [
            'ms_id' => $msUser['id'],
            'full_name' => $avatar ?: $user['full_name'],
        ], 'id = :id', ['id' => $user['id']]);

        $this->completeLogin($user['id']);
        $this->logLoginAttempt($user['id'], true);

        return ['success' => true];
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
