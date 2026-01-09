<?php
require_once __DIR__ . '/../php/connect.php';

class TrafficLogger extends db {
    public function logVisit() {
        if (strpos($_SERVER['REQUEST_URI'], '/includes/') !== false || 
            strpos($_SERVER['REQUEST_URI'], '/api/') !== false) {
            return;
        }

        $ip = $_SERVER['REMOTE_ADDR'];
        $url = $_SERVER['REQUEST_URI'];
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';

        try {
            $pdo = $this->connect();
            $sql = "INSERT INTO site_analytics (ip_address, page_url, user_agent) VALUES (:ip, :url, :ua)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':ip', $ip);
            $stmt->bindValue(':url', $url);
            $stmt->bindValue(':ua', $ua);
            $stmt->execute();
        } catch (Exception $e) {
        }
    }
}

$tracker = new TrafficLogger();
$tracker->logVisit();
