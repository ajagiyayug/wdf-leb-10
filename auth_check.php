<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Unauthorized Access Blocked
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?error=unauthorized");
    exit();
}

// 2. Session Timeout Implementation (60 seconds inactivity timeout for testing)
$timeout_duration = 60; 

if (isset($_SESSION['last_activity'])) {
    $elapsed_time = time() - $_SESSION['last_activity'];
    if ($elapsed_time > $timeout_duration) {
        // Destroy session on timeout
        session_unset();
        session_destroy();
        
        // Remove remember_me cookie on timeout
        if (isset($_COOKIE['remember_me'])) {
            setcookie("remember_me", "", time() - 3600, "/");
        }

        header("Location: login.php?msg=timeout");
        exit();
    }
}

// Update last activity timestamp for current session
$_SESSION['last_activity'] = time();
?>