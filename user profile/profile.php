<?php
session_start();
require 'config.php';  // Your DB connection

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

$user_id  = $_SESSION['user_id'];
$username = $_SESSION['username'];
$error    = '';
$success  = '';

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = trim($_POST['name']  ?? '');
    $email = trim($_POST['email'] ?? '');

    if ($name === '' || $email === '') {
        $error = "Name and Email cannot be empty.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        $stmt = $conn->prepare("UPDATE personnel SET name = ?, email = ? WHERE id = ?");
        $stmt->bind_param("ssi", $name, $email, $user_id);
        if ($stmt->execute()) {
            $success = "Profile updated successfully.";
        } else {
            $error = "Failed to update profile.";
        }
        $stmt->close();
    }
}

// Fetch current info
$stmt = $conn->prepare("SELECT name, email, role, status FROM personnel WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($name, $email, $role, $status);
$stmt->fetch();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>My Profile</title>
  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background: #fef6ec;
    }
    /* Sidebar */
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

    /* Main content */
    .main-content {
      margin-left: 260px; /* leave space for sidebar */
      padding: 40px 30px;
    }
    .container {
      max-width: 500px;
      background: #fff;
      padding: 25px;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
      margin-bottom: 40px;
    }
    h2 { margin-top: 0; color: #007bff; }
    label { display: block; margin-top: 15px; font-weight: bold; }
    input[type="text"], input[type="email"] {
      width: 100%; padding: 10px; margin-top: 5px;
      border: 1px solid #ccc; border-radius: 5px;
    }
    button {
      margin-top: 20px; background: #007bff; color: white;
      border: none; padding: 12px; border-radius: 6px;
      cursor: pointer; width: 100%;
    }
    button:hover { background: #0056b3; }
    .message { text-align: center; margin-top: 15px; }
    .error { color: red; }
    .success { color: green; }
    .info { margin-top: 15px; }
    a.back-link {
      display: block; text-align: center;
      margin-top: 20px; color: #007bff;
      text-decoration: none;
    }
    a.back-link:hover { text-decoration: underline; }
  </style>
</head>
<body>

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

  <div class="main-content">
    <div class="container">
      <h2>My Profile</h2>

      <?php if ($error): ?>
        <div class="message error"><?= htmlspecialchars($error) ?></div>
      <?php elseif ($success): ?>
        <div class="message success"><?= htmlspecialchars($success) ?></div>
      <?php endif; ?>

      <form method="POST" action="">
        <label for="name">Name</label>
        <input type="text" id="name" name="name" value="<?= htmlspecialchars($name) ?>" required>

        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" required>

        <div class="info">
          <p><strong>Role:</strong> <?= htmlspecialchars($role) ?></p>
          <p><strong>Status:</strong> <?= htmlspecialchars($status) ?></p>
        </div>

        <button type="submit">Update Profile</button>
      </form>
    </div>

    <a class="back-link" href="user_dashboard.php">← Back to Dashboard</a>
  </div>

</body>
</html>
