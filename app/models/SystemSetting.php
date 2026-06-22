<?php
class SystemSetting
{
    private $db;
    private static $cache = null;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAll()
    {
        $rows = $this->getAllAsArray();
        $result = [];
        foreach ($rows as $key => $value) {
            $result[] = ['setting_key' => $key, 'setting_value' => $value];
        }
        return $result;
    }

    public function get($key)
    {
        $all = $this->getAllAsArray();
        return $all[$key] ?? null;
    }

    public function set($key, $value)
    {
        $existing = $this->get($key);
        if ($existing !== null) {
            $this->db->update('system_settings', ['setting_value' => $value], "setting_key = :key", ['key' => $key]);
        } else {
            $this->db->insert('system_settings', ['setting_key' => $key, 'setting_value' => $value]);
        }
        self::$cache = null;
    }

    public function getAllAsArray()
    {
        if (self::$cache !== null) {
            return self::$cache;
        }
        $rows = $this->db->fetchAll("SELECT setting_key, setting_value FROM system_settings");
        $result = [];
        foreach ($rows as $row) {
            $result[$row['setting_key']] = $row['setting_value'];
        }
        self::$cache = $result;
        return $result;
    }
}
