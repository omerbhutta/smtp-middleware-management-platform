<?php
class HelpController
{
    public function index()
    {
        $auth = new Auth();
        $auth->requireAuth();

        $title = 'Documentation';
        $active_menu = 'help';
        $app_name = 'SMTP Management Platform';
        $app_version = '1.0.0';

        ob_start();
        include VIEW_PATH . 'help/index.php';
        $content = ob_get_clean();
        include VIEW_PATH . 'layouts/main.php';
    }
}
