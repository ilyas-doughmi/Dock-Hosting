<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION["id"])) {
    http_response_code(401);
    echo json_encode(["error" => "Unauthorized"]);
    exit;
}

require_once("../php/connect.php");
require_once("../Classes/Project.php");

$project = new Project();
$action = $_GET["action"] ?? null;

if ($action === "get_content") {
    $container = $_GET["container"] ?? null;
    $file = $_GET["file"] ?? null;

    if (!$container || !$file) {
        http_response_code(400);
        echo json_encode(["error" => "Missing parameters"]);
        exit;
    }

    $content = $project->getFileContent($container, $file);
    
    $ext = pathinfo($file, PATHINFO_EXTENSION);
    $is_image = in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']);

    echo json_encode([
        "success" => true,
        "content" => $content,
        "is_image" => $is_image,
        "file_path" => $file,
        "file_name" => basename($file)
    ]);
    exit;
}

if ($action === "save_content") {
    $input = json_decode(file_get_contents('php://input'), true);
    $container = $_GET["container"] ?? null;
    $file = $_GET["file"] ?? null;
    $content = $input['content'] ?? null;

    if (!$container || !$file || $content === null) {
        http_response_code(400);
        echo json_encode(["error" => "Missing parameters"]);
        exit;
    }

    $project->saveFileChanges($container, $file, $content);
    
    echo json_encode(["success" => true]);
    exit;
}

echo json_encode(["error" => "Invalid action"]);
