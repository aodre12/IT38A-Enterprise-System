<?php
session_start();
require_once 'config.php';

// DEV MODE ONLY – REMOVE FOR PRODUCTION
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1;
    $_SESSION['username'] = 'AdminDev';
    $_SESSION['role'] = 'admin';
}

// Access Control: Admin Only
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "<h2 style='color:red; text-align:center; margin-top:50px;'>Access Denied. Admin Only.</h2>";
    exit;
}

$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $personnel_id = $_POST['personnel_id'] ?? '';
    $new_password = $_POST['new_password'] ?? '';

    if (empty($personnel_id) || empty($new_password)) {
        $error = "All fields are required.";
    } else {
        $hashedPassword = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE personnel SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $hashedPassword, $personnel_id);

        if ($stmt->execute()) {
            $success = "Password reset successfully.";
        } else {
            $error = "Error resetting password.";
        }
        $stmt->close();
    }
}

// Fetch personnel list
$personnel = $conn->query("SELECT id, name FROM personnel ORDER BY name ASC")->fetch_all(MYSQLI_ASSOC);
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Reset Password</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #fef6ec;
      margin: 0;
      padding: 40px;
    }

    .container {
      max-width: 500px;
      margin: auto;
      background-color: #fff;
      padding: 30px;
      border-radius: 15px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }

    h2 {
      text-align: center;
      margin-bottom: 20px;
    }

    label {
      display: block;
      margin-bottom: 8px;
      font-weight: bold;
    }

    select, input[type="password"] {
      width: 100%;
      padding: 10px;
      margin-bottom: 15px;
      border: 1px solid #ccc;
      border-radius: 5px;
    }

    button {
      background-color: #007bff;
      color: white;
      border: none;
      padding: 12px 20px;
      border-radius: 5px;
      cursor: pointer;
      width: 100%;
    }

    button:hover {
      background-color: #0056b3;
    }

    .success { color: green; text-align: center; margin-bottom: 15px; }
    .error { color: red; text-align: center; margin-bottom: 15px; }

    .back-link {
      display: block;
      text-align: center;
      margin-top: 20px;
      text-decoration: none;
      color: #007bff;
    }

    .back-link:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Reset Personnel Password</h2>

    <?php if ($error): ?><div class="error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <?php if ($success): ?><div class="success"><?= htmlspecialchars($success) ?></div><?php endif; ?>

    <form method="POST" action="">
      <label for="personnel_id">Select Personnel</label>
      <select name="personnel_id" id="personnel_id" required>
        <option value="">-- Choose Personnel --</option>
        <?php foreach ($personnel as $p): ?>
          <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['name']) ?></option>
        <?php endforeach; ?>
      </select>

      <label for="new_password">New Password</label>
      <input type="password" name="new_password" id="new_password" required>

      <button type="submit">Reset Password</button>
    </form>

    <a class="back-link" href="dashboard.php">← Back to Dashboard</a>
  </div>
</body>
</html>
