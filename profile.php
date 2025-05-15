<?php
session_start();
require 'config.php';

// Redirect admins
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
$error = '';
$success = '';

// Update profile
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = trim($_POST['name'] ?? '');
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

// Fetch user data
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
  <title>My Profile | UtilityTrack</title>
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background: #fef6ec;
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
    }
    .profile-container {
      position: relative;
      background: #fff;
      width: 400px;
      padding: 40px 30px;
      border-radius: 20px;
      box-shadow: 0 0 12px rgba(0,0,0,0.1);
      text-align: center;
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
      margin-top: 100px;
      font-size: 26px;
      color: #001F54;
    }
    form {
      margin-top: 20px;
      text-align: left;
    }
    label {
      display: block;
      margin-top: 15px;
      font-weight: bold;
      font-size: 14px;
      color: #333;
    }
    input[type="text"], input[type="email"] {
      width: 100%;
      padding: 12px;
      margin-top: 5px;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 15px;
    }
    button {
      width: 100%;
      padding: 12px;
      background: #0056b3;
      color: white;
      border: none;
      border-radius: 8px;
      font-size: 16px;
      margin-top: 20px;
      cursor: pointer;
    }
    button:hover {
      background: #003d80;
    }
    .info {
      margin-top: 20px;
      text-align: left;
      font-size: 14px;
    }
    .info p {
      margin: 5px 0;
      color: #555;
    }
    .message {
      margin-top: 15px;
      font-size: 14px;
    }
    .error { color: red; }
    .success { color: green; }
    .back-link {
      display: block;
      margin-top: 25px;
      font-size: 14px;
      color: #0056b3;
      text-decoration: none;
      font-weight: bold;
    }
    .back-link:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>

  <div class="profile-container">
    <div class="header-wave"></div>
    <h2>My Profile</h2>

    <?php if ($error): ?>
      <div class="message error"><?= htmlspecialchars($error) ?></div>
    <?php elseif ($success): ?>
      <div class="message success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="POST">
      <label for="name">Name</label>
      <input type="text" id="name" name="name" value="<?= htmlspecialchars($name) ?>" required>

      <label for="email">Email</label>
      <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" required>

      <div class="info">
        <p><strong>Username:</strong> <?= htmlspecialchars($username) ?></p>
        <p><strong>Role:</strong> <?= htmlspecialchars($role) ?></p>
        <p><strong>Status:</strong> <?= htmlspecialchars($status) ?></p>
      </div>

      <button type="submit">Update Profile</button>
    </form>

    <a class="back-link" href="user_dashboard.php">← Back to Dashboard</a>
  </div>

</body>
</html>
