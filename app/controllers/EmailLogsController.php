<?php
class EmailLogsController
{
    public function index()
    {
        $auth = new Auth();
        $auth->requireAuth();

        $logModel = new EmailLog();
        $page = $_GET['page'] ?? 1;

        $filters = [
            'date_from'      => $_GET['date_from'] ?? '',
            'date_to'        => $_GET['date_to'] ?? '',
            'department_id'  => $_GET['department_id'] ?? '',
            'smtp_account_id'=> $_GET['smtp_account_id'] ?? '',
            'status'         => $_GET['status'] ?? '',
            'search'         => $_GET['search'] ?? '',
        ];

        $sort = $_GET['sort'] ?? 'created_at';
        $order = $_GET['order'] ?? 'DESC';
        $logs = $logModel->getAll($filters, (int)$page, 50, $sort, $order);

        $deptModel = new Department();
        $departments = $deptModel->getAll('active');

        $title = 'Email Logs';
        $active_menu = 'email_logs';
        $app_name = 'SMTP Management Platform';
        $app_version = '1.0.0';

        ob_start();
        include VIEW_PATH . 'email_logs/index.php';
        $content = ob_get_clean();
        include VIEW_PATH . 'layouts/main.php';
    }

    public function view()
    {
        $auth = new Auth();
        $auth->requireAuth();

        $id = $_GET['id'] ?? 0;
        $logModel = new EmailLog();
        $log = $logModel->getById($id);

        if (!$log) {
            flash('error', 'Log entry not found.');
            header('Location: ' . BASE_URL . 'email_logs');
            exit;
        }

        $title = 'Email Log Detail';
        $active_menu = 'email_logs';
        $app_name = 'SMTP Management Platform';
        $app_version = '1.0.0';

        ob_start();
        include VIEW_PATH . 'email_logs/view.php';
        $content = ob_get_clean();
        include VIEW_PATH . 'layouts/main.php';
    }
}
