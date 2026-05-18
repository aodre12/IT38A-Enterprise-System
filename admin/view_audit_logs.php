<?php
session_start();
require '../Config.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { header('Location: ../login.php'); exit; }
$username = $_SESSION['username'];
$logs = [];
$r = $conn->query("SELECT a.id,u.username,a.action,a.timestamp FROM audit_logs a JOIN users u ON a.user_id=u.id ORDER BY a.timestamp DESC");
if ($r) while ($row=$r->fetch_assoc()) $logs[]=$row;
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Audit Logs | UtilityTrack</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
<style><?php include __DIR__.'/shared_styles.php'; echo $adminStyles; ?></style></head><body>
<?php include __DIR__.'/sidebar.php'; ?>
<div class="topbar">
  <div style="display:flex;align-items:center;gap:14px;"><button class="hamburger" id="hamburger"><i class="fas fa-bars"></i></button><span class="page-title">Audit Logs</span></div>
  <div class="topbar-right"><?php include __DIR__.'/notif_bell.php'; ?><span><i class="fas fa-user-circle"></i> <?= htmlspecialchars($username) ?></span></div>
</div>
<main class="main">
  <div class="section-card">
    <div class="section-header"><h2><i class="fas fa-history"></i> System Audit Logs</h2></div>
    <div class="section-body" style="padding:0;overflow-x:auto;">
      <table class="data-table">
        <thead><tr><th>#</th><th>User</th><th>Action</th><th>Timestamp</th></tr></thead>
        <tbody>
          <?php if(empty($logs)): ?><tr><td colspan="4" style="text-align:center;padding:20px;color:var(--muted);">No logs.</td></tr>
          <?php else: foreach($logs as $l): ?>
            <tr><td><?= (int)$l['id'] ?></td><td><?= htmlspecialchars($l['username']) ?></td><td><?= htmlspecialchars($l['action']) ?></td><td><?= htmlspecialchars($l['timestamp']) ?></td></tr>
          <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</main>
<script>const s=document.getElementById('sidebar'),h=document.getElementById('hamburger'),b=document.getElementById('backdrop');h.onclick=()=>{s.classList.toggle('open');b.classList.toggle('show');};b.onclick=()=>{s.classList.remove('open');b.classList.remove('show');};</script>
</body></html>
