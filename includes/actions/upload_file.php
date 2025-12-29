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

$files = $_FILES['file'];
$upload_count = 0;
$errors = [];

if (is_array($files['name'])) {
    $count = count($files['name']);
    
    for ($i = 0; $i < $count; $i++) {
        $name = $files['name'][$i];
        $tmp_name = $files['tmp_name'][$i];
        $size = $files['size'][$i];
        $error = $files['error'][$i];

        if ($error === UPLOAD_ERR_OK) {
            $target_file = $target_dir . basename($name);

            if ($size > 10 * 1024 * 1024) { 
                $errors[] = "$name too large";
                continue;
            }

            if (move_uploaded_file($tmp_name, $target_file)) {
                chmod($target_file, 0644);
                $upload_count++;
            } else {
                $errors[] = "Failed to move $name";
            }
        }
    }
} else {
    $name = $files['name'];
    $target_file = $target_dir . basename($name);
    
    if ($files['size'] <= 10 * 1024 * 1024 && move_uploaded_file($files['tmp_name'], $target_file)) {
        chmod($target_file, 0644);
        $upload_count++;
    }
}

$msg = "Uploaded $upload_count files.";
if (!empty($errors)) {
    $msg .= " Errors: " . implode(", ", $errors);
}

header("location: ../../pages/file-manager.php?container=" . $container_name . "&path=" . $path_rel . "&msg=" . urlencode($msg) . "&type=" . (empty($errors) ? "success" : "warning"));
exit;
