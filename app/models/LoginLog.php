<?php
class LoginLog
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getRecent($limit = 10)
    {
        return $this->db->fetchAll(
            "SELECT ll.*, u.username FROM login_logs ll
             LEFT JOIN users u ON ll.user_id = u.id
             ORDER BY ll.created_at DESC LIMIT :limit",
            ['limit' => $limit]
        );
    }
}
