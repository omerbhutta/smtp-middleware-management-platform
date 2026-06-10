<?php
class SystemSetting
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAll()
    {
        return $this->db->fetchAll("SELECT * FROM system_settings");
    }

    public function get($key)
    {
        $row = $this->db->fetchOne("SELECT setting_value FROM system_settings WHERE setting_key = :key LIMIT 1", ['key' => $key]);
        return $row ? $row['setting_value'] : null;
    }

    public function set($key, $value)
    {
        $existing = $this->get($key);
        if ($existing !== null) {
            $this->db->update('system_settings', ['setting_value' => $value], "setting_key = :key", ['key' => $key]);
        } else {
            $this->db->insert('system_settings', ['setting_key' => $key, 'setting_value' => $value]);
        }
    }

    public function getAllAsArray()
    {
        $rows = $this->getAll();
        $result = [];
        foreach ($rows as $row) {
            $result[$row['setting_key']] = $row['setting_value'];
        }
        return $result;
    }
}
