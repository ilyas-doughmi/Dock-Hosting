<?php
session_start();

if (!isset($_SESSION["id"]) || $_SERVER["REQUEST_METHOD"] != "POST") {
    header("location: ../../index.php");
    exit;
}

require_once '../../Classes/GitHubManager.php';

$gh = new GitHubManager();
$gh->disconnect($_SESSION['id']);

header("Location: ../../pages/settings.php?msg=GitHub account disconnected successfully");
exit;
