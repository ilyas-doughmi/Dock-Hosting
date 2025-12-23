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

if (!is_dir($targetDir)) {
    mkdir($targetDir, 0777, true);
}

$filePath = $targetDir . $safeName;
if (!file_exists($filePath)) {
    file_put_contents($filePath, "");
}

$fileParam = $cleanPath === "" ? $safeName : $cleanPath . "/" . $safeName;
$redirectPath = "../../pages/file-manager.php?container=" . urlencode($container) .
                "&path=" . urlencode($cleanPath) .
                "&file=" . urlencode($fileParam);

header("location: " . $redirectPath);
exit;
