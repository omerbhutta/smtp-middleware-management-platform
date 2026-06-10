<?php
class Department
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAll($status = null)
    {
        $sql = "SELECT d.*, (SELECT COUNT(*) FROM security_keys WHERE department_id = d.id) as key_count,
                (SELECT COUNT(*) FROM smtp_accounts WHERE department_id = d.id) as smtp_count
                FROM departments d";
        $params = [];
        if ($status) {
            $sql .= " WHERE d.status = :status";
            $params['status'] = $status;
        }
        $sql .= " ORDER BY d.created_at DESC";
        return $this->db->fetchAll($sql, $params);
    }

    public function getById($id)
    {
        return $this->db->fetchOne("SELECT * FROM departments WHERE id = :id", ['id' => $id]);
    }

    public function create($data)
    {
        $id = $this->db->insert('departments', $data);
        AuditService::log($_SESSION['user_id'] ?? 0, 'Department Created', "Department ID: {$id}, Name: {$data['name']}");
        return $id;
    }

    public function update($id, $data)
    {
        $this->db->update('departments', $data, 'id = :id', ['id' => $id]);
        AuditService::log($_SESSION['user_id'] ?? 0, 'Department Updated', "Department ID: {$id}");
    }

    public function delete($id)
    {
        $this->db->delete('departments', 'id = :id', ['id' => $id]);
        AuditService::log($_SESSION['user_id'] ?? 0, 'Department Deleted', "Department ID: {$id}");
    }

    public function countActive()
    {
        return $this->db->fetchOne("SELECT COUNT(*) as count FROM departments WHERE status = 'active'", [])['count'];
    }

    public function getUsageStats()
    {
        return $this->db->fetchAll("
            SELECT d.name, COUNT(el.id) as email_count
            FROM departments d
            LEFT JOIN email_logs el ON d.id = el.department_id
            GROUP BY d.id
            ORDER BY email_count DESC
            LIMIT 10
        ");
    }
}
