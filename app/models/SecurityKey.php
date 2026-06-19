<?php
class SecurityKey
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAll($departmentId = null, $search = '', $sort = 'created_at', $order = 'DESC')
    {
        $sql = "SELECT sk.*, d.name as department_name FROM security_keys sk
                LEFT JOIN departments d ON sk.department_id = d.id";
        $params = [];
        $conditions = [];
        if ($departmentId) {
            $conditions[] = "sk.department_id = :dept_id";
            $params['dept_id'] = $departmentId;
        }
        if ($search) {
            $conditions[] = "(d.name LIKE :search OR sk.api_key LIKE :search2)";
            $params['search'] = "%{$search}%";
            $params['search2'] = "%{$search}%";
        }
        if ($conditions) {
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }
        $allowed = ['department_name', 'status', 'usage_count', 'last_usage', 'created_at'];
        $sort = in_array($sort, $allowed) ? $sort : 'created_at';
        $order = strtoupper($order) === 'ASC' ? 'ASC' : 'DESC';
        if ($sort === 'department_name') $sort = 'd.name';
        $sql .= " ORDER BY {$sort} {$order}";
        return $this->db->fetchAll($sql, $params);
    }

    public function getById($id)
    {
        return $this->db->fetchOne("SELECT sk.*, d.name as department_name FROM security_keys sk
                                    LEFT JOIN departments d ON sk.department_id = d.id WHERE sk.id = :id", ['id' => $id]);
    }

    public function getByApiKey($apiKey)
    {
        return $this->db->fetchOne("SELECT * FROM security_keys WHERE api_key = :api_key AND status = 'active' LIMIT 1", ['api_key' => $apiKey]);
    }

    public function getBySecretKey($secretKey)
    {
        return $this->db->fetchOne("SELECT * FROM security_keys WHERE secret_key = :secret_key AND status = 'active' LIMIT 1", ['secret_key' => $secretKey]);
    }

    public function create($data)
    {
        $data['api_key'] = $data['api_key'] ?? generateApiKey();
        $data['secret_key'] = $data['secret_key'] ?? generateSecretKey();
        $id = $this->db->insert('security_keys', $data);
        AuditService::log($_SESSION['user_id'] ?? 0, 'Security Key Created', "Key ID: {$id}, Department ID: {$data['department_id']}");
        return $id;
    }

    public function update($id, $data)
    {
        $this->db->update('security_keys', $data, 'id = :id', ['id' => $id]);
        AuditService::log($_SESSION['user_id'] ?? 0, 'Security Key Updated', "Key ID: {$id}");
    }

    public function delete($id)
    {
        $this->db->delete('security_keys', 'id = :id', ['id' => $id]);
        AuditService::log($_SESSION['user_id'] ?? 0, 'Security Key Deleted', "Key ID: {$id}");
    }

    public function recordUsage($id)
    {
        $this->db->query("UPDATE security_keys SET last_usage = NOW(), usage_count = usage_count + 1 WHERE id = :id", ['id' => $id]);
    }

    public function countActive()
    {
        return $this->db->fetchOne("SELECT COUNT(*) as count FROM security_keys WHERE status = 'active'", [])['count'];
    }
}
