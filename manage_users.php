<?php
session_start();
require 'config.php';

// Only allow admins
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// Fetch all users
$sql = "SELECT id, username, role FROM users ORDER BY id ASC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Users | UtilityTrack</title>
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background: #fef6ec;
    }
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
    }
    .sidebar h3 {
      color: #007bff;
      margin-bottom: 30px;
      text-align: center;
      font-weight: 700;
      font-size: 1.4em;
    }
    .sidebar ul { list-style: none; padding: 0; margin: 0; }
    .sidebar li { margin-bottom: 18px; }
    .sidebar a {
      color: #2c3e50; text-decoration: none;
      font-weight: 600; display: block;
      padding: 12px 18px; border-radius: 10px;
      transition: background 0.3s, color 0.3s;
      box-shadow: 0 0 5px rgba(0,123,255,0.2);
    }
    .sidebar a:hover {
      background-color: #007bff; color: white;
      box-shadow: 0 0 10px rgba(0,123,255,0.6);
    }
    .main-content {
      margin-left: 260px;
      padding: 40px 30px;
    }
    .container {
      background: #fff;
      padding: 25px;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
      overflow-x: auto;
    }
    h2 { margin-top: 0; color: #007bff; }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }
    th, td {
      padding: 12px 15px;
      text-align: left;
      border-bottom: 1px solid #ddd;
    }
    th {
      background: #007bff;
      color: white;
    }
    tr:hover { background-color: #f1f1f1; }
    .actions a {
      margin-right: 10px;
      text-decoration: none;
      color: #0056b3;
      font-weight: bold;
    }
    .actions a:hover {
      text-decoration: underline;
    }
    .back-link {
      display: block;
      margin-top: 20px;
      color: #007bff;
      text-decoration: none;
    }
    .back-link:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>

<div class="sidebar">
  <h3>Admin Panel</h3>
  <ul>
    <li><a href="admin_dashboard.php">Dashboard</a></li>
    <li><a href="manage_users.php">Manage Users</a></li>
    <li><a href="configure_system.php">System Config</a></li>
    <li><a href="view_audit_logs.php">Audit Logs</a></li>
    <li><a href="logout.php">Logout</a></li>
  </ul>
</div>

<div class="main-content">
  <div class="container">
    <h2>Manage Users</h2>
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Username</th>
          <th>Role</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($result && $result->num_rows > 0): ?>
          <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
              <td><?= $row['id'] ?></td>
              <td><?= htmlspecialchars($row['username']) ?></td>
              <td><?= htmlspecialchars($row['role']) ?></td>
              <td class="actions">
                <a href="edit_user.php?id=<?= $row['id'] ?>">Edit</a>
                <a href="delete_user.php?id=<?= $row['id'] ?>" onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="4">No users found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>

    <a class="back-link" href="admin_dashboard.php">← Back to Dashboard</a>
  </div>
</div>

</body>
</html>
