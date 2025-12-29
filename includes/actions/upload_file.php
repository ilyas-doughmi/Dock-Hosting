<?php
session_start();

if (!isset($_SESSION["id"]) || $_SERVER["REQUEST_METHOD"] != "POST") {
    header("location: ../../index.php");
    exit;
}

$container_name = $_POST["container"] ?? '';
$path_rel = $_POST["path"] ?? '';

if (empty($container_name) || !isset($_FILES['file'])) {
    header("location: ../../pages/dashboard.php?msg=Invalid Request&type=error");
    exit;
}

$base_path = dirname(__DIR__, 2);
$target_dir = $base_path . "/users/Projects/" . $_SESSION["id"] . "/" . $container_name . "/";

if ($path_rel != "") {
    $target_dir .= $path_rel . "/";
}

if (strpos(realpath($target_dir), realpath($base_path . "/users/Projects/" . $_SESSION["id"])) !== 0) {
    header("location: ../../pages/file-manager.php?container=" . $container_name . "&msg=Invalid path&type=error");
    exit;
}

$file = $_FILES['file'];
$file_name = basename($file['name']);
$target_file = $target_dir . $file_name;

if ($file['size'] > 10 * 1024 * 1024) { 
    header("location: ../../pages/file-manager.php?container=" . $container_name . "&path=" . $path_rel . "&msg=File too large (Max 10MB)&type=error");
    exit;
}

if (move_uploaded_file($file['tmp_name'], $target_file)) {
    chmod($target_file, 0644); 
    header("location: ../../pages/file-manager.php?container=" . $container_name . "&path=" . $path_rel . "&msg=File uploaded successfully&type=success");
} else {
    header("location: ../../pages/file-manager.php?container=" . $container_name . "&path=" . $path_rel . "&msg=Upload failed&type=error");
}
exit;
