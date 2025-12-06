<?php

class db {
    private $host = "localhost";
    private $name = "root";
    private $password = "";
    private $db_name = "dockhosting";

    protected function connect() {
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



