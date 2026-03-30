<?php
// Start session
session_start();

// Capture role before destroying session
$role = $_SESSION['role'] ?? null;

// Redirect to login page after logout
if ($role === 'admin') {
    header("Location: admin_login.php");
} else {
    header("Location: login.php");
}

// Destroy the session
session_unset();  // Unset all session variables
session_destroy();  // Destroy the session
exit();