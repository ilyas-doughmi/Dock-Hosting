<?php
session_start();


if(!isset($_SESSION["id"])){
    return "you need to loggin first";
}

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '../../../php/connect.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

$db =  new db;


if(isset($_GET["code"])){
    $code = $_GET["code"];

    $payload = [
        'client_id' => $_ENV['GITHUB_CLIENT_ID'],
        'client_secret' => $_ENV['GITHUB_CLIENT_SECRET'],
        'code' => $code
    ];

    $ch = curl_init("https://github.com/login/oauth/access_token");

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);

    $response = curl_exec($ch);
    curl_close($ch);
    $data = json_decode($response, true);
    
    if(isset($data["access_token"])){
        $token = $data["access_token"];
        $query = "INSERT INTO oauth_tokens(user_id,access_token) VALUES(:user_id,:acc_token)";
        $stmt = $db->connect()->prepare($query);
        $stmt->bindValue(":user_id",$_SESSION["id"]);
        $stmt->bindParam(":acc_token",$token);
        $stmt->execute();
        echo "done";
    }
    else{
        echo "nothing found";
    }
}
else{
    echo "problem";
}