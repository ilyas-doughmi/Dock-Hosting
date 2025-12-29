<?php

class db {
    private $host;
    private $name;
    private $password;
    private $db_name;

    public function __construct() {
        $this->host = getenv('DB_HOST') ?: 'localhost';
        $this->name = getenv('DB_USER') ?: 'root';
        $this->password = getenv('DB_PASSWORD') ?: '';
        $this->db_name = getenv('DB_NAME') ?: 'dockhosting';
    }

    public function connect() {
        try {
            $pdo = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->name,
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
                ]
            );
            return $pdo;

        } catch (PDOException $e) {
            die("Connexion Failed: " . $e->getMessage());
        }
    }
}



