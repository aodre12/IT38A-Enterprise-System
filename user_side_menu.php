<?php
// user_side_menu.php
session_start();
require 'config.php';  // ensure DB/ session configured

// Redirect admins away
if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    header('Location: admin_dashboard.php');
    exit;
}

// Require login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$username = $_SESSION['username'];
?>

<style>
  .sidebar {
    width: 220px;
    background-color: #fef6ec;
    padding: 25px 20px;
    border-radius: 15px;
    box-shadow: 0 0 12px rgba(0,0,0,0.1);
    height: 100vh;
    position: fixed;
    top: 0; left: 0;
    overflow-y: auto;
    font-family: Arial, sans-serif;
  }
  .sidebar h3 {
    color: #007bff;
    margin-bottom: 30px;
    text-align: center;
    font-weight: 700;
    font-size: 1.4em;
  }
  .sidebar ul {
    list-style: none; padding: 0; margin: 0;
  }
  .sidebar li {
    margin-bottom: 18px;
  }
  .sidebar a {
    color: #2c3e50;
    text-decoration: none;
    font-weight: 600;
    display: block;
    padding: 12px 18px;
    border-radius: 10px;
    transition: background 0.3s, color 0.3s;
    box-shadow: 0 0 5px rgba(0,123,255,0.2);
  }
  .sidebar a:hover {
    background-color: #007bff;
    color: white;
    box-shadow: 0 0 10px rgba(0,123,255,0.6);
  }
</style>

<div class="sidebar">
  <h3>Welcome, <?= htmlspecialchars($username) ?></h3>
  <ul>
    <li><a href="user_dashboard.php">Dashboard</a></li>
    <li><a href="work_orders.php">My Work Orders</a></li>
    <li><a href="tasks.php">My Tasks</a></li>
    <li><a href="profile.php">My Profile</a></li>
    <li><a href="reset_password.php">Reset Password</a></li>
    <li><a href="logout.php">Logout</a></li>
  </ul>
</div>
