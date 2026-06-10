<?php
class EmailLog
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAll($filters = [], $page = 1, $perPage = 50)
    {
        $sql = "SELECT el.*, d.name as department_name, sa.sender_email as smtp_sender
                FROM email_logs el
                LEFT JOIN departments d ON el.department_id = d.id
                LEFT JOIN smtp_accounts sa ON el.smtp_account_id = sa.id
                WHERE 1=1";
        $params = [];

        if (!empty($filters['date_from'])) {
            $sql .= " AND el.created_at >= :date_from";
            $params['date_from'] = $filters['date_from'] . ' 00:00:00';
        }
        if (!empty($filters['date_to'])) {
            $sql .= " AND el.created_at <= :date_to";
            $params['date_to'] = $filters['date_to'] . ' 23:59:59';
        }
        if (!empty($filters['department_id'])) {
            $sql .= " AND el.department_id = :department_id";
            $params['department_id'] = $filters['department_id'];
        }
        if (!empty($filters['smtp_account_id'])) {
            $sql .= " AND el.smtp_account_id = :smtp_account_id";
            $params['smtp_account_id'] = $filters['smtp_account_id'];
        }
        if (!empty($filters['status'])) {
            $sql .= " AND el.status = :status";
            $params['status'] = $filters['status'];
        }
        if (!empty($filters['search'])) {
            $sql .= " AND (el.recipients LIKE :search OR el.sender_email LIKE :search OR el.subject LIKE :search)";
            $params['search'] = '%' . $filters['search'] . '%';
        }

        $countSql = str_replace("SELECT el.*, d.name as department_name, sa.sender_email as smtp_sender", "SELECT COUNT(*) as total", $sql);
        $total = $this->db->fetchOne($countSql, $params)['total'];

        $offset = ($page - 1) * $perPage;
        $sql .= " ORDER BY el.created_at DESC LIMIT :limit OFFSET :offset";
        $params['limit'] = $perPage;
        $params['offset'] = $offset;

        $data = $this->db->fetchAll($sql, $params);

        return [
            'data'        => $data,
            'total'       => $total,
            'page'        => $page,
            'per_page'    => $perPage,
            'total_pages' => ceil($total / $perPage),
        ];
    }

    public function getById($id)
    {
        return $this->db->fetchOne("SELECT el.*, d.name as department_name FROM email_logs el
                                    LEFT JOIN departments d ON el.department_id = d.id WHERE el.id = :id", ['id' => $id]);
    }

    public function create($data)
    {
        return $this->db->insert('email_logs', $data);
    }

    public function getTodayCount()
    {
        return $this->db->fetchOne("SELECT COUNT(*) as count FROM email_logs WHERE DATE(created_at) = CURDATE()", [])['count'];
    }

    public function getWeekCount()
    {
        return $this->db->fetchOne("SELECT COUNT(*) as count FROM email_logs WHERE YEARWEEK(created_at) = YEARWEEK(CURDATE())", [])['count'];
    }

    public function getMonthCount()
    {
        return $this->db->fetchOne("SELECT COUNT(*) as count FROM email_logs WHERE MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())", [])['count'];
    }

    public function getFailedCount()
    {
        return $this->db->fetchOne("SELECT COUNT(*) as count FROM email_logs WHERE status = 'failed'", [])['count'];
    }

    public function getDailyVolume($days = 30)
    {
        return $this->db->fetchAll("
            SELECT DATE(created_at) as date, COUNT(*) as count,
                   SUM(CASE WHEN status = 'sent' THEN 1 ELSE 0 END) as sent,
                   SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed
            FROM email_logs
            WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL :days DAY)
            GROUP BY DATE(created_at)
            ORDER BY date ASC
        ", ['days' => $days]);
    }

    public function getMonthlyTrend($months = 12)
    {
        return $this->db->fetchAll("
            SELECT DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as count
            FROM email_logs
            WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL :months MONTH)
            GROUP BY DATE_FORMAT(created_at, '%Y-%m')
            ORDER BY month ASC
        ", ['months' => $months]);
    }

    public function getFailureAnalysis()
    {
        return $this->db->fetchAll("
            SELECT error_message, COUNT(*) as count
            FROM email_logs
            WHERE status = 'failed' AND error_message IS NOT NULL
            GROUP BY error_message
            ORDER BY count DESC
            LIMIT 10
        ");
    }
}
