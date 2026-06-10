<?php
class SecurityKeysController
{
    public function index()
    {
        $auth = new Auth();
        $auth->requireAuth();

        $keyModel = new SecurityKey();
        $keys = $keyModel->getAll();

        $title = 'Security Keys';
        $active_menu = 'security_keys';
        $app_name = 'SMTP Management Platform';
        $app_version = '1.0.0';

        ob_start();
        include VIEW_PATH . 'security_keys/index.php';
        $content = ob_get_clean();
        include VIEW_PATH . 'layouts/main.php';
    }

    public function create()
    {
        $auth = new Auth();
        $auth->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $keyModel = new SecurityKey();
            $keyModel->create([
                'department_id' => $_POST['department_id'],
            ]);
            flash('success', 'Security key generated successfully.');
            header('Location: ' . BASE_URL . 'security_keys');
            exit;
        }

        $deptModel = new Department();
        $departments = $deptModel->getAll('active');
        $edit_mode = false;
        $key = [];

        $title = 'Generate Security Key';
        $active_menu = 'security_keys';
        $app_name = 'SMTP Management Platform';
        $app_version = '1.0.0';

        ob_start();
        include VIEW_PATH . 'security_keys/form.php';
        $content = ob_get_clean();
        include VIEW_PATH . 'layouts/main.php';
    }

    public function edit()
    {
        $auth = new Auth();
        $auth->requireAuth();

        $keyModel = new SecurityKey();
        $id = $_GET['id'] ?? 0;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $keyModel->update($id, [
                'department_id' => $_POST['department_id'],
                'status'        => $_POST['status'] ?? 'active',
            ]);
            flash('success', 'Security key updated successfully.');
            header('Location: ' . BASE_URL . 'security_keys');
            exit;
        }

        $key = $keyModel->getById($id);
        if (!$key) {
            flash('error', 'Key not found.');
            header('Location: ' . BASE_URL . 'security_keys');
            exit;
        }

        $deptModel = new Department();
        $departments = $deptModel->getAll('active');
        $edit_mode = true;

        $title = 'Edit Security Key';
        $active_menu = 'security_keys';
        $app_name = 'SMTP Management Platform';
        $app_version = '1.0.0';

        ob_start();
        include VIEW_PATH . 'security_keys/form.php';
        $content = ob_get_clean();
        include VIEW_PATH . 'layouts/main.php';
    }

    public function delete()
    {
        $auth = new Auth();
        $auth->requireAuth();

        $id = $_GET['id'] ?? 0;
        $keyModel = new SecurityKey();
        $keyModel->delete($id);
        flash('success', 'Security key deleted successfully.');
        header('Location: ' . BASE_URL . 'security_keys');
        exit;
    }
}
