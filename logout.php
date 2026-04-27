<?php
session_start();

// Capture role before destroying session
$role = $_SESSION['role'] ?? null;

// Destroy the session first
session_unset();
session_destroy();

// Then redirect
if ($role === 'admin') {
    header("Location: login.php");
} else {
    header("Location: login.php");
}
exit();
