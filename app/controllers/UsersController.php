<?php
class UsersController
{
    public function index()
    {
        $auth = new Auth();
        $auth->requireAuth();

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
        $auth->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userModel = new User();
            $data = [
                'username'     => $_POST['username'],
                'email'        => $_POST['email'],
                'password'     => $_POST['password'],
                'role'         => $_POST['role'] ?? 'admin',
                'status'       => $_POST['status'] ?? 'active',
                'mfa_enabled'  => $_POST['mfa_enabled'] ?? 1,
            ];
            $userModel->create($data);
            flash('success', 'User created successfully.');
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
        $auth->requireAuth();

        $userModel = new User();
        $id = $_GET['id'] ?? 0;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'email'       => $_POST['email'],
                'role'        => $_POST['role'] ?? 'admin',
                'status'      => $_POST['status'] ?? 'active',
                'mfa_enabled' => $_POST['mfa_enabled'] ?? 1,
            ];
            if (!empty($_POST['password'])) {
                $data['password'] = $_POST['password'];
            }
            $userModel->update($id, $data);
            flash('success', 'User updated successfully.');
            header('Location: ' . BASE_URL . 'users');
            exit;
        }

        $user = $userModel->getById($id);
        if (!$user) {
            flash('error', 'User not found.');
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
        $auth->requireAuth();

        $id = $_GET['id'] ?? 0;
        $userModel = new User();
        $userModel->delete($id);
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
