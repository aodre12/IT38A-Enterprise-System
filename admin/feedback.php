<?php
session_start();
require '../Config.php';
require '../csrf.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { header('Location: ../login.php'); exit; }
$username = $_SESSION['username'];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_feedback_id']) && csrf_verify()) {
    $id=(int)$_POST['delete_feedback_id'];
    $s=$conn->prepare("DELETE FROM feedback WHERE id=?"); $s->bind_param("i",$id); $s->execute(); $s->close();
    $aid=(int)$_SESSION['user_id']; $a=$conn->prepare("INSERT INTO audit_logs (user_id,action) VALUES (?,?)"); $m="Deleted feedback #$id"; $a->bind_param("is",$aid,$m); $a->execute(); $a->close();
    header('Location: feedback.php'); exit;
}
$exists = $conn->query("SHOW TABLES LIKE 'feedback'")->num_rows > 0;
$rows = [];
if ($exists) { $r=$conn->query("SELECT f.id,u.username,f.message,f.created_at FROM feedback f JOIN users u ON f.user_id=u.id ORDER BY f.created_at DESC"); while($row=$r->fetch_assoc()) $rows[]=$row; }
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Feedback | UtilityTrack</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
<style><?php include __DIR__.'/shared_styles.php'; echo $adminStyles; ?>
textarea{width:100%;height:70px;border:1px solid #ddd;border-radius:6px;padding:8px;resize:vertical;background:#fafafa;font-size:.82rem;}
</style></head><body>
<?php include __DIR__.'/sidebar.php'; ?>
<div class="topbar">
  <div style="display:flex;align-items:center;gap:14px;"><button class="hamburger" id="hamburger"><i class="fas fa-bars"></i></button><span class="page-title">Feedback</span></div>
  <div class="topbar-right"><?php include __DIR__.'/notif_bell.php'; ?><span><i class="fas fa-user-circle"></i> <?= htmlspecialchars($username) ?></span></div>
</div>
<main class="main">
  <div class="section-card">
    <div class="section-header"><h2><i class="fas fa-comment-dots"></i> User Feedback</h2></div>
    <div class="section-body" style="padding:0;overflow-x:auto;">
      <?php if(!$exists): ?><div style="padding:20px;color:var(--muted);">Feedback table not found. Run <code>add_reports_feedback_tables.sql</code>.</div>
      <?php else: ?>
      <table class="data-table">
        <thead><tr><th>#</th><th>User</th><th>Message</th><th>Submitted</th><th>Actions</th></tr></thead>
        <tbody>
          <?php if(empty($rows)): ?><tr><td colspan="5" style="text-align:center;padding:20px;color:var(--muted);">No feedback.</td></tr>
          <?php else: foreach($rows as $f): ?>
            <tr>
              <td><?= (int)$f['id'] ?></td><td><?= htmlspecialchars($f['username']) ?></td>
              <td><textarea readonly><?= htmlspecialchars($f['message']) ?></textarea></td>
              <td><?= htmlspecialchars(substr($f['created_at'],0,10)) ?></td>
              <td><form method="POST" onsubmit="return confirm('Delete?');"><?= csrf_field() ?><input type="hidden" name="delete_feedback_id" value="<?= (int)$f['id'] ?>"><button class="btn btn-danger btn-sm" type="submit"><i class="fas fa-trash"></i> Delete</button></form></td>
            </tr>
          <?php endforeach; endif; ?>
        </tbody>
      </table>
      <?php endif; ?>
    </div>
  </div>
</main>
<script>const s=document.getElementById('sidebar'),h=document.getElementById('hamburger'),b=document.getElementById('backdrop');h.onclick=()=>{s.classList.toggle('open');b.classList.toggle('show');};b.onclick=()=>{s.classList.remove('open');b.classList.remove('show');};</script>
</body></html>
