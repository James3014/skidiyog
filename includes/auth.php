<?php
// Admin Authentication Middleware
// Include this file at the top of every admin page

// Prevent search engines from indexing admin pages
header('X-Robots-Tag: noindex, nofollow', true);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    // Store current page for redirect after login
    $current_page = basename($_SERVER['PHP_SELF']);
    header('Location: login.php?redirect=' . urlencode($current_page));
    exit();
}

// Optional: Check session timeout (30 minutes)
$session_timeout = 1800; // 30 minutes in seconds
if (isset($_SESSION['login_time']) && (time() - $_SESSION['login_time']) > $session_timeout) {
    // Session expired
    session_unset();
    session_destroy();
    header('Location: login.php?error=timeout');
    exit();
}

// Update last activity time
$_SESSION['login_time'] = time();

// Handle logout
if (isset($_GET['act']) && $_GET['act'] === 'logout') {
    session_unset();
    session_destroy();
    header('Location: login.php');
    exit();
}