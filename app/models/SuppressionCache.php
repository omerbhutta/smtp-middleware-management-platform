<?php
class SuppressionCache
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAll($page = 1, $perPage = 50)
    {
        $total = $this->db->fetchOne("SELECT COUNT(*) as total FROM suppression_cache", [])['total'];
        $offset = ($page - 1) * $perPage;

        $data = $this->db->fetchAll(
            "SELECT * FROM suppression_cache ORDER BY created_at DESC LIMIT :limit OFFSET :offset",
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

    public function isSuppressed($email)
    {
        $row = $this->db->fetchOne(
            "SELECT * FROM suppression_cache WHERE email = :email LIMIT 1",
            ['email' => $email]
        );
        return $row ? $row : false;
    }

    public function add($email, $reason = null, $source = 'ebm')
    {
        $existing = $this->isSuppressed($email);
        if (!$existing) {
            $this->db->insert('suppression_cache', [
                'email'  => $email,
                'reason' => $reason,
                'source' => $source,
            ]);
        }
    }

    public function remove($email)
    {
        $this->db->delete('suppression_cache', 'email = :email', ['email' => $email]);
    }

    public function count()
    {
        return $this->db->fetchOne("SELECT COUNT(*) as count FROM suppression_cache", [])['count'];
    }

    public function getStats()
    {
        return $this->db->fetchAll("SELECT source, COUNT(*) as count FROM suppression_cache GROUP BY source");
    }
}
