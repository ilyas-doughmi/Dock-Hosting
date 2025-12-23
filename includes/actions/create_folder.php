<?php
session_start();

if (!isset($_SESSION["id"])) {
    header("location: ../../index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("location: ../../pages/dashboard.php");
    exit;
}

$container = $_POST["container"] ?? "";
$relativePath = trim($_POST["path"] ?? "", "/");
$name = trim($_POST["name"] ?? "");

if ($container === "" || $name === "") {
    header("location: ../../pages/dashboard.php");
    exit;
}

$safeName = basename($name);
$cleanPath = str_replace(["..", "\\"], "", $relativePath);
$baseDir = "C:/xampp/htdocs/Dock-Hosting/users/Projects/" . $_SESSION["id"] . "/" . $container . "/";
$targetDir = $baseDir . ($cleanPath !== "" ? $cleanPath . "/" : "");
$newFolderPath = $targetDir . $safeName;

if (!is_dir($newFolderPath)) {
    mkdir($newFolderPath, 0777, true);
}

$redirectPath = "../../pages/file-manager.php?container=" . urlencode($container) .
                "&path=" . urlencode($cleanPath);

header("location: " . $redirectPath);
exit;
