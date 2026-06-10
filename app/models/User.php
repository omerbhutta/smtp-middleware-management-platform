<?php
class User
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAll($status = null)
    {
        $sql = "SELECT * FROM users";
        $params = [];
        if ($status) {
            $sql .= " WHERE status = :status";
            $params['status'] = $status;
        }
        $sql .= " ORDER BY created_at DESC";
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
