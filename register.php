<?php
session_start();
require_once 'Config.php';
require 'csrf.php';

header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header('X-XSS-Protection: 1; mode=block');

if (isset($_SESSION['user_id'])) {
    header('Location: ' . ($_SESSION['role'] === 'admin' ? 'admin/dashboard.php' : 'user/dashboard.php'));
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify()) {
        $error = 'Invalid request. Please try again.';
    } else {
        $username         = trim($_POST['username']         ?? '');
        $password         = trim($_POST['password']         ?? '');
        $confirm_password = trim($_POST['confirm_password'] ?? '');

        if (empty($username) || empty($password) || empty($confirm_password)) {
            $error = 'All fields are required.';
        } elseif (strlen($username) < 3 || strlen($username) > 30) {
            $error = 'Username must be 3–30 characters.';
        } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
            $error = 'Username can only contain letters, numbers, and underscores.';
        } elseif (strlen($password) < 6) {
            $error = 'Password must be at least 6 characters.';
        } elseif ($password !== $confirm_password) {
            $error = 'Passwords do not match.';
        } else {
            $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $error = 'Username already exists. Please choose another.';
                $stmt->close();
            } else {
                $stmt->close();
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                $role   = 'user';

                $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $username, $hashed, $role);

                if ($stmt->execute()) {
                    $stmt->close();
                    $_SESSION['success_message'] = 'Account created! You can now log in.';
                    header('Location: login.php');
                    exit;
                } else {
                    $error = 'Registration failed. Please try again.';
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Register | UtilityTrack</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
  <style>
    * { margin:0; padding:0; box-sizing:border-box; }
    body {
      font-family:'Segoe UI',sans-serif;
      background:linear-gradient(135deg,#001F54 0%,#0a3a8a 50%,#1565c0 100%);
      min-height:100vh; display:flex; align-items:center; justify-content:center; padding:20px;
    }
    .card { background:#fff; width:100%; max-width:400px; border-radius:20px; box-shadow:0 20px 60px rgba(0,0,0,0.3); overflow:hidden; }
    .card-header { background:linear-gradient(135deg,#001F54,#0a3a8a); padding:32px 32px 28px; text-align:center; }
    .card-header .logo { width:52px; height:52px; background:rgba(255,255,255,0.15); border-radius:14px; display:flex; align-items:center; justify-content:center; font-size:1.4rem; color:#4fc3f7; margin:0 auto 14px; }
    .card-header h1 { font-size:1.5rem; font-weight:800; color:#fff; }
    .card-header p { font-size:0.82rem; color:rgba(255,255,255,0.6); margin-top:4px; }
    .card-body { padding:32px; }
    .alert { padding:12px 14px; border-radius:8px; font-size:0.85rem; margin-bottom:20px; display:flex; align-items:center; gap:8px; }
    .alert-error { background:#fdf2f2; color:#c0392b; border:1px solid #f5c6cb; }
    .form-group { margin-bottom:16px; }
    .form-group label { display:block; font-size:0.82rem; font-weight:600; color:#374151; margin-bottom:6px; }
    .input-wrap { position:relative; }
    .input-wrap i.icon { position:absolute; left:13px; top:50%; transform:translateY(-50%); color:#9ca3af; font-size:0.9rem; }
    .input-wrap input { width:100%; padding:11px 12px 11px 38px; border:1.5px solid #e5e7eb; border-radius:9px; font-size:0.92rem; transition:border-color .2s,box-shadow .2s; background:#fafafa; }
    .input-wrap input:focus { outline:none; border-color:#0056b3; box-shadow:0 0 0 3px rgba(0,86,179,0.1); background:#fff; }
    .toggle-pw { position:absolute; right:12px; top:50%; transform:translateY(-50%); background:none; border:none; cursor:pointer; color:#9ca3af; font-size:0.9rem; }
    .hint { font-size:0.75rem; color:#9ca3af; margin-top:4px; }
    .btn-register { width:100%; padding:12px; background:linear-gradient(135deg,#001F54,#0056b3); color:#fff; border:none; border-radius:9px; font-size:0.95rem; font-weight:700; cursor:pointer; transition:opacity .2s,transform .1s; display:flex; align-items:center; justify-content:center; gap:8px; }
    .btn-register:hover { opacity:.92; transform:translateY(-1px); }
    .divider { text-align:center; margin:20px 0; position:relative; }
    .divider::before { content:''; position:absolute; top:50%; left:0; right:0; height:1px; background:#e5e7eb; }
    .divider span { background:#fff; padding:0 12px; font-size:0.78rem; color:#9ca3af; position:relative; }
    .login-link { text-align:center; font-size:0.85rem; }
    .login-link a { color:#0056b3; text-decoration:none; font-weight:700; }
    .login-link a:hover { text-decoration:underline; }
    .back-home { display:block; text-align:center; margin-top:16px; font-size:0.82rem; color:#6b7280; text-decoration:none; }
    .back-home:hover { color:#0056b3; }
  </style>
</head>
<body>
  <div class="card">
    <div class="card-header">
      <div class="logo"><i class="fas fa-bolt"></i></div>
      <h1>Create Account</h1>
      <p>Join UtilityTrack today</p>
    </div>
    <div class="card-body">
      <?php if ($error): ?>
        <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="POST" action="" autocomplete="on">
        <?= csrf_field() ?>
        <div class="form-group">
          <label for="username">Username</label>
          <div class="input-wrap">
            <i class="fas fa-user icon"></i>
            <input type="text" id="username" name="username"
                   placeholder="Choose a username"
                   value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                   required autofocus autocomplete="username">
          </div>
          <div class="hint">3–30 characters, letters/numbers/underscores only</div>
        </div>
        <div class="form-group">
          <label for="password">Password</label>
          <div class="input-wrap">
            <i class="fas fa-lock icon"></i>
            <input type="password" id="password" name="password"
                   placeholder="Min. 6 characters" required autocomplete="new-password">
            <button type="button" class="toggle-pw" id="togglePw1" tabindex="-1">
              <i class="fas fa-eye" id="eye1"></i>
            </button>
          </div>
        </div>
        <div class="form-group">
          <label for="confirm_password">Confirm Password</label>
          <div class="input-wrap">
            <i class="fas fa-lock icon"></i>
            <input type="password" id="confirm_password" name="confirm_password"
                   placeholder="Repeat your password" required autocomplete="new-password">
            <button type="button" class="toggle-pw" id="togglePw2" tabindex="-1">
              <i class="fas fa-eye" id="eye2"></i>
            </button>
          </div>
        </div>
        <button type="submit" class="btn-register">
          <i class="fas fa-user-plus"></i> Create Account
        </button>
      </form>

      <div class="divider"><span>already have an account?</span></div>
      <div class="login-link"><a href="login.php"><i class="fas fa-sign-in-alt"></i> Sign in here</a></div>
      <a class="back-home" href="index.php"><i class="fas fa-arrow-left"></i> Back to home</a>
    </div>
  </div>

  <script>
    function togglePw(inputId, eyeId) {
      const inp = document.getElementById(inputId);
      const eye = document.getElementById(eyeId);
      const show = inp.type === 'password';
      inp.type = show ? 'text' : 'password';
      eye.className = show ? 'fas fa-eye-slash' : 'fas fa-eye';
    }
    document.getElementById('togglePw1').addEventListener('click', () => togglePw('password','eye1'));
    document.getElementById('togglePw2').addEventListener('click', () => togglePw('confirm_password','eye2'));
  </script>
</body>
</html>
