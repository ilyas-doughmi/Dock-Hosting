<?php

require_once __DIR__ . "/../php/connect.php";


class GitHubManager{
    private $db;

    public function __construct()
    {
        $this->db = new db();
    }
    public function getAccessToken($user_id)
    {
        $query = "SELECT * FROM oauth_tokens WHERE user_id = :user_id";
        $stmt = $this->db->connect()->prepare($query);
        $stmt->execute([
            ":user_id" => $user_id
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}