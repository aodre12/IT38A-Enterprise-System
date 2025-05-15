<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo "<h2 style='color:red; text-align:center; margin-top:50px;'>Access Denied. Admin Only.</h2>";
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
