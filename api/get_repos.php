<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

require_once '../Classes/GitHubManager.php';

$github = new GitHubManager();
$repos = $github->getAllRepos($_SESSION['id']);

echo json_encode($repos);
?>
