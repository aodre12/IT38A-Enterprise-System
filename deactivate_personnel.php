<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: admin_login.php');
    exit;
}

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: admin_dashboard.php");
    exit;
}

$stmt = $conn->prepare("UPDATE personnel SET status='Inactive' WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: admin_dashboard.php");
exit;
