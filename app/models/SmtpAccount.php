<?php
class SmtpAccount
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAll($departmentId = null, $search = '', $sort = 'created_at', $order = 'DESC')
    {
        $sql = "SELECT sa.*, d.name as department_name FROM smtp_accounts sa
                LEFT JOIN departments d ON sa.department_id = d.id";
        $params = [];
        $conditions = [];
        if ($departmentId) {
            $conditions[] = "sa.department_id = :dept_id";
            $params['dept_id'] = $departmentId;
        }
        if ($search) {
            $conditions[] = "(sa.sender_email LIKE :search OR sa.smtp_host LIKE :search2 OR sa.provider_type LIKE :search3 OR d.name LIKE :search4)";
            $params['search'] = "%{$search}%";
            $params['search2'] = "%{$search}%";
            $params['search3'] = "%{$search}%";
            $params['search4'] = "%{$search}%";
        }
        if ($conditions) {
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }
        $allowed = ['sender_email', 'smtp_host', 'smtp_port', 'provider_type', 'encryption', 'status', 'department_name', 'created_at'];
        $sort = in_array($sort, $allowed) ? $sort : 'created_at';
        $order = strtoupper($order) === 'ASC' ? 'ASC' : 'DESC';
        if ($sort === 'department_name') $sort = 'd.name';
        $sql .= " ORDER BY {$sort} {$order}";
        return $this->db->fetchAll($sql, $params);
    }

    public function getById($id)
    {
        return $this->db->fetchOne("SELECT sa.*, d.name as department_name FROM smtp_accounts sa
                                    LEFT JOIN departments d ON sa.department_id = d.id WHERE sa.id = :id", ['id' => $id]);
    }

    public function getActiveByDepartment($departmentId)
    {
        $sql = "SELECT * FROM smtp_accounts WHERE status = 'active' AND is_portal_smtp = 0";
        $params = [];
        if ($departmentId === null) {
            $sql .= " AND department_id IS NULL";
        } else {
            $sql .= " AND department_id = :dept_id";
            $params['dept_id'] = $departmentId;
        }
        $sql .= " ORDER BY created_at DESC";
        return $this->db->fetchAll($sql, $params);
    }

    public function getPortalSmtp()
    {
        return $this->db->fetchOne("SELECT * FROM smtp_accounts WHERE is_portal_smtp = 1 AND status = 'active' LIMIT 1");
    }

    public function create($data)
    {
        $id = $this->db->insert('smtp_accounts', $data);
        AuditService::log($_SESSION['user_id'] ?? 0, 'SMTP Account Created', "Account ID: {$id}, Host: {$data['smtp_host']}");
        return $id;
    }

    public function update($id, $data)
    {
        if (isset($data['smtp_password']) && empty($data['smtp_password'])) {
            unset($data['smtp_password']);
        }
        $this->db->update('smtp_accounts', $data, 'id = :id', ['id' => $id]);
        AuditService::log($_SESSION['user_id'] ?? 0, 'SMTP Account Updated', "Account ID: {$id}");
    }

    public function delete($id)
    {
        $this->db->delete('smtp_accounts', 'id = :id', ['id' => $id]);
        AuditService::log($_SESSION['user_id'] ?? 0, 'SMTP Account Deleted', "Account ID: {$id}");
    }

    public function countActive()
    {
        return $this->db->fetchOne("SELECT COUNT(*) as count FROM smtp_accounts WHERE status = 'active' AND is_portal_smtp = 0", [])['count'];
    }

    public function getUsageStats()
    {
        return $this->db->fetchAll("
            SELECT sa.sender_email, sa.smtp_host, COUNT(el.id) as email_count
            FROM smtp_accounts sa
            LEFT JOIN email_logs el ON sa.id = el.smtp_account_id
            WHERE sa.is_portal_smtp = 0
            GROUP BY sa.id
            ORDER BY email_count DESC
            LIMIT 10
        ");
    }
}
