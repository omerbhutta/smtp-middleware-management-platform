<?php
class UsersController
{
    public function index()
    {
        $auth = new Auth();
        $auth->requireAdmin();

        $userModel = new User();
        $users = $userModel->getAll();

        $title = 'Users';
        $active_menu = 'users';
        $app_name = 'SMTP Management Platform';
        $app_version = '1.0.0';

        ob_start();
        include VIEW_PATH . 'users/index.php';
        $content = ob_get_clean();
        include VIEW_PATH . 'layouts/main.php';
    }

    public function create()
    {
        $auth = new Auth();
        $auth->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $plainPassword = $_POST['password'];
            $userModel = new User();
            $fullName = $_POST['full_name'];
            $data = [
                'full_name'     => $fullName,
                'username'      => $_POST['username'],
                'email'         => $_POST['email'],
                'password'      => $plainPassword,
                'role'          => $_POST['role'] ?? 'admin',
                'status'        => $_POST['status'] ?? 'active',
                'mfa_enabled'   => $_POST['mfa_enabled'] ?? 1,
            ];
            $userModel->create($data);
            $userUrl = portalUrl();
            SmtpMailer::sendPortalEmail(
                $_POST['email'],
                'Your ' . ($app_name ?? 'SMMP') . ' Account Credentials',
                '<div style="font-family:Arial,Helvetica,sans-serif;max-width:560px;margin:0 auto;background:#fff;border:1px solid #e5e7eb;border-radius:12px;overflow:hidden;">'
                . '<div style="background:linear-gradient(135deg,#3b82f6,#06b6d4);padding:32px;text-align:center;">'
                . '<h1 style="color:#fff;margin:0;font-size:22px;">' . escape($app_name ?? 'SMMP') . '</h1>'
                . '<p style="color:rgba(255,255,255,.85);margin:8px 0 0;font-size:14px;">Welcome, ' . escape($fullName) . '!</p>'
                . '</div>'
                . '<div style="padding:32px;">'
                . '<p style="color:#374151;font-size:15px;line-height:1.6;">Your account has been created. Use the credentials below to sign in:</p>'
                . '<div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;padding:20px;margin:16px 0;">'
                . '<p style="margin:4px 0;font-size:14px;color:#6b7280;"><strong style="color:#374151;">Full Name:</strong> ' . escape($fullName) . '</p>'
                . '<p style="margin:4px 0;font-size:14px;color:#6b7280;"><strong style="color:#374151;">Username:</strong> ' . escape($_POST['username']) . '</p>'
                . '<p style="margin:4px 0;font-size:14px;color:#6b7280;"><strong style="color:#374151;">Password:</strong> <span style="font-family:monospace;background:#e5e7eb;padding:2px 8px;border-radius:4px;">' . escape($plainPassword) . '</span></p>'
                . '</div>'
                . '<a href="' . $userUrl . 'auth/login" style="display:inline-block;padding:12px 32px;background:#3b82f6;color:#fff;text-decoration:none;border-radius:8px;font-size:15px;font-weight:600;">Sign In to Dashboard</a>'
                . '<p style="color:#9ca3af;font-size:12px;margin-top:24px;border-top:1px solid #e5e7eb;padding-top:16px;">This is an automated message from ' . escape($app_name ?? 'SMMP') . '.</p>'
                . '</div>'
                . '</div>'
            );
            notifyAllUsers(
                'New User Account Created',
                'A new user account has been created by ' . ($_SESSION['full_name'] ?? $_SESSION['username']) . ".\n\n"
                . 'Name: ' . $fullName . "\n"
                . 'Username: ' . $_POST['username'] . "\n"
                . 'Email: ' . $_POST['email']
            );
            flash('success', 'User created successfully. Credentials sent to ' . escape($_POST['email']));
            header('Location: ' . BASE_URL . 'users');
            exit;
        }

        $edit_mode = false;
        $user = [];
        $title = 'Create User';
        $active_menu = 'users';
        $app_name = 'SMTP Management Platform';
        $app_version = '1.0.0';

        ob_start();
        include VIEW_PATH . 'users/form.php';
        $content = ob_get_clean();
        include VIEW_PATH . 'layouts/main.php';
    }

    public function edit()
    {
        $auth = new Auth();
        $auth->requireAdmin();

        $userModel = new User();
        $id = $_GET['id'] ?? 0;

        $user = $userModel->getById($id);
        if (!$user) {
            flash('error', 'User not found.');
            header('Location: ' . BASE_URL . 'users');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Handle MS settings save — only update non-empty fields
            if (!empty($_POST['update_ms_settings'])) {
                if (!empty($_POST['ms_client_id'])) {
                    saveEnvSetting('MS_CLIENT_ID', $_POST['ms_client_id']);
                }
                if (!empty($_POST['ms_client_secret'])) {
                    saveEnvSetting('MS_CLIENT_SECRET', $_POST['ms_client_secret']);
                }
                if (!empty($_POST['ms_tenant_id'])) {
                    saveEnvSetting('MS_TENANT_ID', $_POST['ms_tenant_id']);
                }
                if (!empty($_POST['ms_redirect_uri'])) {
                    saveEnvSetting('MS_REDIRECT_URI', $_POST['ms_redirect_uri']);
                }
                flash('success', 'Microsoft login settings saved.');
                header('Location: ' . BASE_URL . 'users/edit?id=' . $id);
                exit;
            }

            $data = [
                'full_name'     => $_POST['full_name'],
                'email'         => $_POST['email'],
                'role'          => $_POST['role'] ?? 'admin',
                'status'        => $_POST['status'] ?? 'active',
                'mfa_enabled'   => $_POST['mfa_enabled'] ?? 1,
            ];
            $passwordChanged = !empty($_POST['password']);
            if ($passwordChanged) {
                $data['password'] = $_POST['password'];
            }
            $userModel->update($id, $data);
            if ($passwordChanged) {
                $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
                $portalUrl = $protocol . '://' . $_SERVER['HTTP_HOST'] . BASE_URL;
                SmtpMailer::sendPortalEmail(
                    $user['email'],
                    'Your ' . ($app_name ?? 'SMMP') . ' Password Has Been Changed',
                    '<div style="font-family:Arial,Helvetica,sans-serif;max-width:560px;margin:0 auto;background:#fff;border:1px solid #e5e7eb;border-radius:12px;overflow:hidden;">'
                    . '<div style="background:linear-gradient(135deg,#f59e0b,#d97706);padding:32px;text-align:center;">'
                    . '<h1 style="color:#fff;margin:0;font-size:22px;">' . escape($app_name ?? 'SMMP') . '</h1>'
                    . '<p style="color:rgba(255,255,255,.85);margin:8px 0 0;font-size:14px;">Password Updated, ' . escape($user['full_name'] ?? $user['username']) . '</p>'
                    . '</div>'
                    . '<div style="padding:32px;">'
                    . '<p style="color:#374151;font-size:15px;line-height:1.6;">Your password has been changed. Here are your updated credentials:</p>'
                    . '<div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;padding:20px;margin:16px 0;">'
                    . '<p style="margin:4px 0;font-size:14px;color:#6b7280;"><strong style="color:#374151;">Username:</strong> ' . escape($user['username']) . '</p>'
                    . '<p style="margin:4px 0;font-size:14px;color:#6b7280;"><strong style="color:#374151;">New Password:</strong> <span style="font-family:monospace;background:#e5e7eb;padding:2px 8px;border-radius:4px;">' . escape($_POST['password']) . '</span></p>'
                    . '</div>'
                    . '<a href="' . $portalUrl . 'auth/login" style="display:inline-block;padding:12px 32px;background:#3b82f6;color:#fff;text-decoration:none;border-radius:8px;font-size:15px;font-weight:600;">Sign In to Dashboard</a>'
                    . '<p style="color:#9ca3af;font-size:12px;margin-top:24px;border-top:1px solid #e5e7eb;padding-top:16px;">This is an automated message from ' . escape($app_name ?? 'SMMP') . '. Please do not reply.</p>'
                    . '</div>'
                    . '</div>'
                );
            }
            flash('success', 'User updated successfully.');
            header('Location: ' . BASE_URL . 'users');
            exit;
        }

        $edit_mode = true;
        $title = 'Edit User';
        $active_menu = 'users';
        $app_name = 'SMTP Management Platform';
        $app_version = '1.0.0';

        ob_start();
        include VIEW_PATH . 'users/form.php';
        $content = ob_get_clean();
        include VIEW_PATH . 'layouts/main.php';
    }

    public function delete()
    {
        $auth = new Auth();
        $auth->requireAdmin();

        $id = $_GET['id'] ?? 0;
        if ($id == ($_SESSION['user_id'] ?? 0)) {
            flash('error', 'You cannot delete your own account.');
            header('Location: ' . BASE_URL . 'users');
            exit;
        }
        $userToDelete = $userModel->getById($id);
        $userModel->delete($id);
        if ($userToDelete) {
            notifyAllUsers(
                'User Account Deleted',
                'User account deleted by ' . ($_SESSION['full_name'] ?? $_SESSION['username']) . ".\n\n"
                . 'Name: ' . ($userToDelete['full_name'] ?? $userToDelete['username']) . "\n"
                . 'Username: ' . $userToDelete['username'] . "\n"
                . 'Email: ' . $userToDelete['email']
            );
        }
        flash('success', 'User deleted successfully.');
        header('Location: ' . BASE_URL . 'users');
        exit;
    }

    public function theme()
    {
        $auth = new Auth();
        $auth->requireAuth();

        $theme = $_GET['theme'] ?? 'dark';
        if (!in_array($theme, ['dark', 'light'])) {
            $theme = 'dark';
        }

        $_SESSION['theme'] = $theme;

        $userModel = new User();
        $userModel->update($_SESSION['user_id'], ['theme' => $theme]);

        header('Content-Type: application/json');
        echo json_encode(['status' => true, 'theme' => $theme]);
        exit;
    }

    public function sidebar()
    {
        $auth = new Auth();
        $auth->requireAuth();

        $state = $_GET['state'] ?? 'collapsed';
        if (!in_array($state, ['collapsed', 'expanded'])) {
            $state = 'collapsed';
        }

        $_SESSION['sidebar_state'] = $state;

        $userModel = new User();
        $userModel->update($_SESSION['user_id'], ['sidebar_state' => $state]);

        header('Content-Type: application/json');
        echo json_encode(['status' => true, 'state' => $state]);
        exit;
    }
}
