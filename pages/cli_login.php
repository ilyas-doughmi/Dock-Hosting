<?php
session_start();
require_once '../php/connect.php';
if(!isset($_SESSION["id"]))
{
    header("location: ../login.php");
    exit();
}

$port = $_GET["port"] ?? 0;
$username = $_SESSION["username"];
$userId = $_SESSION["id"];

$token = base64_encode($userId . "_SECRET_" . time());

header("Location: http://localhost:$port/callback?token=$token&username=$username");
exit;
