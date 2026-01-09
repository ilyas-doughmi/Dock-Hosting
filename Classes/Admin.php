<?php
require_once __DIR__ . '/../php/connect.php';

class Admin extends db {
    
    public function __construct() {
        parent::__construct();
    }

    public function getAllUsers() {
        $query = "SELECT id, username, email, role, created_at FROM users ORDER BY created_at DESC";
        return $this->connect()->query($query)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteUser($id) {

        
        $query = "DELETE FROM users WHERE id = :id";
        $stmt = $this->connect()->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function updateUserRole($id, $role) {
        $query = "UPDATE users SET role = :role WHERE id = :id";
        $stmt = $this->connect()->prepare($query);
        $stmt->bindParam(':role', $role);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
    
    public function getAllLogs() {
        $query = "SELECT audit_logs.*, users.username FROM audit_logs LEFT JOIN users ON audit_logs.user_id = users.id ORDER BY created_at DESC LIMIT 500";
        return $this->connect()->query($query)->fetchAll(PDO::FETCH_ASSOC);
    }


    public function getSystemHealth() {
        $stats = [
            'cpu_usage' => 0,
            'ram_used' => 0,
            'ram_total' => 0,
            'disk_free' => 0,
            'disk_total' => 0
        ];

        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $cmd = "wmic cpu get loadpercentage";
            $output = shell_exec($cmd);
            if (preg_match('/\d+/', $output, $matches)) {
                $stats['cpu_usage'] = $matches[0];
            }

            $cmd = "wmic OS get FreePhysicalMemory,TotalVisibleMemorySize /Value";
            $output = shell_exec($cmd);
            if (preg_match('/FreePhysicalMemory=(\d+)/', $output, $free) && preg_match('/TotalVisibleMemorySize=(\d+)/', $output, $total)) {
                $stats['ram_total'] = round($total[1] / 1024, 2); 
                $stats['ram_used'] = round(($total[1] - $free[1]) / 1024, 2); 
            }
            
            
            $stats['disk_free'] = round(disk_free_space("C:") / 1024 / 1024 / 1024, 2);
            $stats['disk_total'] = round(disk_total_space("C:") / 1024 / 1024 / 1024, 2);

        } else {
            
            $load = sys_getloadavg();
            $stats['cpu_usage'] = round($load[0] * 100); 

            $free = shell_exec('free -m');
            $free = (string)trim($free);
            $free_arr = explode("\n", $free);
            if(count($free_arr) >= 2) {
                $mem = preg_split("/\s+/", $free_arr[1]);
                $stats['ram_used'] = $mem[2];
                $stats['ram_total'] = $mem[1];
            }
            
            $stats['disk_free'] = round(disk_free_space("/") / 1024 / 1024 / 1024, 2);
            $stats['disk_total'] = round(disk_total_space("/") / 1024 / 1024 / 1024, 2);
        }
        
        return $stats;
    }

    public function getTrafficStats() {

        $sql = "SELECT DATE(created_at) as date, COUNT(*) as count 
                FROM site_analytics 
                WHERE created_at >= DATE(NOW()) - INTERVAL 7 DAY 
                GROUP BY DATE(created_at) 
                ORDER BY date ASC";
        $daily = $this->connect()->query($sql)->fetchAll(PDO::FETCH_ASSOC);


        $sql = "SELECT page_url, COUNT(*) as count 
                FROM site_analytics 
                GROUP BY page_url 
                ORDER BY count DESC 
                LIMIT 5";
        $pages = $this->connect()->query($sql)->fetchAll(PDO::FETCH_ASSOC);

        $sql = "SELECT COUNT(*) as count FROM site_analytics WHERE DATE(created_at) = DATE(NOW())";
        $today = $this->connect()->query($sql)->fetchColumn();
        
        return [
            'daily' => $daily,
            'pages' => $pages,
            'today' => $today
        ];
    }

    public function getDockerStats() {
        $cmd = "docker stats --no-stream --format \"{{json .}}\"";
        $output = shell_exec($cmd);
        
        $containers = [];
        if ($output) {
            $lines = explode("\n", trim($output));
            foreach ($lines as $line) {
                if (!empty($line)) {
                    $containers[] = json_decode($line, true);
                }
            }
        }
        return $containers;
    }

    public function getDockerImages() {
        $cmd = "docker images --format \"{{json .}}\"";
        $output = shell_exec($cmd);
        
        $images = [];
        if ($output) {
            $lines = explode("\n", trim($output));
            foreach ($lines as $line) {
                if (!empty($line)) {
                    $json = json_decode($line, true);
                    $images[] = [
                        'Repository' => $json['Repository'] ?? $json['repository'],
                        'Tag' => $json['Tag'] ?? $json['tag'],
                        'ID' => $json['ID'] ?? $json['id'],
                        'CreatedSince' => $json['CreatedSince'] ?? $json['created_since'],
                        'Size' => $json['Size'] ?? $json['size']
                    ];
                }
            }
        }
        return $images;
    }


    public function createAnnouncement($message, $type) {
        $query = "INSERT INTO announcements (message, type) VALUES (:message, :type)";
        $stmt = $this->connect()->prepare($query);
        $stmt->bindParam(':message', $message);
        $stmt->bindParam(':type', $type);
        return $stmt->execute();
    }

    public function deleteAnnouncement($id) {
        $query = "DELETE FROM announcements WHERE id = :id";
        $stmt = $this->connect()->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function getActiveAnnouncements() {
        $query = "SELECT * FROM announcements WHERE is_active = 1 ORDER BY created_at DESC";
        return $this->connect()->query($query)->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getAllAnnouncements() {
        $query = "SELECT * FROM announcements ORDER BY created_at DESC";
        return $this->connect()->query($query)->fetchAll(PDO::FETCH_ASSOC);
    }


    public function toggleMaintenanceMode($status) {
        $value = $status ? '1' : '0';
        $query = "INSERT INTO settings (key_name, value) VALUES ('maintenance_mode', :val) ON DUPLICATE KEY UPDATE value = :val";
        $stmt = $this->connect()->prepare($query);
        $stmt->bindParam(':val', $value);
        return $stmt->execute();
    }

    public function isMaintenanceMode() {
        $query = "SELECT value FROM settings WHERE key_name = 'maintenance_mode'";
        $stmt = $this->connect()->query($query);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return ($result && $result['value'] === '1');
    }
}
