<?php
require_once __DIR__ . '/../php/connect.php';

class Logger extends db {
    
    public function __construct() {
        parent::__construct();
    }

    public function logActivity($userId, $action, $details = null) {
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? null;
        
        $sql = "INSERT INTO audit_logs (user_id, action, details, ip_address) VALUES (:user_id, :action, :details, :ip_address)";
        $stmt = $this->connect()->prepare($sql);
        
        $stmt->bindValue(':user_id', $userId);
        $stmt->bindValue(':action', $action);
        $stmt->bindValue(':details', $details);
        $stmt->bindValue(':ip_address', $ipAddress);
        
        try {
            $stmt->execute();
        } catch (PDOException $e) {
            error_log("Failed to log activity: " . $e->getMessage());
        }
    }
}
