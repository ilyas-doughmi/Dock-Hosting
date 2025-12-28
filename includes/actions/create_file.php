<?php
session_start();

if (!isset($_SESSION["id"]) || $_SERVER["REQUEST_METHOD"] != "POST") {
    header("location: ../../index.php");
    exit;
}

$container_name = $_POST["container"];
$path_rel = $_POST["path"];
$file_name = $_POST["name"];

if (empty($container_name) || empty($file_name)) {
    header("location: ../../pages/dashboard.php");
    exit;
}

$base_path = dirname(__DIR__, 2);
$target_dir = $base_path . "/users/Projects/" . $_SESSION["id"] . "/" . $container_name . "/";

if ($path_rel != "") {
    $target_dir .= $path_rel . "/";
}

$full_path = $target_dir . $file_name;

if (strpos(realpath($target_dir), realpath($base_path . "/users/Projects/" . $_SESSION["id"])) !== 0) {
    header("location: ../../pages/file-manager.php?container=" . $container_name . "&error=invalid_path");
    exit;
}

if (!file_exists($full_path)) {
    file_put_contents($full_path, ""); 
}

header("location: ../../pages/file-manager.php?container=" . $container_name . "&path=" . $path_rel);
exit;
