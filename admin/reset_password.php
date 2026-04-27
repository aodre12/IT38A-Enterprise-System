<?php
session_start();
require '../Config.php';
require '../csrf.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') { header('Location: ../login.php'); exit; }
$username = $_SESSION['username'];
$userId = (int)$_SESSION['user_id'];
$success = ''; $error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_verify()) {
    $np=$_POST['new_password']??''; $cp=$_POST['confirm_password']??'';
    if (strlen($np)<6) $error="Min. 6 characters.";
    elseif ($np!==$cp) $error="Passwords don't match.";
    else {
        $h=password_hash($np,PASSWORD_DEFAULT);
        $s=$conn->prepare("UPDATE users SET password=? WHERE id=?"); $s->bind_param("si",$h,$userId);
        $success=$s->execute()?"Password updated.":"Failed."; $s->close();
    }
}
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Reset Password | UtilityTrack</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
<style><?php include __DIR__.'/shared_styles.php'; echo $adminStyles; ?>
.form-group{margin-bottom:18px;}.form-group label{display:block;font-weight:600;font-size:.88rem;margin-bottom:6px;color:var(--text);}
.form-group input{width:100%;padding:10px 12px;border:1px solid var(--border);border-radius:8px;font-size:.9rem;}
.form-group input:focus{outline:none;border-color:var(--accent);}
.msg-ok{color:var(--accent2);font-size:.85rem;margin-bottom:14px;} .msg-err{color:var(--danger);font-size:.85rem;margin-bottom:14px;}
</style></head><body>
<?php include __DIR__.'/sidebar.php'; ?>
<div class="topbar">
  <div style="display:flex;align-items:center;gap:14px;"><button class="hamburger" id="hamburger"><i class="fas fa-bars"></i></button><span class="page-title">Reset Password</span></div>
  <div class="topbar-right"><?php include __DIR__.'/notif_bell.php'; ?><span><i class="fas fa-user-circle"></i> <?= htmlspecialchars($username) ?></span></div>
</div>
<main class="main">
  <div class="section-card" style="max-width:460px;">
    <div class="section-header"><h2><i class="fas fa-key"></i> Reset Password</h2></div>
    <div class="section-body">
      <?php if($error): ?><div class="msg-err"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div><?php endif; ?>
      <?php if($success): ?><div class="msg-ok"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($success) ?></div><?php endif; ?>
      <form method="POST">
        <?= csrf_field() ?>
        <div class="form-group"><label>New Password</label><input type="password" name="new_password" placeholder="Min. 6 characters" required></div>
        <div class="form-group"><label>Confirm Password</label><input type="password" name="confirm_password" placeholder="Repeat password" required></div>
        <button type="submit" class="btn btn-success" style="width:100%;justify-content:center;padding:11px;"><i class="fas fa-save"></i> Update Password</button>
      </form>
      <a href="dashboard.php" class="btn btn-primary" style="width:100%;justify-content:center;padding:11px;margin-top:10px;"><i class="fas fa-arrow-left"></i> Back</a>
    </div>
  </div>
</main>
<script>const s=document.getElementById('sidebar'),h=document.getElementById('hamburger'),b=document.getElementById('backdrop');h.onclick=()=>{s.classList.toggle('open');b.classList.toggle('show');};b.onclick=()=>{s.classList.remove('open');b.classList.remove('show');};</script>
</body></html>
