<?php

header('Content-Type: application/json');
session_start();
require_once __DIR__ . '/../vendor/autoload.php';
use Dotenv\Dotenv;
require_once("../Classes/Project.php");
require_once("../php/connect.php");

$dotenv = Dotenv::createImmutable(__DIR__ . "/../");
$dotenv->load();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'message' => 'Method Not Allowed. Expected POST, got ' . $_SERVER['REQUEST_METHOD']
    ]);
    exit;
}

if (empty($_POST) && empty($_FILES) && isset($_SERVER['CONTENT_LENGTH']) && $_SERVER['CONTENT_LENGTH'] > 0) {
    $maxSize = ini_get('post_max_size');
    http_response_code(413);
    echo json_encode([
        'message' => "Upload too large. Server post_max_size is $maxSize."
    ]);
    exit;
}

$token = $_POST['token'] ?? '';
$incomingProjectId = $_POST['project_id'] ?? null;
$incomingProjectName = $_POST['project_name'] ?? '';

if (empty($token)) {
    http_response_code(400);
    echo json_encode(['message' => "Missing token."]);
    exit;
}

if (!isset($_FILES['project_zip']) || $_FILES['project_zip']['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    $err = $_FILES['project_zip']['error'] ?? 'No file';
    echo json_encode(['message' => "File upload failed (Error Code: $err)."]);
    exit;
}

$detectedType = 'php';
$zipScanner = new ZipArchive;
if ($zipScanner->open($_FILES['project_zip']['tmp_name']) === TRUE) {
    if ($zipScanner->locateName('package.json') !== false) {
        $detectedType = 'node';
    } elseif ($zipScanner->locateName('app.py') !== false || $zipScanner->locateName('requirements.txt') !== false) {
        $detectedType = 'python';
    }
    $zipScanner->close();
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
$targetProject = null;

if ($incomingProjectId) {
    $userProjects = $projectObj->getProjects($userId);
    foreach ($userProjects as $p) {
        if ($p['project_id'] == $incomingProjectId) {
            $targetProject = $p;
            break;
        }
    }
    
    if (!$targetProject) {
        http_response_code(404);
        echo json_encode(['message' => 'Linked project not found or access denied. Try deleting .dock folder to unlink.']);
        exit;
    }
} else {
    if (empty($incomingProjectName)) {
        http_response_code(400);
        echo json_encode(['message' => "Project name is required for new deployments."]);
        exit;
    }

    $userProjects = $projectObj->getProjects($userId);
    
    foreach ($userProjects as $p) {
        if ($p['project_name'] === $incomingProjectName) {
            $targetProject = $p;
            break;
        }
    }

    if (!$targetProject) {
        $newSuffix = substr(bin2hex(random_bytes(3)), 0, 6);
        $finalName = $incomingProjectName . '-' . $newSuffix;
        
        $lastPort = $projectObj->trackPort();
        $port = $lastPort ? $lastPort + 1 : 8000;
        
        if (isset($_ENV['HOST_BASE_PATH']) && !empty($_ENV['HOST_BASE_PATH'])) {
            $host_base = $_ENV['HOST_BASE_PATH'];
        } elseif (getenv('HOST_BASE_PATH')) {
            $host_base = getenv('HOST_BASE_PATH');
        } else {
            $host_base = str_replace('\\', '/', dirname(__DIR__) . '/users');
        }
        
        $baseDir = $host_base . "/Projects/$userId/$finalName/";
        
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
        
        $htaccess_content = "ErrorDocument 500 \"Internal Server Error\"\n";
        $htaccess_content .= "php_flag display_errors off\n";
        $htaccess_content .= "php_flag log_errors on\n";
        $htaccess_content .= "php_value error_log /var/www/html/error.log\n";
        
        if (!file_exists($baseDir . ".htaccess")) {
            file_put_contents($baseDir . ".htaccess", $htaccess_content);
        }
        
        file_put_contents($baseDir . "error.log", "");
        chmod($baseDir . "error.log", 0666);
        
        $created = $projectObj->createProject($finalName, $port, $finalName, $userId, $detectedType);
        
        if ($created) {
            if (isset($_ENV['HOST_BASE_PATH']) && !empty($_ENV['HOST_BASE_PATH'])) {
                $host_base = $_ENV['HOST_BASE_PATH'];
            } elseif (getenv('HOST_BASE_PATH')) {
                $host_base = getenv('HOST_BASE_PATH');
            } else {
                $host_base = str_replace('\\', '/', dirname(__DIR__) . '/users');
            }
            
            $host_project_path = $host_base . "/Projects/" . $userId . "/" . $finalName;
            $subdomain = $finalName . ".dockhosting.dev";
            
            $safe_port = escapeshellarg($port);
            $safe_name = escapeshellarg($finalName);
            $safe_subdomain = escapeshellarg($subdomain);
            $safe_volume = escapeshellarg($host_project_path . ":/var/www/html");
            
            if ($detectedType === 'node') {
                $internal_port = "3000";
                $image = "node:18-alpine";
                $command = "sh -c \"apk add --no-cache bash && cd /var/www/html && if [ -f package.json ]; then npm install; fi && if [ -f index.js ]; then node --watch index.js; else npm start; fi\"";
            } elseif ($detectedType === 'python') {
                $internal_port = "5000";
                $image = "python:3.9-slim";
                $command = "sh -c \"cd /var/www/html && if [ -f requirements.txt ]; then pip install -r requirements.txt; fi && if [ -f app.py ]; then python app.py; fi\"";
            } else {
                $internal_port = "80";
                $image = "dock-hosting-user";
                $command = "";
            }
            
            $safe_internal_port = escapeshellarg($internal_port);
            
            $dockerCmd = "docker run -d -p {$safe_port}:{$internal_port} --name {$safe_name} --network proxy_network -e VIRTUAL_HOST={$safe_subdomain} -e LETSENCRYPT_HOST={$safe_subdomain} -e VIRTUAL_PORT={$internal_port} -e PORT={$internal_port} -v {$safe_volume}";
            $dockerCmd .= " -e DB_HOST=dock-hosting-db -e DB_USER=root -e DB_PASSWORD=" . escapeshellarg(getenv('DB_PASSWORD'));
            $dockerCmd .= " {$image} {$command}";
            
            shell_exec($dockerCmd);
            
            $userProjects = $projectObj->getProjects($userId);
            foreach ($userProjects as $p) {
                if ($p['project_name'] === $finalName) {
                    $targetProject = $p;
                    break;
                }
            }
        }
    }
}

if (!$targetProject) {
    http_response_code(500);
    echo json_encode(['message' => "Could not find or create project."]);
    exit;
}

$containerName = $targetProject['container_name'];
$realProjectName = $targetProject['project_name'];
$projectType = $targetProject['type'] ?? $detectedType;

if ($incomingProjectId) {
    if (isset($_ENV['HOST_BASE_PATH']) && !empty($_ENV['HOST_BASE_PATH'])) {
        $host_base = $_ENV['HOST_BASE_PATH'];
    } elseif (getenv('HOST_BASE_PATH')) {
        $host_base = getenv('HOST_BASE_PATH');
    } else {
        $host_base = str_replace('\\', '/', dirname(__DIR__) . '/users');
    }
    
    $baseDir = $host_base . "/Projects/$userId/$containerName/";
    
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
    
    $restartResult = $projectObj->restartContainer($containerName);
} else {
    $restartResult = true;
}

echo json_encode([
    'status' => 'success',
    'message' => 'Deployment successful!',
    'url' => "http://" . $realProjectName . ".dockhosting.dev",
    'project_id' => $targetProject['project_id'],
    'project_name' => $realProjectName,
    'type' => $projectType
]);
?>