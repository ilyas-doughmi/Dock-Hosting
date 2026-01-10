<?php
header('Content-Type: application/json');
session_start(); 

require_once("../Classes/Project.php");

$token = $_POST['token'] ?? '';
$projectName = $_POST['project_name'] ?? '';

if (empty($token) || empty($projectName)) {
    http_response_code(400);
    echo json_encode(['message' => 'Missing token or project name.']);
    exit;
}

$decoded = base64_decode($token);
$parts = explode('_SECRET_', $decoded);
$userId = $parts[0] ?? null;

if (!$userId) {
    http_response_code(401);
    echo json_encode(['message' => 'Invalid authentication token.']);
    exit;
}

$_SESSION['id'] = $userId;

$projectObj = new Project(); 

$userProjects = $projectObj->getProjects($userId);
$targetProject = null;

foreach ($userProjects as $p) {
    if ($p['project_name'] === $projectName) {
        $targetProject = $p;
        break;
    }
}

if (!$targetProject) {
    http_response_code(404);
    echo json_encode(['message' => "Project '$projectName' not found. Please create it in the dashboard first."]);
    exit;
}

$containerName = $targetProject['container_name'];
$projectType = $targetProject['type'] ?? 'php';

if (!isset($_FILES['project_zip']) || $_FILES['project_zip']['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(['message' => 'File upload failed.']);
    exit;
}

$baseDir = dirname(__DIR__) . "/users/Projects/$userId/$containerName/";

if (!is_dir($baseDir)) {
    mkdir($baseDir, 0777, true);
}

$zip = new ZipArchive;
if ($zip->open($_FILES['project_zip']['tmp_name']) === TRUE) {
    $zip->extractTo($baseDir);
    $zip->close();
} else {
    http_response_code(500);
    echo json_encode(['message' => 'Failed to extract project zip.']);
    exit;
}

$warning = null;
if ($projectType === 'node' && !file_exists($baseDir . 'package.json')) {
    $warning = "Warning: Deployed to Node.js container but 'package.json' is missing.";
} elseif ($projectType === 'python' && !file_exists($baseDir . 'app.py') && !file_exists($baseDir . 'requirements.txt')) {
    $warning = "Warning: Deployed to Python container but 'app.py' or 'requirements.txt' is missing.";
} elseif ($projectType === 'php' && !file_exists($baseDir . 'index.php')) {
    $warning = "Warning: Deployed to PHP container but 'index.php' is missing.";
}

$restartResult = $projectObj->restartContainer($containerName);

$response = [
    'status' => $restartResult ? 'success' : 'warning',
    'message' => $restartResult 
        ? "Successfully deployed to $projectType container '$projectName'!" 
        : "Files deployed, but container restart had issues.",
    'url' => "http://" . $projectName . ".dockhosting.dev",
    'type' => $projectType
];

if ($warning) {
    $response['validation_warning'] = $warning;
}

echo json_encode($response);
?>