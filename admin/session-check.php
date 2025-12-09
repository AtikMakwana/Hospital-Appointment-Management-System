<?php
// session-check.php

// Start session only if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Set session timeout duration (in seconds)
$timeout_duration = 1200; // 20 minutes

// Check if admin is logged in
if (!isset($_SESSION['admin'])) {
    header("Location: admin-login.php");
    exit();
}

// Check last activity
if (isset($_SESSION['last_activity'])) {
    $elapsed_time = time() - $_SESSION['last_activity'];
    if ($elapsed_time > $timeout_duration) {
        session_unset();
        session_destroy();
        header("Location: timeout.php");
        exit();
    }
}

// Update last activity timestamp
$_SESSION['last_activity'] = time();
?>
