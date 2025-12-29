<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();


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
    
    echo "<pre>";
    print_r($data);
    echo "</pre>";
}
else{
    echo "problem";
}