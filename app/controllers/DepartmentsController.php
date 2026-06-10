<?php
class DepartmentsController
{
    public function index()
    {
        $auth = new Auth();
        $auth->requireAuth();

        $deptModel = new Department();
        $departments = $deptModel->getAll();

        $title = 'Departments';
        $active_menu = 'departments';
        $app_name = 'SMTP Management Platform';
        $app_version = '1.0.0';

        ob_start();
        include VIEW_PATH . 'departments/index.php';
        $content = ob_get_clean();
        include VIEW_PATH . 'layouts/main.php';
    }

    public function create()
    {
        $auth = new Auth();
        $auth->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $deptModel = new Department();
            $deptModel->create([
                'name'        => $_POST['name'],
                'description' => $_POST['description'] ?? '',
                'status'      => $_POST['status'] ?? 'active',
            ]);
            notifyAllUsers(
                'New Department Created',
                'A new department has been created by ' . ($_SESSION['full_name'] ?? $_SESSION['username']) . ".\n\n"
                . 'Department: ' . $_POST['name']
            );
            flash('success', 'Department created successfully.');
            header('Location: ' . BASE_URL . 'departments');
            exit;
        }

        $edit_mode = false;
        $dept = [];
        $title = 'Create Department';
        $active_menu = 'departments';
        $app_name = 'SMTP Management Platform';
        $app_version = '1.0.0';

        ob_start();
        include VIEW_PATH . 'departments/form.php';
        $content = ob_get_clean();
        include VIEW_PATH . 'layouts/main.php';
    }

    public function edit()
    {
        $auth = new Auth();
        $auth->requireAuth();

        $deptModel = new Department();
        $id = $_GET['id'] ?? 0;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $deptModel->update($id, [
                'name'        => $_POST['name'],
                'description' => $_POST['description'] ?? '',
                'status'      => $_POST['status'] ?? 'active',
            ]);
            flash('success', 'Department updated successfully.');
            header('Location: ' . BASE_URL . 'departments');
            exit;
        }

        $dept = $deptModel->getById($id);
        if (!$dept) {
            flash('error', 'Department not found.');
            header('Location: ' . BASE_URL . 'departments');
            exit;
        }

        $edit_mode = true;
        $title = 'Edit Department';
        $active_menu = 'departments';
        $app_name = 'SMTP Management Platform';
        $app_version = '1.0.0';

        ob_start();
        include VIEW_PATH . 'departments/form.php';
        $content = ob_get_clean();
        include VIEW_PATH . 'layouts/main.php';
    }

    public function delete()
    {
        $auth = new Auth();
        $auth->requireAuth();

        $id = $_GET['id'] ?? 0;
        $deptModel = new Department();
        $dept = $deptModel->getById($id);
        $deptModel->delete($id);
        if ($dept) {
            notifyAllUsers(
                'Department Deleted',
                'A department has been deleted by ' . ($_SESSION['full_name'] ?? $_SESSION['username']) . ".\n\n"
                . 'Department: ' . $dept['name']
            );
        }
        flash('success', 'Department deleted successfully.');
        header('Location: ' . BASE_URL . 'departments');
        exit;
    }
}
