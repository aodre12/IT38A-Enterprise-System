<?php
// Admin login now redirects to unified login.php
// Kept for backward compatibility with any bookmarks.
session_start();
if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin' && isset($_SESSION['user_id'])) {
    header('Location: admin/dashboard.php');
    exit;
}
header('Location: login.php');
exit;
