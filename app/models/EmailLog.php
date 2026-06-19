<?php
class EmailLog
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAll($filters = [], $page = 1, $perPage = 50, $sort = 'created_at', $order = 'DESC')
    {
        $sql = "SELECT el.*, d.name as department_name, sa.sender_email as smtp_sender, sk.api_key
                FROM email_logs el
                LEFT JOIN departments d ON el.department_id = d.id
                LEFT JOIN smtp_accounts sa ON el.smtp_account_id = sa.id
                LEFT JOIN security_keys sk ON el.security_key_id = sk.id
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
            $sql .= " AND (el.recipients LIKE :search OR el.sender_email LIKE :search OR el.subject LIKE :search OR el.error_message LIKE :search)";
            $params['search'] = '%' . $filters['search'] . '%';
        }

        $countSql = str_replace("SELECT el.*, d.name as department_name, sa.sender_email as smtp_sender", "SELECT COUNT(*) as total", $sql);
        $total = $this->db->fetchOne($countSql, $params)['total'];

        $offset = ($page - 1) * $perPage;
        $allowed = ['recipients', 'subject', 'department_name', 'sender_email', 'status', 'created_at'];
        $sort = in_array($sort, $allowed) ? $sort : 'created_at';
        $order = strtoupper($order) === 'ASC' ? 'ASC' : 'DESC';
        if ($sort === 'department_name') $sort = 'd.name';
        if ($sort === 'sender_email') $sort = 'el.sender_email';
        $sql .= " ORDER BY {$sort} {$order} LIMIT :limit OFFSET :offset";
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
        return $this->db->fetchOne("SELECT el.*, d.name as department_name, sk.api_key FROM email_logs el
                                    LEFT JOIN departments d ON el.department_id = d.id
                                    LEFT JOIN security_keys sk ON el.security_key_id = sk.id WHERE el.id = :id", ['id' => $id]);
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

    public function getSkippedCount()
    {
        return $this->db->fetchOne("SELECT COUNT(*) as count FROM email_logs WHERE error_message LIKE 'Skipped:%' OR error_message LIKE '%| Skipped:%'", [])['count'];
    }

    public function getSkippedDailyVolume($days = 30)
    {
        return $this->db->fetchAll("
            SELECT DATE(created_at) as date, COUNT(*) as count
            FROM email_logs
            WHERE (error_message LIKE 'Skipped:%' OR error_message LIKE '%| Skipped:%')
            AND created_at >= DATE_SUB(CURDATE(), INTERVAL :days DAY)
            GROUP BY DATE(created_at)
            ORDER BY date ASC
        ", ['days' => $days]);
    }

    public function getSkippedWeeklyCount()
    {
        return $this->db->fetchOne("
            SELECT COUNT(*) as count FROM email_logs
            WHERE (error_message LIKE 'Skipped:%' OR error_message LIKE '%| Skipped:%')
            AND YEARWEEK(created_at) = YEARWEEK(CURDATE())
        ", [])['count'];
    }

    public function getSkippedBreakdown()
    {
        $rows = $this->db->fetchAll("
            SELECT error_message, COUNT(*) as count
            FROM email_logs
            WHERE (error_message LIKE 'Skipped:%' OR error_message LIKE '%| Skipped:%')
            GROUP BY error_message
            ORDER BY count DESC
            LIMIT 10
        ");
        $forbidden = 0;
        $suppressed = 0;
        foreach ($rows as $r) {
            if (str_contains($r['error_message'], 'forbidden')) {
                $forbidden += $r['count'];
            }
            if (str_contains($r['error_message'], 'suppressed')) {
                $suppressed += $r['count'];
            }
        }
        return ['forbidden' => $forbidden, 'suppressed' => $suppressed, 'total' => $forbidden + $suppressed];
    }

    public function getSkippedRecipientCount($days = 30)
    {
        $rows = $this->db->fetchAll("
            SELECT error_message
            FROM email_logs
            WHERE (error_message LIKE 'Skipped:%' OR error_message LIKE '%| Skipped:%')
            AND created_at >= DATE_SUB(CURDATE(), INTERVAL :days DAY)
        ", ['days' => $days]);
        $total = 0;
        foreach ($rows as $r) {
            preg_match_all('/\(forbidden\)|\(suppressed\)/i', $r['error_message'], $m);
            $total += count($m[0]);
        }
        return $total;
    }

    public function getSkippedRecipientDailyVolume($days = 30)
    {
        $rows = $this->db->fetchAll("
            SELECT DATE(created_at) as date, error_message
            FROM email_logs
            WHERE (error_message LIKE 'Skipped:%' OR error_message LIKE '%| Skipped:%')
            AND created_at >= DATE_SUB(CURDATE(), INTERVAL :days DAY)
            ORDER BY created_at ASC
        ", ['days' => $days]);
        $daily = [];
        foreach ($rows as $r) {
            $date = $r['date'];
            if (!isset($daily[$date])) $daily[$date] = 0;
            preg_match_all('/\(forbidden\)|\(suppressed\)/i', $r['error_message'], $m);
            $daily[$date] += count($m[0]);
        }
        // Fill in all dates from the range with 0 for missing dates
        $result = [];
        $start = new DateTime("-{$days} days");
        $end = new DateTime();
        for ($d = clone $start; $d <= $end; $d->modify('+1 day')) {
            $key = $d->format('Y-m-d');
            $result[] = ['date' => $key, 'skipped' => $daily[$key] ?? 0];
        }
        return $result;
    }

    public function getTopDepartments($limit = 6)
    {
        return $this->db->fetchAll("
            SELECT d.name, COUNT(el.id) as email_count
            FROM email_logs el
            INNER JOIN departments d ON el.department_id = d.id
            GROUP BY d.id, d.name
            ORDER BY email_count DESC
            LIMIT :lim
        ", ['lim' => $limit]);
    }

    public function getWeeklyPercentChange()
    {
        $current = $this->getWeekCount();
        $lastWeek = $this->db->fetchOne("SELECT COUNT(*) as count FROM email_logs WHERE YEARWEEK(created_at) = YEARWEEK(DATE_SUB(CURDATE(), INTERVAL 7 DAY))", [])['count'];
        if ($lastWeek == 0) return $current > 0 ? 100 : 0;
        return round((($current - $lastWeek) / $lastWeek) * 100, 1);
    }

    public function getMonthlyPercentChange()
    {
        $current = $this->getMonthCount();
        $lastMonth = $this->db->fetchOne("SELECT COUNT(*) as count FROM email_logs WHERE MONTH(created_at) = MONTH(DATE_SUB(CURDATE(), INTERVAL 1 MONTH)) AND YEAR(created_at) = YEAR(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))", [])['count'];
        if ($lastMonth == 0) return $current > 0 ? 100 : 0;
        return round((($current - $lastMonth) / $lastMonth) * 100, 1);
    }

    public function getFailedPercentChange()
    {
        $current = $this->getFailedCount();
        $lastMonth = $this->db->fetchOne("SELECT COUNT(*) as count FROM email_logs WHERE status = 'failed' AND MONTH(created_at) = MONTH(DATE_SUB(CURDATE(), INTERVAL 1 MONTH)) AND YEAR(created_at) = YEAR(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))", [])['count'];
        if ($lastMonth == 0) return $current > 0 ? 100 : 0;
        return round((($current - $lastMonth) / $lastMonth) * 100, 1);
    }

    public function getDayOfWeekDistribution()
    {
        return $this->db->fetchAll("
            SELECT DAYNAME(created_at) as dayname, DAYOFWEEK(created_at) as dow, COUNT(*) as count
            FROM email_logs
            WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 90 DAY)
            GROUP BY DAYNAME(created_at), DAYOFWEEK(created_at)
            ORDER BY DAYOFWEEK(created_at) ASC
        ");
    }

    public function getHourlyDistribution()
    {
        return $this->db->fetchAll("
            SELECT HOUR(created_at) as hour, COUNT(*) as count
            FROM email_logs
            WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
            GROUP BY HOUR(created_at)
            ORDER BY hour ASC
        ");
    }

    public function getSentFailedSkippedTotals()
    {
        $volume = $this->getDailyVolume(30);
        $sent = 0; $failed = 0;
        foreach ($volume as $v) {
            $sent += (int)$v['sent'];
            $failed += (int)$v['failed'];
        }
        $skipped = $this->getSkippedRecipientCount(30);
        return ['sent' => $sent, 'failed' => $failed, 'skipped' => $skipped];
    }

    public function getTotalEmailCount()
    {
        return $this->db->fetchOne("SELECT COUNT(*) as count FROM email_logs")['count'];
    }

    public function getSuccessRate()
    {
        $total = $this->getTotalEmailCount();
        if ($total == 0) return 100;
        $sent = $this->db->fetchOne("SELECT COUNT(*) as count FROM email_logs WHERE status = 'sent'")['count'];
        return round(($sent / $total) * 100, 1);
    }
}
