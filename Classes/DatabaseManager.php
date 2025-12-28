<?php

class DatabaseManager extends db {

    public function __construct() {
        parent::__construct();
    }

    public function createDatabase($userId, $projectName) {
        $suffix = substr(bin2hex(random_bytes(4)), 0, 6);
        $cleanProjectName = preg_replace('/[^a-zA-Z0-9]/', '', $projectName);
        
        $dbName = "db_" . $userId . "_" . substr($cleanProjectName, 0, 10) . "_" . $suffix;
        $dbUser = "u_" . $userId . "_" . $suffix;
        $dbPass = bin2hex(random_bytes(12));

        try {
            $pdo = $this->connect();
            
            $sql = "
                CREATE DATABASE `$dbName`;
                CREATE USER '$dbUser'@'%' IDENTIFIED BY '$dbPass';
                GRANT ALL PRIVILEGES ON `$dbName`.* TO '$dbUser'@'%';
                FLUSH PRIVILEGES;
            ";
            $pdo->exec($sql);

            $saveSql = "INSERT INTO user_databases (user_id, project_name, db_name, db_user, db_password) VALUES (:uid, :pname, :dbname, :dbuser, :dbpass)";
            $stmt = $pdo->prepare($saveSql);
            $stmt->execute([
                ':uid' => $userId, 
                ':pname' => $projectName,
                ':dbname' => $dbName, 
                ':dbuser' => $dbUser, 
                ':dbpass' => $dbPass
            ]);

            return ['name' => $dbName, 'user' => $dbUser, 'pass' => $dbPass];

        } catch (PDOException $e) {
            return false;
        }
    }

    public function getDatabase($userId, $projectName) {
        $query = "SELECT * FROM user_databases WHERE user_id = :uid AND project_name = :pname LIMIT 1";
        $stmt = $this->connect()->prepare($query);
        $stmt->execute([':uid' => $userId, ':pname' => $projectName]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function deleteDatabase($userId, $projectName) {
        $db = $this->getDatabase($userId, $projectName);
        if (!$db) return false;

        $dbName = $db['db_name'];
        $dbUser = $db['db_user'];

        try {
            $pdo = $this->connect();
            
            $pdo->exec("DROP DATABASE IF EXISTS `$dbName`");
            $pdo->exec("DROP USER IF EXISTS '$dbUser'@'%'");

            $sql = "DELETE FROM user_databases WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':id' => $db['id']]);

            return true;
        } catch (PDOException $e) {
            return false;
        }
    }
}
