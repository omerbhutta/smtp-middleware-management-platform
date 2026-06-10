<?php
class MfaCode
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function cleanExpired()
    {
        $this->db->query("DELETE FROM mfa_codes WHERE expires_at < NOW()");
    }
}
