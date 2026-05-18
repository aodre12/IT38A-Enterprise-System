<?php
session_start();
require 'Config.php';
require 'csrf.php';

// Security headers
header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');

// Already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: ' . ($_SESSION['role'] === 'admin' ? 'admin/dashboard.php' : 'user/dashboard.php'));
    exit;
}

$error = '';

// Show success message from registration
$success = '';
if (!empty($_SESSION['success_message'])) {
    $success = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify()) {
        $error = 'Invalid request. Please try again.';
    } else {
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
                    // Regenerate session ID to prevent fixation
                    session_regenerate_id(true);

                    $_SESSION['user_id']  = $userId;
                    $_SESSION['username'] = $username;
                    $_SESSION['role']     = $role;

                    // Log login action
                    $notifTableExists = $conn->query("SHOW TABLES LIKE 'notifications'")->num_rows > 0;
                    if ($notifTableExists) {
                        $msg  = "Welcome back, $username! You logged in successfully.";
                        $link = $role === 'admin' ? 'admin/dashboard.php' : 'user/dashboard.php';
                        $ns   = $conn->prepare("INSERT INTO notifications (user_id, message, link) VALUES (?, ?, ?)");
                        $ns->bind_param("iss", $userId, $msg, $link);
                        $ns->execute();
                        $ns->close();
                    }

                    header('Location: ' . ($role === 'admin' ? 'admin/dashboard.php' : 'user/dashboard.php'));
                    exit;
                }
            }
            $stmt->close();
            $error = 'Invalid username or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Login | UtilityTrack</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
  <style>
    * { margin:0; padding:0; box-sizing:border-box; }
    body {
      font-family:'Segoe UI',sans-serif;
      background:linear-gradient(135deg,#001F54 0%,#0a3a8a 50%,#1565c0 100%);
      min-height:100vh; display:flex; align-items:center; justify-content:center;
      padding:20px;
    }
    .card {
      background:#fff; width:100%; max-width:400px;
      border-radius:20px; box-shadow:0 20px 60px rgba(0,0,0,0.3);
      overflow:hidden;
    }
    .card-header {
      background:linear-gradient(135deg,#001F54,#0a3a8a);
      padding:32px 32px 28px; text-align:center;
    }
    .card-header .logo {
      width:52px; height:52px; background:rgba(255,255,255,0.15);
      border-radius:14px; display:flex; align-items:center; justify-content:center;
      font-size:1.4rem; color:#4fc3f7; margin:0 auto 14px;
    }
    .card-header h1 { font-size:1.5rem; font-weight:800; color:#fff; }
    .card-header p { font-size:0.82rem; color:rgba(255,255,255,0.6); margin-top:4px; }
    .card-body { padding:32px; }
    .alert {
      padding:12px 14px; border-radius:8px; font-size:0.85rem;
      margin-bottom:20px; display:flex; align-items:center; gap:8px;
    }
    .alert-error { background:#fdf2f2; color:#c0392b; border:1px solid #f5c6cb; }
    .alert-success { background:#f0fdf4; color:#166534; border:1px solid #bbf7d0; }
    .form-group { margin-bottom:18px; }
    .form-group label {
      display:block; font-size:0.82rem; font-weight:600;
      color:#374151; margin-bottom:6px;
    }
    .input-wrap { position:relative; }
    .input-wrap i {
      position:absolute; left:13px; top:50%; transform:translateY(-50%);
      color:#9ca3af; font-size:0.9rem;
    }
    .input-wrap input {
      width:100%; padding:11px 12px 11px 38px;
      border:1.5px solid #e5e7eb; border-radius:9px;
      font-size:0.92rem; transition:border-color .2s, box-shadow .2s;
      background:#fafafa;
    }
    .input-wrap input:focus {
      outline:none; border-color:#0056b3;
      box-shadow:0 0 0 3px rgba(0,86,179,0.1);
      background:#fff;
    }
    .toggle-pw {
      position:absolute; right:12px; top:50%; transform:translateY(-50%);
      background:none; border:none; cursor:pointer; color:#9ca3af; font-size:0.9rem;
    }
    .btn-login {
      width:100%; padding:12px; background:linear-gradient(135deg,#001F54,#0056b3);
      color:#fff; border:none; border-radius:9px; font-size:0.95rem;
      font-weight:700; cursor:pointer; transition:opacity .2s, transform .1s;
      display:flex; align-items:center; justify-content:center; gap:8px;
    }
    .btn-login:hover { opacity:.92; transform:translateY(-1px); }
    .btn-login:active { transform:translateY(0); }
    .divider { text-align:center; margin:20px 0; position:relative; }
    .divider::before {
      content:''; position:absolute; top:50%; left:0; right:0;
      height:1px; background:#e5e7eb;
    }
    .divider span {
      background:#fff; padding:0 12px; font-size:0.78rem;
      color:#9ca3af; position:relative;
    }
    .links { display:flex; justify-content:space-between; font-size:0.82rem; }
    .links a { color:#0056b3; text-decoration:none; font-weight:600; }
    .links a:hover { text-decoration:underline; }
    .back-home {
      display:block; text-align:center; margin-top:20px;
      font-size:0.82rem; color:#6b7280; text-decoration:none;
    }
    .back-home:hover { color:#0056b3; }
  </style>
</head>
<body>
  <div class="card">
    <div class="card-header">
      <div class="logo"><i class="fas fa-bolt"></i></div>
      <h1>UtilityTrack</h1>
      <p>Sign in to your account</p>
    </div>
    <div class="card-body">
      <?php if ($error): ?>
        <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div>
      <?php endif; ?>
      <?php if ($success): ?>
        <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($success) ?></div>
      <?php endif; ?>

      <form method="POST" action="" autocomplete="on">
        <?= csrf_field() ?>
        <div class="form-group">
          <label for="username">Username</label>
          <div class="input-wrap">
            <i class="fas fa-user"></i>
            <input type="text" id="username" name="username"
                   placeholder="Enter your username"
                   value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                   required autofocus autocomplete="username">
          </div>
        </div>
        <div class="form-group">
          <label for="password">Password</label>
          <div class="input-wrap">
            <i class="fas fa-lock"></i>
            <input type="password" id="password" name="password"
                   placeholder="Enter your password"
                   required autocomplete="current-password">
            <button type="button" class="toggle-pw" id="togglePw" tabindex="-1">
              <i class="fas fa-eye" id="eyeIcon"></i>
            </button>
          </div>
        </div>
        <button type="submit" class="btn-login">
          <i class="fas fa-sign-in-alt"></i> Sign In
        </button>
      </form>

      <div class="divider"><span>or</span></div>
      <div class="links">
        <a href="register.php"><i class="fas fa-user-plus"></i> Create account</a>
        <a href="user/reset_password.php"><i class="fas fa-key"></i> Forgot password?</a>
      </div>
      <a class="back-home" href="index.php"><i class="fas fa-arrow-left"></i> Back to home</a>
    </div>
  </div>

  <script>
    const pw = document.getElementById('password');
    const eye = document.getElementById('eyeIcon');
    document.getElementById('togglePw').addEventListener('click', () => {
      const show = pw.type === 'password';
      pw.type = show ? 'text' : 'password';
      eye.className = show ? 'fas fa-eye-slash' : 'fas fa-eye';
    });
  </script>
</body>
</html>
