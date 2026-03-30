<?php
session_start();
require 'config.php';

// Only admin access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: admin_login.php');
    exit;
}

$success = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $system_name     = trim($_POST['system_name'] ?? '');
    $contact_email   = trim($_POST['contact_email'] ?? '');
    $maintenance_mode = isset($_POST['maintenance_mode']) ? 1 : 0;

    if ($system_name === '' || $contact_email === '') {
        $error = "All fields are required.";
    } elseif (!filter_var($contact_email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        $stmt = $conn->prepare("UPDATE system_config SET system_name = ?, contact_email = ?, maintenance_mode = ?");
        $stmt->bind_param("ssi", $system_name, $contact_email, $maintenance_mode);
        if ($stmt->execute()) {
            $success = "System settings updated successfully.";
        } else {
            $error = "Failed to update settings.";
        }
        $stmt->close();
    }
}

// Fetch current config
$config = [
    'system_name' => '',
    'contact_email' => '',
    'maintenance_mode' => 0
];

$result = $conn->query("SELECT system_name, contact_email, maintenance_mode FROM system_config LIMIT 1");
if ($result && $row = $result->fetch_assoc()) {
    $config = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Configure System | UtilityTrack</title>
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
    .container {
      background: #fff;
      width: 500px;
      padding: 40px 30px;
      border-radius: 20px;
      box-shadow: 0 0 12px rgba(0,0,0,0.1);
      position: relative;
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
    input[type="text"], input[type="email"] {
      width: 100%;
      padding: 12px;
      margin: 12px 0;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 16px;
    }
    label {
      font-weight: bold;
      display: block;
      text-align: left;
      margin-top: 10px;
    }
    .checkbox {
      text-align: left;
      margin: 12px 0;
    }
    button {
      width: 100%;
      padding: 12px;
      background: #0056b3;
      color: white;
      border: none;
      border-radius: 8px;
      font-size: 16px;
      cursor: pointer;
    }
    button:hover {
      background: #003d80;
    }
    .message {
      margin-top: 15px;
      font-size: 14px;
    }
    .error { color: red; }
    .success { color: green; }
    .back-link {
      display: block;
      margin-top: 20px;
      text-decoration: none;
      color: #0056b3;
      font-weight: bold;
    }
    .back-link:hover { text-decoration: underline; }
  </style>
</head>
<body>
  <div class="container">
    <div class="header-wave"></div>
    <h2>Configure System</h2>

    <?php if ($error): ?><div class="message error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <?php if ($success): ?><div class="message success"><?= htmlspecialchars($success) ?></div><?php endif; ?>

    <form method="POST">
      <label for="system_name">System Name</label>
      <input type="text" id="system_name" name="system_name" value="<?= htmlspecialchars($config['system_name']) ?>" required>

      <label for="contact_email">Contact Email</label>
      <input type="email" id="contact_email" name="contact_email" value="<?= htmlspecialchars($config['contact_email']) ?>" required>

      <div class="checkbox">
        <label>
          <input type="checkbox" name="maintenance_mode" <?= $config['maintenance_mode'] ? 'checked' : '' ?>>
          Enable Maintenance Mode
        </label>
      </div>

      <button type="submit">Save Settings</button>
    </form>

    <a class="back-link" href="admin_dashboard.php">← Back to Dashboard</a>
  </div>
</body>
</html>
