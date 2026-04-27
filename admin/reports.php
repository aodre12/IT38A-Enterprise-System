<?php
session_start();
require '../Config.php';
require '../csrf.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { header('Location: ../login.php'); exit; }
$username = $_SESSION['username'];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_report_id']) && csrf_verify()) {
    $id = (int)$_POST['delete_report_id'];
    $s = $conn->prepare("DELETE FROM reports WHERE id=?"); $s->bind_param("i",$id); $s->execute(); $s->close();
    $aid=(int)$_SESSION['user_id']; $a=$conn->prepare("INSERT INTO audit_logs (user_id,action) VALUES (?,?)"); $m="Deleted report #$id"; $a->bind_param("is",$aid,$m); $a->execute(); $a->close();
    header('Location: reports.php'); exit;
}
$exists = $conn->query("SHOW TABLES LIKE 'reports'")->num_rows > 0;
$rows = [];
if ($exists) { $r=$conn->query("SELECT r.id,u.username,r.title,r.report_date,r.created_at FROM reports r JOIN users u ON r.user_id=u.id ORDER BY r.created_at DESC"); while($row=$r->fetch_assoc()) $rows[]=$row; }
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Reports | UtilityTrack</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
<style><?php include __DIR__.'/shared_styles.php'; echo $adminStyles; ?></style></head><body>
<?php include __DIR__.'/sidebar.php'; ?>
<div class="topbar">
  <div style="display:flex;align-items:center;gap:14px;"><button class="hamburger" id="hamburger"><i class="fas fa-bars"></i></button><span class="page-title">Reports</span></div>
  <div class="topbar-right"><?php include __DIR__.'/notif_bell.php'; ?><span><i class="fas fa-user-circle"></i> <?= htmlspecialchars($username) ?></span></div>
</div>
<main class="main">
  <div class="section-card">
    <div class="section-header"><h2><i class="fas fa-chart-bar"></i> All Reports</h2></div>
    <div class="section-body" style="padding:0;overflow-x:auto;">
      <?php if(!$exists): ?><div style="padding:20px;color:var(--muted);">Reports table not found. Run <code>add_reports_feedback_tables.sql</code>.</div>
      <?php else: ?>
      <table class="data-table">
        <thead><tr><th>#</th><th>User</th><th>Title</th><th>Date</th><th>Created</th><th>Actions</th></tr></thead>
        <tbody>
          <?php if(empty($rows)): ?><tr><td colspan="6" style="text-align:center;padding:20px;color:var(--muted);">No reports.</td></tr>
          <?php else: foreach($rows as $r): ?>
            <tr>
              <td><?= (int)$r['id'] ?></td><td><?= htmlspecialchars($r['username']) ?></td>
              <td><?= htmlspecialchars($r['title']) ?></td><td><?= htmlspecialchars($r['report_date']) ?></td>
              <td><?= htmlspecialchars(substr($r['created_at'],0,10)) ?></td>
              <td><form method="POST" onsubmit="return confirm('Delete?');"><?= csrf_field() ?><input type="hidden" name="delete_report_id" value="<?= (int)$r['id'] ?>"><button class="btn btn-danger btn-sm" type="submit"><i class="fas fa-trash"></i> Delete</button></form></td>
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
