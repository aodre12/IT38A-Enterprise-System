<?php
session_start();
require 'config.php'; // your DB connection

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username === '' || $password === '') {
        $error = 'Please enter both username and password.';
    } else {
        $stmt = $conn->prepare("SELECT id, password, role FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $stmt->bind_result($userId, $hash, $role);
            $stmt->fetch();
            if (password_verify($password, $hash)) {
                $_SESSION['user_id']  = $userId;
                $_SESSION['username'] = $username;
                $_SESSION['role']     = $role;

                // Admin must login via dedicated admin flow.
                if ($role === 'admin') {
                    // Clear any session created by this login attempt.
                    session_unset();
                    header('Location: admin_login.php');
                    exit;
                }

                header('Location: user_dashboard.php');
                exit;
            }
        }
        $error = 'Invalid username or password.';
        $stmt->close();
    }
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login | UtilityTrack</title>
  <style>
    body { margin:0; font-family:'Segoe UI',sans-serif; background:#fef6ec; display:flex; align-items:center; justify-content:center; height:100vh; }
    .login-container { position:relative; background:#fff; width:350px; padding:40px 30px; border-radius:20px; box-shadow:0 0 12px rgba(0,0,0,0.1); text-align:center; }
    .header-wave { position:absolute; top:0; left:0; width:100%; height:100px; background:#001F54; border-top-left-radius:20px; border-top-right-radius:20px; clip-path:ellipse(100% 80% at 50% 0%); }
    h2 { margin-top:100px; font-size:28px; color:#000; }
    input { width:100%; padding:12px; margin:12px 0; border:1px solid #ccc; border-radius:8px; font-size:16px; box-sizing:border-box; }
    button { width:100%; padding:12px; background:#0056b3; color:#fff; border:none; border-radius:8px; font-size:16px; cursor:pointer; }
    button:hover { background:#003d80; }
    .error { color:red; margin-top:10px; font-size:14px; }
    .signup-prompt { margin-top:15px; font-size:14px; }
    .signup-prompt a { color:#0056b3; text-decoration:none; font-weight:bold; }
    .signup-prompt a:hover { text-decoration:underline; }
  </style>
</head>
<body>
  <div class="login-container">
    <div class="header-wave"></div>
    <h2>LOGIN</h2>
    <?php if ($error): ?><div class="error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <form method="POST" action="">
      <input type="text" name="username" placeholder="Username" required autofocus>
      <input type="password" name="password" placeholder="Password" required>
      <button type="submit">Login</button>
    </form>
    <div class="signup-prompt">
      Don’t have an account? <a href="register.php">Sign up</a>
    </div>
  </div>
</body>
</html>
