<?php
session_start();
require '../Config.php';
require '../csrf.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') { header('Location: ../login.php'); exit; }
$username = $_SESSION['username'];
$personnelList = $conn->query("SELECT id, name FROM personnel ORDER BY status DESC, name ASC");
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify()) { $error = 'Invalid request.'; }
    else {
        $title = trim($_POST['title'] ?? ''); $desc = trim($_POST['description'] ?? '');
        $due = $_POST['due_date'] ?? ''; $assigned = $_POST['assigned_to'] ?? null; $by = (int)$_SESSION['user_id'];
        if (!$title || !$desc || !$due) { $error = 'All fields are required.'; }
        else {
            if ($assigned === '') $assigned = null;
            $s = $conn->prepare("INSERT INTO work_orders (title, description, assigned_to, due_date, created_by) VALUES (?, ?, ?, ?, ?)");
            $s->bind_param("ssisi", $title, $desc, $assigned, $due, $by);
            if ($s->execute()) { header('Location: dashboard.php'); exit; }
            else $error = 'Failed: ' . $conn->error;
        }
    }
}
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Create Work Order | UtilityTrack</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
<style><?php include __DIR__.'/shared_styles.php'; echo $adminStyles; ?>
.form-group{margin-bottom:18px;}.form-group label{display:block;font-weight:600;font-size:.88rem;margin-bottom:6px;color:var(--text);}
.form-group input,.form-group select,.form-group textarea{width:100%;padding:10px 12px;border:1px solid var(--border);border-radius:8px;font-size:.9rem;resize:vertical;}
.form-group input:focus,.form-group select:focus,.form-group textarea:focus{outline:none;border-color:var(--accent);}
.form-error{color:var(--danger);font-size:.85rem;margin-bottom:14px;}
</style></head><body>
<?php include __DIR__.'/sidebar.php'; ?>
<div class="topbar">
  <div style="display:flex;align-items:center;gap:14px;"><button class="hamburger" id="hamburger"><i class="fas fa-bars"></i></button><span class="page-title">Create Work Order</span></div>
  <div class="topbar-right"><?php include __DIR__.'/notif_bell.php'; ?><span><i class="fas fa-user-circle"></i> <?= htmlspecialchars($username) ?></span></div>
</div>
<main class="main">
  <div class="section-card" style="max-width:560px;">
    <div class="section-header"><h2><i class="fas fa-plus-circle"></i> New Work Order</h2></div>
    <div class="section-body">
      <?php if($error): ?><div class="form-error"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div><?php endif; ?>
      <form method="POST">
        <?= csrf_field() ?>
        <div class="form-group"><label>Title</label><input type="text" name="title" placeholder="Work order title" required></div>
        <div class="form-group"><label>Description</label><textarea name="description" rows="4" placeholder="Describe the work..." required></textarea></div>
        <div class="form-group"><label>Due Date</label><input type="date" name="due_date" required></div>
        <div class="form-group"><label>Assign To (optional)</label>
          <select name="assigned_to"><option value="">— Unassigned —</option>
            <?php if($personnelList): while($p=$personnelList->fetch_assoc()): ?>
              <option value="<?= (int)$p['id'] ?>"><?= htmlspecialchars($p['name']) ?></option>
            <?php endwhile; endif; ?>
          </select>
        </div>
        <button type="submit" class="btn btn-success" style="width:100%;justify-content:center;padding:11px;"><i class="fas fa-save"></i> Create Work Order</button>
      </form>
      <a href="dashboard.php" class="btn btn-primary" style="width:100%;justify-content:center;padding:11px;margin-top:10px;"><i class="fas fa-arrow-left"></i> Back</a>
    </div>
  </div>
</main>
<script>const s=document.getElementById('sidebar'),h=document.getElementById('hamburger'),b=document.getElementById('backdrop');h.onclick=()=>{s.classList.toggle('open');b.classList.toggle('show');};b.onclick=()=>{s.classList.remove('open');b.classList.remove('show');};</script>
</body></html>
