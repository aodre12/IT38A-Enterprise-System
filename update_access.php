<?php
session_start();
require 'config.php';

// Only allow admin users
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: admin_login.php');
    exit;
}

$success = '';
$error = '';

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_POST['user_id'] ?? '';
    $newRole = $_POST['role'] ?? '';
    $newStatus = $_POST['status'] ?? '';

    if ($userId && $newRole && $newStatus) {
        $stmt = $conn->prepare("UPDATE personnel SET role = ?, status = ? WHERE id = ?");
        $stmt->bind_param("ssi", $newRole, $newStatus, $userId);
        if ($stmt->execute()) {
            $success = "Access updated successfully.";
        } else {
            $error = "Failed to update access.";
        }
        $stmt->close();
    } else {
        $error = "Please fill all fields.";
    }
}

// Fetch all users
$users = [];
$result = $conn->query("SELECT id, name, email, role, status FROM personnel");
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}
$result->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Update Access | UtilityTrack</title>
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background: #fef6ec;
      padding: 50px 20px;
    }
    .container {
      max-width: 800px;
      margin: auto;
      background: white;
      padding: 30px;
      border-radius: 20px;
      box-shadow: 0 0 12px rgba(0, 0, 0, 0.1);
      position: relative;
    }
    .header-wave {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100px;
      background: #001F54;
      border-top-left-radius: 20px;
      border-top-right-radius: 20px;
      clip-path: ellipse(100% 80% at 50% 0%);
    }
    h2 {
      text-align: center;
      margin-top: 100px;
      color: #001F54;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 30px;
    }
    th, td {
      padding: 12px;
      border-bottom: 1px solid #ddd;
      text-align: left;
    }
    th {
      background: #001F54;
      color: white;
    }
    select, button {
      padding: 8px;
      font-size: 14px;
      border-radius: 6px;
      border: 1px solid #ccc;
    }
    button {
      background: #0056b3;
      color: white;
      cursor: pointer;
    }
    button:hover {
      background: #003d80;
    }
    .message {
      text-align: center;
      font-size: 14px;
      margin-top: 15px;
    }
    .success { color: green; }
    .error { color: red; }
    .back-link {
      display: block;
      text-align: center;
      margin-top: 25px;
      font-size: 14px;
      color: #0056b3;
      font-weight: bold;
      text-decoration: none;
    }
    .back-link:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>

<div class="container">
  <div class="header-wave"></div>
  <h2>Update User Access</h2>

  <?php if ($success): ?>
    <div class="message success"><?= htmlspecialchars($success) ?></div>
  <?php elseif ($error): ?>
    <div class="message error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form method="POST">
    <table>
      <thead>
        <tr>
          <th>Name</th>
          <th>Email</th>
          <th>Role</th>
          <th>Status</th>
          <th>Change</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($users as $user): ?>
          <tr>
            <td><?= htmlspecialchars($user['name']) ?></td>
            <td><?= htmlspecialchars($user['email']) ?></td>
            <td>
              <select name="role">
                <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>User</option>
                <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
              </select>
            </td>
            <td>
              <select name="status">
                <option value="active" <?= $user['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                <option value="inactive" <?= $user['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
              </select>
            </td>
            <td>
              <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
              <button type="submit">Update</button>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </form>

  <a class="back-link" href="admin_dashboard.php">← Back to Dashboard</a>
</div>

</body>
</html>
