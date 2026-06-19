<?php
class SmtpAccountsController
{
    public function index()
    {
        $auth = new Auth();
        $auth->requireAdmin();

        $smtpModel = new SmtpAccount();
        $accounts = $smtpModel->getAll();

        $title = 'SMTP Accounts';
        $active_menu = 'smtp_accounts';
        $app_name = 'SMTP Management Platform';
        $app_version = '1.0.0';

        ob_start();
        include VIEW_PATH . 'smtp_accounts/index.php';
        $content = ob_get_clean();
        include VIEW_PATH . 'layouts/main.php';
    }

    public function create()
    {
        $auth = new Auth();
        $auth->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $smtpModel = new SmtpAccount();
            $isPortal = $_POST['is_portal_smtp'] ?? 0;
            $id = $smtpModel->create([
                'department_id'  => $_POST['department_id'] ?: null,
                'provider_type'  => $_POST['provider_type'],
                'smtp_host'      => $_POST['smtp_host'],
                'smtp_port'      => $_POST['smtp_port'],
                'smtp_username'  => $_POST['smtp_username'],
                'smtp_password'  => $_POST['smtp_password'],
                'encryption'     => $_POST['encryption'],
                'sender_email'   => $_POST['sender_email'],
                'sender_name'    => $_POST['sender_name'] ?? '',
                'is_portal_smtp' => $isPortal,
                'status'         => $_POST['status'] ?? 'active',
            ]);
            if ($isPortal) {
                $settings = new SystemSetting();
                $settings->set('portal_smtp_id', $id);
            }
            notifyAllUsers(
                'New SMTP Account Added',
                'A new SMTP account has been added by ' . ($_SESSION['full_name'] ?? $_SESSION['username']) . ".\n\n"
                . 'Provider: ' . $_POST['provider_type'] . "\n"
                . 'Host: ' . $_POST['smtp_host'] . "\n"
                . 'Sender: ' . $_POST['sender_email']
            );
            flash('success', 'SMTP account created successfully.');
            header('Location: ' . BASE_URL . 'smtp_accounts');
            exit;
        }

        $deptModel = new Department();
        $departments = $deptModel->getAll('active');
        $edit_mode = false;
        $acc = [];

        $title = 'Create SMTP Account';
        $active_menu = 'smtp_accounts';
        $app_name = 'SMTP Management Platform';
        $app_version = '1.0.0';

        ob_start();
        include VIEW_PATH . 'smtp_accounts/form.php';
        $content = ob_get_clean();
        include VIEW_PATH . 'layouts/main.php';
    }

    public function edit()
    {
        $auth = new Auth();
        $auth->requireAdmin();

        $smtpModel = new SmtpAccount();
        $id = $_GET['id'] ?? 0;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $isPortal = $_POST['is_portal_smtp'] ?? 0;
            $data = [
                'department_id'  => $_POST['department_id'] ?: null,
                'provider_type'  => $_POST['provider_type'],
                'smtp_host'      => $_POST['smtp_host'],
                'smtp_port'      => $_POST['smtp_port'],
                'smtp_username'  => $_POST['smtp_username'],
                'encryption'     => $_POST['encryption'],
                'sender_email'   => $_POST['sender_email'],
                'sender_name'    => $_POST['sender_name'] ?? '',
                'is_portal_smtp' => $isPortal,
                'status'         => $_POST['status'] ?? 'active',
            ];
            if (!empty($_POST['smtp_password'])) {
                $data['smtp_password'] = $_POST['smtp_password'];
            }
            $smtpModel->update($id, $data);
            if ($isPortal) {
                $settings = new SystemSetting();
                $settings->set('portal_smtp_id', $id);
            }
            flash('success', 'SMTP account updated successfully.');
            header('Location: ' . BASE_URL . 'smtp_accounts');
            exit;
        }

        $acc = $smtpModel->getById($id);
        if (!$acc) {
            flash('error', 'SMTP account not found.');
            header('Location: ' . BASE_URL . 'smtp_accounts');
            exit;
        }

        $deptModel = new Department();
        $departments = $deptModel->getAll('active');
        $edit_mode = true;

        $title = 'Edit SMTP Account';
        $active_menu = 'smtp_accounts';
        $app_name = 'SMTP Management Platform';
        $app_version = '1.0.0';

        ob_start();
        include VIEW_PATH . 'smtp_accounts/form.php';
        $content = ob_get_clean();
        include VIEW_PATH . 'layouts/main.php';
    }

    public function test_smtp()
    {
        $auth = new Auth();
        $auth->requireAdmin();

        header('Content-Type: application/json');

        $id = $_GET['id'] ?? 0;
        $recipient = $_POST['recipient'] ?? '';

        if (!filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['status' => false, 'message' => 'Invalid recipient email address.']);
            exit;
        }

        $smtpModel = new SmtpAccount();
        $acc = $smtpModel->getById($id);
        if (!$acc) {
            echo json_encode(['status' => false, 'message' => 'SMTP account not found.']);
            exit;
        }

        try {
            $result = SmtpMailer::send($acc, $recipient, 'Test Email from SMMP', "<h3>SMTP Test</h3><p>This is a test email sent from <b>SMTP Middleware Management Platform</b>.</p><p>If you received this, your SMTP connection is working correctly.</p>");
            if ($result['status']) {
                echo json_encode(['status' => true, 'message' => 'Test email sent successfully to ' . $recipient]);
            } else {
                echo json_encode(['status' => false, 'message' => $result['message']]);
            }
        } catch (Exception $e) {
            echo json_encode(['status' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    public function delete()
    {
        $auth = new Auth();
        $auth->requireAdmin();

        $id = $_GET['id'] ?? 0;
        $smtpModel = new SmtpAccount();
        $acc = $smtpModel->getById($id);
        $smtpModel->delete($id);
        if ($acc) {
            notifyAllUsers(
                'SMTP Account Deleted',
                'An SMTP account has been deleted by ' . ($_SESSION['full_name'] ?? $_SESSION['username']) . ".\n\n"
                . 'Provider: ' . $acc['provider_type'] . "\n"
                . 'Host: ' . $acc['smtp_host'] . "\n"
                . 'Sender: ' . $acc['sender_email']
            );
        }
        flash('success', 'SMTP account deleted successfully.');
        header('Location: ' . BASE_URL . 'smtp_accounts');
        exit;
    }
}
