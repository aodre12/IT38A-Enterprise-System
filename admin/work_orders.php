<?php
session_start();
require '../Config.php';
require '../csrf.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { header('Location: ../login.php'); exit; }
$username = $_SESSION['username'];
$allowed = ['Pending','In Progress','Completed','Cancelled'];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['work_order_id'], $_POST['status'])) {
    if (csrf_verify()) {
        $wid = (int)$_POST['work_order_id']; $ns = $_POST['status'];
        if (in_array($ns, $allowed, true)) {
            $s = $conn->prepare("UPDATE work_orders SET status=? WHERE id=?");
            $s->bind_param("si", $ns, $wid); $s->execute(); $s->close();
            $aid = (int)$_SESSION['user_id'];
            $a = $conn->prepare("INSERT INTO audit_logs (user_id, action) VALUES (?, ?)");
            $am = "Updated work order #$wid to $ns"; $a->bind_param("is", $aid, $am); $a->execute(); $a->close();
        }
    }
    header('Location: work_orders.php'); exit;
}
$rows = [];
$s = $conn->prepare("SELECT w.id,w.title,w.description,p.name AS assigned_name,w.status,w.due_date,w.created_at FROM work_orders w LEFT JOIN personnel p ON w.assigned_to=p.id ORDER BY w.created_at DESC");
$s->execute(); $r = $s->get_result();
while ($row = $r->fetch_assoc()) $rows[] = $row;
$s->close();
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Work Orders | UtilityTrack</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
<style><?php include __DIR__.'/shared_styles.php'; echo $adminStyles; ?>
textarea{width:100%;height:60px;border:1px solid #ddd;border-radius:6px;padding:8px;resize:vertical;background:#fafafa;font-size:.82rem;}
select{padding:7px 10px;border-radius:6px;border:1px solid #ccc;font-size:.82rem;width:100%;margin-bottom:6px;}
</style></head><body>
<?php include __DIR__.'/sidebar.php'; ?>
<div class="topbar">
  <div style="display:flex;align-items:center;gap:14px;"><button class="hamburger" id="hamburger"><i class="fas fa-bars"></i></button><span class="page-title">Work Orders</span></div>
  <div class="topbar-right"><?php include __DIR__.'/notif_bell.php'; ?><span><i class="fas fa-user-circle"></i> <?= htmlspecialchars($username) ?></span></div>
</div>
<main class="main">
  <div class="section-card">
    <div class="section-header"><h2><i class="fas fa-clipboard-list"></i> All Work Orders</h2><a href="create_work.php" class="btn btn-success btn-sm"><i class="fas fa-plus"></i> New</a></div>
    <div style="overflow-x:auto;">
      <table class="data-table">
        <thead><tr><th>#</th><th>Title</th><th>Description</th><th>Assigned</th><th>Due</th><th>Status</th><th>Created</th><th>Update</th></tr></thead>
        <tbody>
          <?php if(empty($rows)): ?>
            <tr><td colspan="8" style="text-align:center;padding:20px;color:var(--muted);">No work orders found.</td></tr>
          <?php else: foreach($rows as $w):
            $sc=['Pending'=>'badge-pending','In Progress'=>'badge-pending','Completed'=>'badge-done','Cancelled'=>'badge-inactive'];
            $cls=$sc[$w['status']]??'badge-pending'; ?>
            <tr>
              <td><?= (int)$w['id'] ?></td>
              <td><?= htmlspecialchars($w['title']) ?></td>
              <td><textarea readonly><?= htmlspecialchars($w['description']??'') ?></textarea></td>
              <td><?= htmlspecialchars($w['assigned_name']??'—') ?></td>
              <td><?= htmlspecialchars($w['due_date']??'—') ?></td>
              <td><span class="badge <?= $cls ?>"><?= htmlspecialchars($w['status']) ?></span></td>
              <td><?= htmlspecialchars(substr($w['created_at'],0,10)) ?></td>
              <td>
                <form method="POST">
                  <?= csrf_field() ?>
                  <input type="hidden" name="work_order_id" value="<?= (int)$w['id'] ?>">
                  <select name="status"><?php foreach($allowed as $st): ?><option value="<?= $st ?>" <?= $w['status']===$st?'selected':'' ?>><?= $st ?></option><?php endforeach; ?></select>
                  <button class="btn btn-success btn-sm" type="submit" style="width:100%;">Save</button>
                </form>
              </td>
            </tr>
          <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</main>
<script>const s=document.getElementById('sidebar'),h=document.getElementById('hamburger'),b=document.getElementById('backdrop');h.onclick=()=>{s.classList.toggle('open');b.classList.toggle('show');};b.onclick=()=>{s.classList.remove('open');b.classList.remove('show');};</script>
</body></html>
