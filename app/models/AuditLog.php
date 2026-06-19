<?php
class AuditLog
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAll($page = 1, $perPage = 50, $search = '', $sort = 'created_at', $order = 'DESC')
    {
        $sql = "SELECT COUNT(*) as total FROM audit_logs al
                LEFT JOIN users u ON al.user_id = u.id";
        $countParams = [];
        if ($search) {
            $sql .= " WHERE (al.action LIKE :search OR al.details LIKE :search2 OR u.username LIKE :search3 OR al.ip_address LIKE :search4)";
            $countParams['search'] = "%{$search}%";
            $countParams['search2'] = "%{$search}%";
            $countParams['search3'] = "%{$search}%";
            $countParams['search4'] = "%{$search}%";
        }
        $total = $this->db->fetchOne($sql, $countParams)['total'];
        $offset = ($page - 1) * $perPage;

        $dataSql = "SELECT al.*, u.username FROM audit_logs al
                    LEFT JOIN users u ON al.user_id = u.id";
        $dataParams = [];
        if ($search) {
            $dataSql .= " WHERE (al.action LIKE :search OR al.details LIKE :search2 OR u.username LIKE :search3 OR al.ip_address LIKE :search4)";
            $dataParams['search'] = "%{$search}%";
            $dataParams['search2'] = "%{$search}%";
            $dataParams['search3'] = "%{$search}%";
            $dataParams['search4'] = "%{$search}%";
        }
        $allowed = ['username', 'action', 'ip_address', 'created_at'];
        $sort = in_array($sort, $allowed) ? $sort : 'created_at';
        $order = strtoupper($order) === 'ASC' ? 'ASC' : 'DESC';
        if ($sort === 'username') $sort = 'u.username';
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
