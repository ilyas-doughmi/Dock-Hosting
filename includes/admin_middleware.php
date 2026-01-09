<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function checkAdmin() {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        header("Location: /pages/dashboard.php?msg=" . urlencode("Access Denied: Admin privileges required."));
        exit;
    }
}

function checkLoggedIn() {
    if (!isset($_SESSION['id'])) {
        header("Location: /login.php");
        exit;
    }
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}
