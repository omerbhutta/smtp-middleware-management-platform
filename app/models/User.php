<?php
class User
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAllActive()
    {
        return $this->db->fetchAll("SELECT id, username, full_name, email FROM users WHERE status = 'active' ORDER BY full_name ASC");
    }

    public function getAll($status = null, $departmentId = null, $search = '', $sort = 'created_at', $order = 'DESC')
    {
        $sql = "SELECT u.*, d.name as department_name FROM users u
                LEFT JOIN departments d ON u.department_id = d.id";
        $params = [];
        $conditions = [];
        if ($status) {
            $conditions[] = "u.status = :status";
            $params['status'] = $status;
        }
        if ($departmentId) {
            $conditions[] = "u.department_id = :dept_id";
            $params['dept_id'] = $departmentId;
        }
        if ($search) {
            $conditions[] = "(u.full_name LIKE :search OR u.username LIKE :search2 OR u.email LIKE :search3)";
            $params['search'] = "%{$search}%";
            $params['search2'] = "%{$search}%";
            $params['search3'] = "%{$search}%";
        }
        if ($conditions) {
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }
        $allowed = ['full_name', 'username', 'email', 'role', 'status', 'mfa_enabled', 'last_login', 'created_at'];
        $sort = in_array($sort, $allowed) ? $sort : 'created_at';
        $order = strtoupper($order) === 'ASC' ? 'ASC' : 'DESC';
        $sql .= " ORDER BY {$sort} {$order}";
        return $this->db->fetchAll($sql, $params);
    }

    public function getById($id)
    {
        return $this->db->fetchOne("SELECT * FROM users WHERE id = :id", ['id' => $id]);
    }

    public function create($data)
    {
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        $id = $this->db->insert('users', $data);
        AuditService::log($_SESSION['user_id'] ?? 0, 'User Created', "User ID: {$id}, Username: {$data['username']}");
        return $id;
    }

    public function update($id, $data)
    {
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        } else {
            unset($data['password']);
        }
        $this->db->update('users', $data, 'id = :id', ['id' => $id]);
        AuditService::log($_SESSION['user_id'] ?? 0, 'User Updated', "User ID: {$id}");
    }

    public function delete($id)
    {
        $this->db->delete('users', 'id = :id', ['id' => $id]);
        AuditService::log($_SESSION['user_id'] ?? 0, 'User Deleted', "User ID: {$id}");
    }

    public function updateLastLogin($id)
    {
        $this->db->query("UPDATE users SET last_login = NOW() WHERE id = :id", ['id' => $id]);
    }

    public function countByStatus($status)
    {
        return $this->db->fetchOne("SELECT COUNT(*) as count FROM users WHERE status = :status", ['status' => $status])['count'];
    }
}
