<?php
session_start();

if (!isset($_SESSION["id"]) || $_SERVER["REQUEST_METHOD"] != "POST") {
    header("location: ../../index.php");
    exit;
}

require_once "../../php/connect.php";
require_once "../../Classes/DatabaseManager.php";

$container_name = $_POST["container"] ?? '';

if (empty($container_name)) {
    header("location: ../../pages/dashboard.php?msg=Invalid Request&type=error");
    exit;
}

$dbManager = new DatabaseManager();

$existing = $dbManager->getDatabase($_SESSION["id"], $container_name);
if ($existing) {
    header("location: ../../pages/file-manager.php?container=" . $container_name . "&msg=Database already exists&type=error");
    exit;
}

$result = $dbManager->createDatabase($_SESSION["id"], $container_name);

if ($result) {
    header("location: ../../pages/file-manager.php?container=" . $container_name . "&msg=Database created successfully&type=success");
} else {
    header("location: ../../pages/file-manager.php?container=" . $container_name . "&msg=Failed to create database&type=error");
}
exit;
