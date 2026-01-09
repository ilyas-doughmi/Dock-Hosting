<?php
require_once __DIR__ . '/../Classes/Admin.php';

function checkMaintenance() {
    if (strpos($_SERVER['REQUEST_URI'], '/admin/') !== false || 
        strpos($_SERVER['REQUEST_URI'], 'login.php') !== false ||
        strpos($_SERVER['REQUEST_URI'], 'admin_actions.php') !== false) {
        return;
    }

    if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
        return;
    }

    $admin = new Admin();
    return $admin->isMaintenanceMode();
}
?>
