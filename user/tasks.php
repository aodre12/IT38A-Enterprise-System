<?php
session_start();
require '../Config.php';
require 'layout.php';
if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') { header('Location: ../admin/dashboard.php'); exit; }
if (!isset($_SESSION['user_id'])) { header('Location: ../login.php'); exit; }

$userId   = (int)$_SESSION['user_id'];
$username = $_SESSION['username'];

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'complete' && isset($_POST['task_id'])) {
        $tid = (int)$_POST['task_id'];
        $s = $conn->prepare("UPDATE tasks SET status='Completed' WHERE id=? AND assigned_to=? AND status!='Completed'");
        $s->bind_param("ii", $tid, $userId); $s->execute(); $s->close();
        $a = $conn->prepare("INSERT INTO audit_logs (user_id, action) VALUES (?, ?)");
        $m = "Completed task #$tid"; $a->bind_param("is", $userId, $m); $a->execute(); $a->close();
    }
    if ($_POST['action'] === 'add' && !empty(trim($_POST['new_task'] ?? ''))) {
        $txt = trim($_POST['new_task']);
        $s = $conn->prepare("INSERT INTO tasks (task, assigned_to, status) VALUES (?, ?, 'Pending')");
        $s->bind_param("si", $txt, $userId); $s->execute(); $s->close();
        $a = $conn->prepare("INSERT INTO audit_logs (user_id, action) VALUES (?, ?)");
        $m = "Added task: $txt"; $a->bind_param("is", $userId, $m); $a->execute(); $a->close();
    }
    header('Location: tasks.php'); exit;
}

// Load tasks
$stmt = $conn->prepare("SELECT id, task, status FROM tasks WHERE assigned_to=? ORDER BY id DESC");
$stmt->bind_param("i", $userId); $stmt->execute(); $res = $stmt->get_result();
$pending = []; $done = [];
while ($r = $res->fetch_assoc()) { if ($r['status']==='Completed') $done[]=$r; else $pending[]=$r; }
$stmt->close();
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>My Tasks | UtilityTrack</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
<?php user_styles('.task-item{display:flex;align-items:center;gap:12px;padding:13px 0;border-bottom:1px solid var(--border);}
.task-item:last-child{border:none;}
.task-check{width:22px;height:22px;border-radius:50%;border:2px solid var(--border);display:flex;align-items:center;justify-content:center;flex-shrink:0;cursor:pointer;}
.task-check.done{background:var(--accent);border-color:var(--accent);color:#fff;}
.task-text{flex:1;font-size:.88rem;}
.task-text.done{text-decoration:line-through;color:var(--muted);}
.add-form{display:flex;gap:10px;}
.add-form input{flex:1;}
.stats-mini{display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:20px;}
.sm-card{background:var(--card);border-radius:12px;padding:16px;box-shadow:0 2px 8px rgba(0,0,0,.05);text-align:center;border-top:3px solid var(--blue);}
.sm-card.g{border-top-color:var(--accent);}
.sm-num{font-size:1.8rem;font-weight:800;color:var(--text);}
.sm-lbl{font-size:.72rem;color:var(--muted);text-transform:uppercase;letter-spacing:.4px;margin-top:2px;}
'); ?>
</head><body>
<?php user_sidebar('tasks.php'); ?>
<?php user_topbar('My Tasks', $conn); ?>
<main class="main">

  <!-- Mini stats -->
  <div class="stats-mini">
    <div class="sm-card"><div class="sm-num"><?= count($pending) ?></div><div class="sm-lbl">Pending</div></div>
    <div class="sm-card g"><div class="sm-num"><?= count($done) ?></div><div class="sm-lbl">Completed</div></div>
  </div>

  <!-- Add task -->
  <div class="page-card">
    <div class="pc-header"><h2><i class="fas fa-plus-circle" style="color:var(--blue);"></i> Add New Task</h2></div>
    <div class="pc-body">
      <form method="POST" class="add-form">
        <input type="hidden" name="action" value="add">
        <div class="form-group" style="flex:1;margin:0;">
          <input type="text" name="new_task" placeholder="Describe your task..." required>
        </div>
        <button type="submit" class="btn btn-primary"><i class="fas fa-plus"></i> Add</button>
      </form>
    </div>
  </div>

  <!-- Pending tasks -->
  <div class="page-card">
    <div class="pc-header">
      <h2><i class="fas fa-hourglass-half" style="color:var(--warning);"></i> Pending Tasks</h2>
      <span class="badge b-pending"><?= count($pending) ?> tasks</span>
    </div>
    <div class="pc-body">
      <?php if (empty($pending)): ?>
        <div class="empty-state"><i class="fas fa-check-double"></i><p>All caught up! No pending tasks.</p></div>
      <?php else: ?>
        <?php foreach ($pending as $t): ?>
          <div class="task-item">
            <form method="POST" style="margin:0;">
              <input type="hidden" name="action" value="complete">
              <input type="hidden" name="task_id" value="<?= (int)$t['id'] ?>">
              <button type="submit" class="task-check" title="Mark complete"><i class="fas fa-check" style="font-size:.65rem;"></i></button>
            </form>
            <span class="task-text"><?= htmlspecialchars($t['task']) ?></span>
            <span class="badge b-pending">Pending</span>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>

  <!-- Completed tasks -->
  <?php if (!empty($done)): ?>
  <div class="page-card">
    <div class="pc-header">
      <h2><i class="fas fa-check-circle" style="color:var(--accent);"></i> Completed Tasks</h2>
      <span class="badge b-done"><?= count($done) ?> done</span>
    </div>
    <div class="pc-body">
      <?php foreach ($done as $t): ?>
        <div class="task-item">
          <div class="task-check done"><i class="fas fa-check" style="font-size:.65rem;"></i></div>
          <span class="task-text done"><?= htmlspecialchars($t['task']) ?></span>
          <span class="badge b-done">Done</span>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>

</main>
<?php user_sidebar_script(); ?>
</body></html>
