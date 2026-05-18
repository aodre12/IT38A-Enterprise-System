<?php
session_start();
require '../Config.php';
require '../csrf.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') { header('Location: ../login.php'); exit; }
$username = $_SESSION['username'];
$personnelList = $conn->query("SELECT id, name FROM personnel WHERE status='Active'")->fetch_all(MYSQLI_ASSOC);
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify()) { $error = 'Invalid request.'; }
    else {
        $task = trim($_POST['task'] ?? ''); $assigned_to = $_POST['assigned_to'] ?? ''; $status = $_POST['status'] ?? 'Pending';
        if ($task && $assigned_to) {
            $s = $conn->prepare("INSERT INTO tasks (task, assigned_to, status) VALUES (?, ?, ?)");
            $s->bind_param("sis", $task, $assigned_to, $status);
            if ($s->execute()) { header('Location: dashboard.php'); exit; }
            else $error = "Failed to assign task.";
        } else $error = "Task and assignee are required.";
    }
}
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Assign Task | UtilityTrack</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
<style><?php include __DIR__.'/shared_styles.php'; echo $adminStyles; ?>
.form-group{margin-bottom:18px;}.form-group label{display:block;font-weight:600;font-size:.88rem;margin-bottom:6px;color:var(--text);}
.form-group input,.form-group select,.form-group textarea{width:100%;padding:10px 12px;border:1px solid var(--border);border-radius:8px;font-size:.9rem;resize:vertical;}
.form-group input:focus,.form-group select:focus,.form-group textarea:focus{outline:none;border-color:var(--accent);}
.form-error{color:var(--danger);font-size:.85rem;margin-bottom:14px;}
</style></head><body>
<?php include __DIR__.'/sidebar.php'; ?>
<div class="topbar">
  <div style="display:flex;align-items:center;gap:14px;"><button class="hamburger" id="hamburger"><i class="fas fa-bars"></i></button><span class="page-title">Assign Task</span></div>
  <div class="topbar-right"><?php include __DIR__.'/notif_bell.php'; ?><span><i class="fas fa-user-circle"></i> <?= htmlspecialchars($username) ?></span></div>
</div>
<main class="main">
  <div class="section-card" style="max-width:520px;">
    <div class="section-header"><h2><i class="fas fa-tasks"></i> Assign New Task</h2></div>
    <div class="section-body">
      <?php if($error): ?><div class="form-error"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div><?php endif; ?>
      <form method="POST">
        <?= csrf_field() ?>
        <div class="form-group"><label>Task Description</label><textarea name="task" rows="4" placeholder="Describe the task..." required></textarea></div>
        <div class="form-group"><label>Assign To</label>
          <select name="assigned_to" required><option value="">— Select Personnel —</option>
            <?php foreach($personnelList as $p): ?><option value="<?= (int)$p['id'] ?>"><?= htmlspecialchars($p['name']) ?></option><?php endforeach; ?>
          </select>
        </div>
        <div class="form-group"><label>Status</label><select name="status"><option value="Pending">Pending</option><option value="Completed">Completed</option></select></div>
        <button type="submit" class="btn btn-success" style="width:100%;justify-content:center;padding:11px;"><i class="fas fa-check"></i> Assign Task</button>
      </form>
      <a href="dashboard.php" class="btn btn-primary" style="width:100%;justify-content:center;padding:11px;margin-top:10px;"><i class="fas fa-arrow-left"></i> Back</a>
    </div>
  </div>
</main>
<script>const s=document.getElementById('sidebar'),h=document.getElementById('hamburger'),b=document.getElementById('backdrop');h.onclick=()=>{s.classList.toggle('open');b.classList.toggle('show');};b.onclick=()=>{s.classList.remove('open');b.classList.remove('show');};</script>
</body></html>
