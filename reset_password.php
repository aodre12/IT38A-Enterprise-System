<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo "<h2 style='color:red; text-align:center; margin-top:50px;'>Access Denied. Admin Only.</h2>";
    exit;
}

$username = $_SESSION['username'] ?? 'AdminDev';

$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($user_id) || empty($new_password) || empty($confirm_password)) {
        $error = "All fields are required.";
    } elseif ($new_password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        // Hash the new password securely
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        // Update password in DB
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $hashed_password, $user_id);

        if ($stmt->execute()) {
            $success = "Password reset successfully.";
        } else {
            $error = "Failed to reset password. Please try again.";
        }
    }
}

// Fetch users for dropdown
$users = $conn->query("SELECT id, username FROM users")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Reset Password - Utility ERP</title>
    <style>
        /* Same styles as dashboard */
        body { font-family: Arial, sans-serif; background: #f5f6fa; margin: 0; }
        header { background: #2c3e50; color: white; padding: 20px; text-align: center; }
        .container { padding: 20px; max-width: 600px; margin: 30px auto; background: #fff; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        h1, h2 { margin-top: 0; }
        label { display: block; margin: 15px 0 5px; }
        input[type=password], select {
            width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; font-size: 1em;
            box-sizing: border-box;
        }
        button {
            background: #2ecc71; color: white; padding: 12px 20px; border: none; border-radius: 4px;
            cursor: pointer; font-size: 1em;
            margin-top: 20px;
        }
        button:hover { background: #27ae60; }
        .error { color: #e74c3c; margin-top: 10px; }
        .success { color: #27ae60; margin-top: 10px; }
        a.back-link {
            display: inline-block; margin-top: 15px; color: #2980b9; text-decoration: none;
        }
        a.back-link:hover { text-decoration: underline; }
    </style>
</head>
<body>
<header>
    <h1>Utility ERP - Admin Dashboard</h1>
    <p>Welcome, <?= htmlspecialchars($username) ?></p>
</header>

<div class="container">
    <h2>Reset Password</h2>

    <?php if ($error): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php elseif ($success): ?>
        <p class="success"><?= htmlspecialchars($success) ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <label for="user_id">Select User:</label>
        <select id="user_id" name="user_id" required>
            <option value="">-- Select User --</option>
            <?php foreach ($users as $user): ?>
                <option value="<?= $user['id'] ?>"><?= htmlspecialchars($user['username']) ?></option>
            <?php endforeach; ?>
        </select>

        <label for="new_password">New Password:</label>
        <input type="password" id="new_password" name="new_password" required />

        <label for="confirm_password">Confirm Password:</label>
        <input type="password" id="confirm_password" name="confirm_password" required />

        <button type="submit">Reset Password</button>
    </form>

    <a class="back-link" href="admin_dashboard.php">&larr; Back to Dashboard</a>
</div>
</body>
</html>
