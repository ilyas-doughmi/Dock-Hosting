<?php
session_start();
require_once __DIR__ . '/admin_middleware.php';
checkAdmin();

require_once __DIR__ . '/../Classes/Admin.php';
require_once __DIR__ . '/../Classes/Project.php';
require_once __DIR__ . '/Logger.php';

$admin = new Admin();
$project = new Project();
$logger = new Logger();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'delete_user') {
        $userId = $_POST['user_id'];
        $result = $admin->deleteUser($userId);
        if ($result) {
            $logger->logActivity($_SESSION['id'], 'DELETE_USER', "Deleted user ID: $userId");
            header("Location: ../pages/admin/users.php?msg=" . urlencode("User deleted successfully"));
        } else {
            header("Location: ../pages/admin/users.php?msg=" . urlencode("Failed to delete user") . "&type=error");
        }
    }
    elseif ($action === 'toggle_container') {
        $containerName = $_POST['container_name'];
        $currentStatus = $_POST['current_status'];
        
        if (strtolower($currentStatus) === 'running') {
            $result = $project->adminStopContainer($containerName);
            $newStatus = 'Stopped';
        } else {
            $result = $project->adminStartContainer($containerName);
            $newStatus = 'Started';
        }

        if ($result) {
            $logger->logActivity($_SESSION['id'], 'TOGGLE_CONTAINER', "$newStatus container: $containerName");
            header("Location: ../pages/admin/projects.php?msg=" . urlencode("Container $newStatus"));
        } else {
            header("Location: ../pages/admin/projects.php?msg=" . urlencode("Failed to toggle container") . "&type=error");
        }
    }
    elseif ($action === 'delete_project') {
        $containerName = $_POST['container_name'];
        $userId = $_POST['user_id'];
        
        $result = $project->adminDeleteProject($containerName, $userId);
        
        if ($result) {
            $logger->logActivity($_SESSION['id'], 'DELETE_PROJECT', "Deleted project: $containerName");
            header("Location: ../pages/admin/projects.php?msg=" . urlencode("Project deleted successfully"));
        } else {
            header("Location: ../pages/admin/projects.php?msg=" . urlencode("Failed to delete project") . "&type=error");
        }
    }
    else {
        header("Location: ../pages/admin/dashboard.php");
    }
    exit;
}
