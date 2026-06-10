<?php
class AuditService
{
    public static function log($userId, $action, $details = null)
    {
        try {
            $db = Database::getInstance();
            $db->insert('audit_logs', [
                'user_id'    => $userId ?: null,
                'action'     => $action,
                'details'    => $details,
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            ]);
        } catch (Exception $e) {}
    }
}
