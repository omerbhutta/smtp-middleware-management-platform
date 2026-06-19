<?php
class SuppressionCache
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAll($page = 1, $perPage = 50, $search = '', $sort = 'created_at', $order = 'DESC')
    {
        $countSql = "SELECT COUNT(*) as total FROM suppression_cache";
        $countParams = [];
        if ($search) {
            $countSql .= " WHERE email LIKE :search OR reason LIKE :search2 OR source LIKE :search3";
            $countParams['search'] = "%{$search}%";
            $countParams['search2'] = "%{$search}%";
            $countParams['search3'] = "%{$search}%";
        }
        $total = $this->db->fetchOne($countSql, $countParams)['total'];
        $offset = ($page - 1) * $perPage;

        $dataSql = "SELECT * FROM suppression_cache";
        $dataParams = [];
        if ($search) {
            $dataSql .= " WHERE email LIKE :search OR reason LIKE :search2 OR source LIKE :search3";
            $dataParams['search'] = "%{$search}%";
            $dataParams['search2'] = "%{$search}%";
            $dataParams['search3'] = "%{$search}%";
        }
        $allowed = ['email', 'reason', 'source', 'created_at'];
        $sort = in_array($sort, $allowed) ? $sort : 'created_at';
        $order = strtoupper($order) === 'ASC' ? 'ASC' : 'DESC';
        $dataSql .= " ORDER BY {$sort} {$order} LIMIT :limit OFFSET :offset";
        $dataParams['limit'] = $perPage;
        $dataParams['offset'] = $offset;
        $data = $this->db->fetchAll($dataSql, $dataParams);

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
