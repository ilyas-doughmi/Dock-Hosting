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
    public function getAllRepos($user_id)
    {
        $access_token = $this->getAccessToken($user_id);

        if(!$access_token){
            return [];
        }

        $ch = curl_init("https://api.github.com/user/repos");

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/vnd.github+json',
            'Authorization: Bearer ' . $access_token,
            'User-Agent: Dock-Hosting'
        ]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }

    public function disconnect($user_id)
    {
        $query = "DELETE FROM oauth_tokens WHERE user_id = :user_id";
        $stmt = $this->db->connect()->prepare($query);
        return $stmt->execute([":user_id" => $user_id]);
    }
}