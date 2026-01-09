<?php
session_start();
header('Content-Type: application/json');

require_once("../php/connect.php");
require_once("../Classes/Project.php");

if (!isset($_SESSION["id"]) || !isset($_GET["container"])) {
    http_response_code(401);
    echo json_encode(["error" => "Unauthorized"]);
    exit;
}

$container_name = $_GET["container"];
$projectObj = new Project();

$user_projects = $projectObj->getProjects($_SESSION["id"]);
$is_owner = false;
foreach ($user_projects as $p) {
    if ($p['container_name'] === $container_name) {
        $is_owner = true;
        break;
    }
}

if (!$is_owner) {
    http_response_code(403);
    echo json_encode(["error" => "Access Denied"]);
    exit;
}


$stats = $projectObj->getContainerStats($container_name);

if ($stats) {
    echo json_encode([
        "success" => true,
        "cpu" => $stats['CPUPerc'] ?? '0%',
        "ram" => $stats['MemUsage'] ?? '0MB'
    ]);
} else {
    echo json_encode(["success" => false]);
}
?>
