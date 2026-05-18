<?php
session_start();
require '../Config.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { header('Location: ../login.php'); exit; }
$username = $_SESSION['username'];
$users = $conn->query("SELECT id, username, role, created_at FROM users ORDER BY id ASC")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Manage Users | UtilityTrack</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
<style><?php include __DIR__.'/shared_styles.php'; echo $adminStyles; ?></style></head><body>
<?php include __DIR__.'/sidebar.php'; ?>
<div class="topbar">
  <div style="display:flex;align-items:center;gap:14px;"><button class="hamburger" id="hamburger"><i class="fas fa-bars"></i></button><span class="page-title">Manage Users</span></div>
  <div class="topbar-right"><?php include __DIR__.'/notif_bell.php'; ?><span><i class="fas fa-user-circle"></i> <?= htmlspecialchars($username) ?></span></div>
</div>
<main class="main">
  <div class="section-card">
    <div class="section-header"><h2><i class="fas fa-users"></i> All Users</h2></div>
    <div class="section-body" style="padding:0;overflow-x:auto;">
      <table class="data-table">
        <thead><tr><th>#</th><th>Username</th><th>Role</th><th>Registered</th><th>Actions</th></tr></thead>
        <tbody>
          <?php if(empty($users)): ?><tr><td colspan="5" style="text-align:center;padding:20px;color:var(--muted);">No users.</td></tr>
          <?php else: foreach($users as $u): ?>
            <tr>
              <td><?= (int)$u['id'] ?></td><td><?= htmlspecialchars($u['username']) ?></td>
              <td><span class="badge <?= $u['role']==='admin'?'badge-active':'badge-pending' ?>"><?= htmlspecialchars($u['role']) ?></span></td>
              <td><?= htmlspecialchars(substr($u['created_at'],0,10)) ?></td>
              <td><a href="update_access.php" class="btn btn-primary btn-sm"><i class="fas fa-shield-alt"></i> Access</a></td>
            </tr>
          <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</main>
<script>const s=document.getElementById('sidebar'),h=document.getElementById('hamburger'),b=document.getElementById('backdrop');h.onclick=()=>{s.classList.toggle('open');b.classList.toggle('show');};b.onclick=()=>{s.classList.remove('open');b.classList.remove('show');};</script>
</body></html>
