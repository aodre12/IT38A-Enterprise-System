<?php
session_start();
require '../Config.php';
require '../csrf.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { header('Location: ../login.php'); exit; }
$username = $_SESSION['username'];
$success = ''; $error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_verify()) {
    $sn=trim($_POST['system_name']??''); $ce=trim($_POST['contact_email']??''); $mm=isset($_POST['maintenance_mode'])?1:0;
    if (!$sn||!$ce) $error="All fields required.";
    elseif (!filter_var($ce,FILTER_VALIDATE_EMAIL)) $error="Invalid email.";
    else {
        $s=$conn->prepare("UPDATE system_config SET system_name=?,contact_email=?,maintenance_mode=?");
        $s->bind_param("ssi",$sn,$ce,$mm);
        $success=$s->execute()?"Settings saved.":"Failed."; $s->close();
    }
}
$cfg=['system_name'=>'','contact_email'=>'','maintenance_mode'=>0];
$r=$conn->query("SELECT system_name,contact_email,maintenance_mode FROM system_config LIMIT 1");
if($r&&$row=$r->fetch_assoc()) $cfg=$row;
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Configure System | UtilityTrack</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
<style><?php include __DIR__.'/shared_styles.php'; echo $adminStyles; ?>
.form-group{margin-bottom:18px;}.form-group label{display:block;font-weight:600;font-size:.88rem;margin-bottom:6px;color:var(--text);}
.form-group input{width:100%;padding:10px 12px;border:1px solid var(--border);border-radius:8px;font-size:.9rem;}
.form-group input:focus{outline:none;border-color:var(--accent);}
.cb-label{display:flex;align-items:center;gap:10px;font-size:.9rem;cursor:pointer;}
.cb-label input{width:auto;}
.msg-ok{color:var(--accent2);font-size:.85rem;margin-bottom:14px;} .msg-err{color:var(--danger);font-size:.85rem;margin-bottom:14px;}
</style></head><body>
<?php include __DIR__.'/sidebar.php'; ?>
<div class="topbar">
  <div style="display:flex;align-items:center;gap:14px;"><button class="hamburger" id="hamburger"><i class="fas fa-bars"></i></button><span class="page-title">Configure System</span></div>
  <div class="topbar-right"><?php include __DIR__.'/notif_bell.php'; ?><span><i class="fas fa-user-circle"></i> <?= htmlspecialchars($username) ?></span></div>
</div>
<main class="main">
  <div class="section-card" style="max-width:520px;">
    <div class="section-header"><h2><i class="fas fa-cog"></i> System Settings</h2></div>
    <div class="section-body">
      <?php if($error): ?><div class="msg-err"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div><?php endif; ?>
      <?php if($success): ?><div class="msg-ok"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($success) ?></div><?php endif; ?>
      <form method="POST">
        <?= csrf_field() ?>
        <div class="form-group"><label>System Name</label><input type="text" name="system_name" value="<?= htmlspecialchars($cfg['system_name']) ?>" required></div>
        <div class="form-group"><label>Contact Email</label><input type="email" name="contact_email" value="<?= htmlspecialchars($cfg['contact_email']) ?>" required></div>
        <div class="form-group"><label class="cb-label"><input type="checkbox" name="maintenance_mode" <?= $cfg['maintenance_mode']?'checked':'' ?>> Enable Maintenance Mode</label></div>
        <button type="submit" class="btn btn-success" style="width:100%;justify-content:center;padding:11px;"><i class="fas fa-save"></i> Save Settings</button>
      </form>
      <a href="dashboard.php" class="btn btn-primary" style="width:100%;justify-content:center;padding:11px;margin-top:10px;"><i class="fas fa-arrow-left"></i> Back</a>
    </div>
  </div>
</main>
<script>const s=document.getElementById('sidebar'),h=document.getElementById('hamburger'),b=document.getElementById('backdrop');h.onclick=()=>{s.classList.toggle('open');b.classList.toggle('show');};b.onclick=()=>{s.classList.remove('open');b.classList.remove('show');};</script>
</body></html>
