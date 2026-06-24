<?php
class Database
{
    private static $instance = null;
    private $pdo;

    private function __construct()
    {
        $config = require CONFIG_PATH . 'database.php';
        try {
            $port = isset($config['port']) ? ";port={$config['port']}" : '';
            $dsn = "mysql:host={$config['host']}{$port};dbname={$config['database']};charset={$config['charset']}";
            $this->pdo = new PDO($dsn, $config['username'], $config['password'], [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => true,
            ]);
            $tz = $config['timezone'] ?? 'America/Los_Angeles';
            $offset = (new DateTime('now', new DateTimeZone($tz)))->format('P');
            $this->pdo->exec("SET time_zone = '{$offset}'");
        } catch (PDOException $e) {
            if (defined('INSTALL_MODE') && INSTALL_MODE) {
                throw $e;
            }
            die('Database connection failed: ' . $e->getMessage());
        }
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection()
    {
        return $this->pdo;
    }

    public function query($sql, $params = [])
    {
        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $key => $value) {
            if (is_int($value)) {
                $stmt->bindValue(":{$key}", $value, PDO::PARAM_INT);
            } else {
                $stmt->bindValue(":{$key}", $value);
            }
        }
        $stmt->execute();
        return $stmt;
    }

    public function fetchAll($sql, $params = [])
    {
        return $this->query($sql, $params)->fetchAll();
    }

    public function fetchOne($sql, $params = [])
    {
        return $this->query($sql, $params)->fetch();
    }

    public function insert($table, $data)
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
        $this->query($sql, $data);
        return $this->pdo->lastInsertId();
    }

    public function update($table, $data, $where, $whereParams = [])
    {
        $sets = [];
        foreach ($data as $key => $value) {
            $sets[] = "{$key} = :{$key}";
        }
        $sql = "UPDATE {$table} SET " . implode(', ', $sets) . " WHERE {$where}";
        return $this->query($sql, array_merge($data, $whereParams))->rowCount();
    }

    public function delete($table, $where, $params = [])
    {
        $sql = "DELETE FROM {$table} WHERE {$where}";
        return $this->query($sql, $params)->rowCount();
    }

    public function getLastInsertId()
    {
        return $this->pdo->lastInsertId();
    }

    public function columnExists($table, $column)
    {
        try {
            $result = $this->fetchOne("SHOW COLUMNS FROM `{$table}` WHERE Field = :field", ['field' => $column]);
            return $result !== false;
        } catch (Exception $e) {
            return false;
        }
    }
}
