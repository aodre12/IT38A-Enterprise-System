<?php
session_start();
require '../Config.php';
require '../csrf.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { header('Location: ../login.php'); exit; }
$username = $_SESSION['username'];
$success = ''; $error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_verify()) {
    $uid=(int)($_POST['user_id']??0); $nr=$_POST['role']??''; $ns=$_POST['status']??'';
    if ($uid && $nr && $ns) {
        $s=$conn->prepare("UPDATE personnel SET role=?,status=? WHERE id=?"); $s->bind_param("ssi",$nr,$ns,$uid);
        $success = $s->execute() ? "Access updated." : "Failed."; $s->close();
    } else $error = "Fill all fields.";
}
$users = $conn->query("SELECT id,name,email,role,status FROM personnel")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Update Access | UtilityTrack</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
<style><?php include __DIR__.'/shared_styles.php'; echo $adminStyles; ?>
select{padding:7px 10px;border-radius:6px;border:1px solid var(--border);font-size:.82rem;}
.msg-ok{color:var(--accent2);font-size:.85rem;padding:14px 24px;} .msg-err{color:var(--danger);font-size:.85rem;padding:14px 24px;}
</style></head><body>
<?php include __DIR__.'/sidebar.php'; ?>
<div class="topbar">
  <div style="display:flex;align-items:center;gap:14px;"><button class="hamburger" id="hamburger"><i class="fas fa-bars"></i></button><span class="page-title">Update Access</span></div>
  <div class="topbar-right"><?php include __DIR__.'/notif_bell.php'; ?><span><i class="fas fa-user-circle"></i> <?= htmlspecialchars($username) ?></span></div>
</div>
<main class="main">
  <div class="section-card">
    <div class="section-header"><h2><i class="fas fa-shield-alt"></i> Personnel Access Control</h2></div>
    <div class="section-body" style="padding:0;overflow-x:auto;">
      <?php if($success): ?><div class="msg-ok"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($success) ?></div><?php endif; ?>
      <?php if($error):   ?><div class="msg-err"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div><?php endif; ?>
      <table class="data-table">
        <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Action</th></tr></thead>
        <tbody>
          <?php if(empty($users)): ?><tr><td colspan="5" style="text-align:center;padding:20px;color:var(--muted);">No personnel.</td></tr>
          <?php else: foreach($users as $u): ?>
            <tr>
              <td><?= htmlspecialchars($u['name']) ?></td>
              <td><?= htmlspecialchars($u['email']??'—') ?></td>
              <form method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="user_id" value="<?= (int)$u['id'] ?>">
                <td><select name="role"><option value="user" <?= $u['role']==='user'?'selected':'' ?>>User</option><option value="admin" <?= $u['role']==='admin'?'selected':'' ?>>Admin</option></select></td>
                <td><select name="status"><option value="Active" <?= $u['status']==='Active'?'selected':'' ?>>Active</option><option value="Inactive" <?= $u['status']==='Inactive'?'selected':'' ?>>Inactive</option></select></td>
                <td><button class="btn btn-success btn-sm" type="submit"><i class="fas fa-save"></i> Save</button></td>
              </form>
            </tr>
          <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</main>
<script>const s=document.getElementById('sidebar'),h=document.getElementById('hamburger'),b=document.getElementById('backdrop');h.onclick=()=>{s.classList.toggle('open');b.classList.toggle('show');};b.onclick=()=>{s.classList.remove('open');b.classList.remove('show');};</script>
</body></html>
