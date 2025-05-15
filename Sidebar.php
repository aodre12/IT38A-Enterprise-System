<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$username = $_SESSION['username'] ?? 'Guest';
$role = $_SESSION['role'] ?? 'user';
?>

<style>
  body {
    margin: 0;
    font-family: Arial, sans-serif;
  }

  .sidebar {
    width: 220px;
    background-color: #2c3e50;
    position: fixed;
    height: 100vh;
    padding-top: 30px;
    box-shadow: 2px 0 5px rgba(0,0,0,0.1);
    z-index: 10;
  }

  .sidebar h2 {
    text-align: center;
    color: #ecf0f1;
    margin-bottom: 30px;
    font-size: 22px;
  }

  .sidebar ul {
    list-style: none;
    padding: 0;
    margin: 0;
  }

  .sidebar ul li {
    margin-bottom: 10px;
  }

  .sidebar ul li a {
    display: block;
    padding: 12px 20px;
    color: #ecf0f1;
    text-decoration: none;
    border-radius: 8px;
    margin: 0 10px;
    transition: background 0.3s;
  }

  .sidebar ul li a:hover {
    background-color: #34495e;
  }

  .sidebar .footer {
    position: absolute;
    bottom: 20px;
    width: 100%;
    text-align: center;
    font-size: 13px;
    color: #95a5a6;
  }

  .main-content {
    margin-left: 220px;
    padding: 30px;
    background-color: #f5f6fa;
    min-height: 100vh;
  }
</style>

<div class="sidebar">
  <h2>UtilityTrack ERP</h2>
  <ul>
    <li><a href="user_dashboard.php">🏠 Dashboard</a></li>
    <li><a href="personnel.php">👥 Personnel</a></li>
    <li><a href="tasks.php">📋 Tasks</a></li>
    <li><a href="work_orders.php">🛠️ Work Orders</a></li>
    <li><a href="create_work.php">➕ Create Work</a></li>
    <li><a href="reset_password.php">🔒 Reset Password</a></li>
    <?php if ($role === 'admin'): ?>
    <?php endif; ?>
    <li><a href="logout.php">🚪 Logout</a></li>
  </ul>
  <div class="footer">
    Logged in as: <?= htmlspecialchars($username) ?>
  </div>
</div>
