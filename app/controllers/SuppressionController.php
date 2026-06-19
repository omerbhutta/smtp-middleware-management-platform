<?php
class SuppressionController
{
    public function index()
    {
        $auth = new Auth();
        $auth->requireAuth();

        $suppression = new SuppressionCache();
        $page = $_GET['page'] ?? 1;
        $suppressions = $suppression->getAll((int)$page, 50);

        $title = 'Suppression List';
        $active_menu = 'suppression';
        $app_name = 'SMTP Management Platform';
        $app_version = '1.0.0';

        ob_start();
        include VIEW_PATH . 'suppression/index.php';
        $content = ob_get_clean();
        include VIEW_PATH . 'layouts/main.php';
    }

    public function add()
    {
        $auth = new Auth();
        $auth->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $reason = $_POST['reason'] ?? 'Manually added';
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $suppression = new SuppressionCache();
                $suppression->add($email, $reason, 'manual');
                AuditService::log($_SESSION['user_id'], 'Suppression Added', "Email: {$email}");
                flash('success', "{$email} added to suppression list.");
            } else {
                flash('error', 'Invalid email address.');
            }
        }
        header('Location: ' . BASE_URL . 'suppression');
        exit;
    }

    public function remove()
    {
        $auth = new Auth();
        $auth->requireAdmin();

        $email = $_GET['email'] ?? '';
        if ($email) {
            $suppression = new SuppressionCache();
            $suppression->remove($email);
            AuditService::log($_SESSION['user_id'], 'Suppression Removed', "Email: {$email}");
            flash('success', "{$email} removed from suppression list.");
        }
        header('Location: ' . BASE_URL . 'suppression');
        exit;
    }
}
