<?php
session_start();

if (!isset($_SESSION["id"]) || $_SERVER["REQUEST_METHOD"] != "POST") {
    header("location: ../../index.php");
    exit;
}

require_once "../../php/connect.php";
require_once "../../Classes/DatabaseManager.php";

$container_name = $_POST["container"];

if (empty($container_name)) {
    header("location: ../../pages/dashboard.php");
    exit;
}

$dbManager = new DatabaseManager();
$result = $dbManager->deleteDatabase($_SESSION["id"], $container_name);

if ($result) {
    header("location: ../../pages/file-manager.php?container=" . $container_name . "&msg=Database deleted successfully");
} else {
    header("location: ../../pages/file-manager.php?container=" . $container_name . "&msg=Failed to delete database&type=error");
}
exit;
