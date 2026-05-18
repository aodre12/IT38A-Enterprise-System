<?php
session_start();
require '../Config.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') { header('Location: ../login.php'); exit; }
$id = (int)($_GET['id'] ?? 0);
if (!$id) { header('Location: dashboard.php'); exit; }
$s = $conn->prepare("UPDATE personnel SET status='Inactive' WHERE id=?");
$s->bind_param("i", $id);
$s->execute();
header('Location: dashboard.php');
exit;
