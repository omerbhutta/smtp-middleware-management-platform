<?php
class DeployLog
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function create($data)
    {
        return $this->db->insert('deploy_logs', $data);
    }

    public function update($id, $data)
    {
        $this->db->update('deploy_logs', $data, 'id = :id', ['id' => $id]);
    }

    public function getRecent($limit = 20)
    {
        $sql = "SELECT dl.*, u.full_name, u.username
                FROM deploy_logs dl
                LEFT JOIN users u ON dl.user_id = u.id
                WHERE dl.action IN ('self_update', 'self_full_update')
                ORDER BY dl.created_at DESC
                LIMIT :lim";
        return $this->db->fetchAll($sql, ['lim' => $limit]);
    }
}