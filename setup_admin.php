<?php
// ONE-TIME ADMIN SETUP — DELETE THIS FILE AFTER USE
require 'Config.php';

$username = 'lorbelleganzan';
$password = 'ganzan15';
$role     = 'admin';
$hash     = password_hash($password, PASSWORD_DEFAULT);

// Check if already exists
$check = $conn->prepare("SELECT id, role FROM users WHERE username = ?");
$check->bind_param("s", $username);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    // Already exists — just make sure role is admin
    $check->bind_result($existingId, $existingRole);
    $check->fetch();
    $check->close();

    if ($existingRole !== 'admin') {
        $upd = $conn->prepare("UPDATE users SET role='admin', password=? WHERE id=?");
        $upd->bind_param("si", $hash, $existingId);
        $upd->execute();
        $upd->close();
        $msg = "✅ Existing user updated to admin role!";
    } else {
        // Just update password in case
        $upd = $conn->prepare("UPDATE users SET password=? WHERE id=?");
        $upd->bind_param("si", $hash, $existingId);
        $upd->execute();
        $upd->close();
        $msg = "✅ Admin already exists — password refreshed!";
    }
} else {
    $check->close();
    // Insert new admin (no email column needed)
    $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $hash, $role);
    if ($stmt->execute()) {
        $msg = "✅ Admin account created!";
    } else {
        $msg = "❌ Failed: " . htmlspecialchars($conn->error);
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Setup</title>
  <style>
    * { margin:0; padding:0; box-sizing:border-box; }
    body { font-family:'Segoe UI',sans-serif; background:linear-gradient(135deg,#001F54,#1565c0); min-height:100vh; display:flex; align-items:center; justify-content:center; }
    .card { background:#fff; border-radius:16px; padding:36px 32px; max-width:420px; width:100%; box-shadow:0 20px 60px rgba(0,0,0,.3); }
    .icon { font-size:2.5rem; text-align:center; margin-bottom:16px; }
    h2 { text-align:center; font-size:1.3rem; color:#001F54; margin-bottom:24px; }
    .info-row { display:flex; justify-content:space-between; padding:10px 0; border-bottom:1px solid #f0f0f0; font-size:.9rem; }
    .info-row:last-of-type { border:none; }
    .info-row .lbl { color:#7f8c8d; }
    .info-row .val { font-weight:700; color:#2c3e50; }
    .warn { background:#fff3cd; border:1px solid #ffc107; border-radius:8px; padding:12px 14px; font-size:.83rem; color:#856404; margin:20px 0; display:flex; align-items:center; gap:8px; }
    .btn { display:block; text-align:center; margin-top:20px; padding:12px; background:linear-gradient(135deg,#001F54,#0056b3); color:#fff; border-radius:9px; text-decoration:none; font-weight:700; font-size:.95rem; }
    .btn:hover { opacity:.9; }
    .msg { text-align:center; font-size:1rem; font-weight:600; color:#166534; margin-bottom:20px; }
  </style>
</head>
<body>
  <div class="card">
    <div class="icon">🔐</div>
    <h2>Admin Setup</h2>
    <div class="msg"><?= $msg ?></div>

    <div class="info-row"><span class="lbl">Username</span><span class="val">lorbelleganzan</span></div>
    <div class="info-row"><span class="lbl">Password</span><span class="val">ganzan15</span></div>
    <div class="info-row"><span class="lbl">Role</span><span class="val">Admin ✓</span></div>

    <div class="warn">⚠️ <strong>Delete this file immediately after logging in!</strong> It's a security risk.</div>

    <a href="login.php" class="btn">Go to Login →</a>
  </div>
</body>
</html>
