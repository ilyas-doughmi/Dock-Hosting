<?php
session_start();

if (!isset($_SESSION["id"])) {
    exit;
}

$container_name = $_GET["container"];
$file_path = $_GET["file"];

$base_path = dirname(__DIR__, 2);
$target_file = $base_path . "/users/Projects/" . $_SESSION["id"] . "/" . $container_name . "/" . $file_path;

if (file_exists($target_file)) {
    $mime = mime_content_type($target_file);
    header("Content-Type: $mime");
    readfile($target_file);
} else {
    echo "File not found";
}
