<?php
session_start();
require 'Config.php';

// Redirect if not logged in or is admin
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
if ($_SESSION['role'] === 'admin') {
    header('Location: ../admin/dashboard.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$error = $success = '';

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if ($name === '' || $email === '') {
        $error = "Name and Email cannot be empty.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        $stmt = $conn->prepare("UPDATE personnel SET name = ?, email = ? WHERE id = ?");
        $stmt->bind_param("ssi", $name, $email, $user_id);
        $stmt->execute() ? $success = "Profile updated successfully." : $error = "Update failed.";
        $stmt->close();
    }
}

// Fetch latest profile info
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
  <meta charset="UTF-8">
  <title>My Profile</title>
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background: #fef6ec;
    }
    .container {
      position: relative;
      max-width: 500px;
      margin: 60px auto;
      background: #fff;
      padding: 40px 30px;
      border-radius: 20px;
      box-shadow: 0 0 12px rgba(0, 0, 0, 0.1);
    }
    .header-wave {
      position: absolute;
      top: 0; left: 0;
      width: 100%; height: 100px;
      background: #001F54;
      border-top-left-radius: 20px;
      border-top-right-radius: 20px;
      clip-path: ellipse(100% 80% at 50% 0%);
    }
    h2 {
      margin-top: 100px;
      font-size: 28px;
      text-align: center;
      color: #003049;
    }
    label {
      font-weight: bold;
      display: block;
      margin-top: 15px;
    }
    input[type="text"], input[type="email"] {
      width: 100%;
      padding: 10px;
      margin-top: 5px;
      border: 1px solid #ccc;
      border-radius: 5px;
    }
    button {
      width: 100%;
      background: #007bff;
      color: white;
      border: none;
      padding: 12px;
      border-radius: 6px;
      margin-top: 20px;
      cursor: pointer;
    }
    button:hover {
      background: #0056b3;
    }
    .info p {
      margin: 8px 0;
      color: #555;
    }
    .message {
      text-align: center;
      margin-top: 10px;
      font-weight: bold;
    }
    .error { color: red; }
    .success { color: green; }
    a.back-link {
      display: block;
      text-align: center;
      margin-top: 20px;
      color: #0056b3;
      text-decoration: none;
      font-weight: bold;
    }
    a.back-link:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="header-wave"></div>
    <h2>My Profile</h2>

    <?php if ($error): ?>
      <div class="message error"><?= htmlspecialchars($error) ?></div>
    <?php elseif ($success): ?>
      <div class="message success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="POST">
      <label for="name">Name</label>
      <input type="text" name="name" id="name" value="<?= htmlspecialchars($name) ?>" required>

      <label for="email">Email</label>
      <input type="email" name="email" id="email" value="<?= htmlspecialchars($email) ?>" required>

      <div class="info">
        <p><strong>Role:</strong> <?= htmlspecialchars($role) ?></p>
        <p><strong>Status:</strong> <?= htmlspecialchars($status) ?></p>
      </div>

      <button type="submit">Update Profile</button>
    </form>

    <a class="back-link" href="../user/dashboard.php">← Back to Dashboard</a>
  </div>
</body>
</html>
