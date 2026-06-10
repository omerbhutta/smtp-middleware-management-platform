<?php
class AuditLog
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAll($page = 1, $perPage = 50)
    {
        $total = $this->db->fetchOne("SELECT COUNT(*) as total FROM audit_logs", [])['total'];
        $offset = ($page - 1) * $perPage;

        $data = $this->db->fetchAll(
            "SELECT al.*, u.username FROM audit_logs al
             LEFT JOIN users u ON al.user_id = u.id
             ORDER BY al.created_at DESC LIMIT :limit OFFSET :offset",
            ['limit' => $perPage, 'offset' => $offset]
        );

        return [
            'data'        => $data,
            'total'       => $total,
            'page'        => $page,
            'per_page'    => $perPage,
            'total_pages' => ceil($total / $perPage),
        ];
    }

    public function getRecent($limit = 10)
    {
        return $this->db->fetchAll(
            "SELECT al.*, u.username FROM audit_logs al
             LEFT JOIN users u ON al.user_id = u.id
             ORDER BY al.created_at DESC LIMIT :limit",
            ['limit' => $limit]
        );
    }
}
