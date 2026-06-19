<?php
class AuditController
{
    public function index()
    {
        $auth = new Auth();
        $auth->requireAuth();

        $auditModel = new AuditLog();
        $page = $_GET['page'] ?? 1;
        $search = $_GET['search'] ?? '';
        $sort = $_GET['sort'] ?? 'created_at';
        $order = $_GET['order'] ?? 'DESC';
        $audits = $auditModel->getAll((int)$page, 50, $search, $sort, $order);

        $title = 'Audit Logs';
        $active_menu = 'audit';
        $app_name = 'SMTP Management Platform';
        $app_version = '1.0.0';

        ob_start();
        include VIEW_PATH . 'audit/index.php';
        $content = ob_get_clean();
        include VIEW_PATH . 'layouts/main.php';
    }
}
