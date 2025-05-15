<?php
session_start();
require 'config.php';

// Only logged-in users
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user_id'];
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if (strlen($newPassword) < 6) {
        $error = "Password must be at least 6 characters.";
    } elseif ($newPassword !== $confirmPassword) {
        $error = "Passwords do not match.";
    } else {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $hashedPassword, $userId);
        if ($stmt->execute()) {
            $success = "Password updated successfully.";
        } else {
            $error = "Failed to update password.";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Reset Password</title>
  <style>
    body { margin:0; font-family:'Segoe UI',sans-serif; background:#fef6ec; display:flex; align-items:center; justify-content:center; height:100vh; }
    .container { position:relative; background:#fff; width:400px; padding:40px 30px; border-radius:20px; box-shadow:0 0 12px rgba(0,0,0,0.1); text-align:center; }
    .header-wave { position:absolute; top:0; left:0; width:100%; height:100px; background:#001F54; border-top-left-radius:20px; border-top-right-radius:20px; clip-path:ellipse(100% 80% at 50% 0%); }
    h2 { margin-top:100px; font-size:24px; color:#001F54; }
    input { width:100%; padding:12px; margin:12px 0; border:1px solid #ccc; border-radius:8px; font-size:16px; }
    button { width:100%; padding:12px; background:#0056b3; color:#fff; border:none; border-radius:8px; font-size:16px; cursor:pointer; }
    button:hover { background:#003d80; }
    .message { font-size:14px; margin-top:10px; }
    .error { color:red; }
    .success { color:green; }
    .back-link { margin-top:15px; display:block; text-decoration:none; color:#0056b3; font-weight:bold; }
    .back-link:hover { text-decoration:underline; }
  </style>
</head>
<body>
  <div class="container">
    <div class="header-wave"></div>
    <h2>Reset Password</h2>

    <?php if ($error): ?><div class="message error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <?php if ($success): ?><div class="message success"><?= htmlspecialchars($success) ?></div><?php endif; ?>

    <form method="POST">
      <input type="password" name="new_password" placeholder="New Password" required>
      <input type="password" name="confirm_password" placeholder="Confirm Password" required>
      <button type="submit">Update Password</button>
    </form>

    <a class="back-link" href="user_dashboard.php">← Back to Dashboard</a>
  </div>
</body>
</html>
