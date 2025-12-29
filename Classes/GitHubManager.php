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

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ? $result["access_token"] : null;
    }
}