<?php
session_start();
require '../Config.php';

header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php'); exit;
}
if (!isset($_SESSION['user_id'], $_SESSION['username'])) {
    header('Location: ../login.php'); exit;
}

$username = $_SESSION['username'];

$workOrders      = $conn->query("SELECT COUNT(*) FROM work_orders")->fetch_row()[0] ?? 0;
$personnelActive = $conn->query("SELECT COUNT(*) FROM personnel WHERE status='Active'")->fetch_row()[0] ?? 0;
$pendingTasks    = $conn->query("SELECT COUNT(*) FROM tasks WHERE status='Pending'")->fetch_row()[0] ?? 0;
$completedTasks  = $conn->query("SELECT COUNT(*) FROM tasks WHERE status='Completed'")->fetch_row()[0] ?? 0;
$personnel       = $conn->query("SELECT id, name, role, status FROM personnel")->fetch_all(MYSQLI_ASSOC);
$tasks           = $conn->query("SELECT t.task, p.name AS assigned, t.status FROM tasks t JOIN personnel p ON t.assigned_to=p.id")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Admin Dashboard | UtilityTrack</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
  <style><?php include __DIR__ . '/shared_styles.php'; echo $adminStyles; ?></style>
</head>
<body>
<?php include __DIR__ . '/sidebar.php'; ?>
<div class="topbar">
  <div style="display:flex;align-items:center;gap:14px;">
    <button class="hamburger" id="hamburger"><i class="fas fa-bars"></i></button>
    <span class="page-title">Admin Dashboard</span>
  </div>
  <div class="topbar-right">
    <?php include __DIR__ . '/notif_bell.php'; ?>
    <span><i class="fas fa-user-circle"></i> <?= htmlspecialchars($username) ?></span>
  </div>
</div>
<main class="main">
  <div class="stats-grid">
    <div class="stat-card">
      <div class="icon"><i class="fas fa-clipboard-list"></i></div>
      <div class="stat-info"><div class="label">Work Orders</div><div class="value"><?= $workOrders ?></div></div>
    </div>
    <div class="stat-card green">
      <div class="icon"><i class="fas fa-users"></i></div>
      <div class="stat-info"><div class="label">Active Personnel</div><div class="value"><?= $personnelActive ?></div></div>
    </div>
    <div class="stat-card orange">
      <div class="icon"><i class="fas fa-hourglass-half"></i></div>
      <div class="stat-info"><div class="label">Pending Tasks</div><div class="value"><?= $pendingTasks ?></div></div>
    </div>
    <div class="stat-card red">
      <div class="icon"><i class="fas fa-check-circle"></i></div>
      <div class="stat-info"><div class="label">Completed Tasks</div><div class="value"><?= $completedTasks ?></div></div>
    </div>
  </div>

  <div class="section-card">
    <div class="section-header"><h2><i class="fas fa-bolt"></i> Quick Actions</h2></div>
    <div class="section-body">
      <div class="quick-actions">
        <a href="add_personnel.php"   class="btn btn-success"><i class="fas fa-user-plus"></i> Add Personnel</a>
        <a href="assign_task.php"     class="btn btn-primary"><i class="fas fa-tasks"></i> Assign Task</a>
        <a href="create_work.php"     class="btn btn-primary"><i class="fas fa-plus-circle"></i> Create Work Order</a>
        <a href="work_orders.php"     class="btn btn-warning"><i class="fas fa-clipboard-list"></i> Work Orders</a>
        <a href="reports.php"         class="btn btn-primary"><i class="fas fa-chart-bar"></i> Reports</a>
        <a href="feedback.php"        class="btn btn-primary"><i class="fas fa-comment-dots"></i> Feedback</a>
        <a href="view_audit_logs.php" class="btn btn-primary"><i class="fas fa-history"></i> Audit Logs</a>
        <a href="configure_system.php"class="btn btn-warning"><i class="fas fa-cog"></i> Configure System</a>
      </div>
    </div>
  </div>

  <div class="section-card">
    <div class="section-header">
      <h2><i class="fas fa-users"></i> Personnel</h2>
      <a href="add_personnel.php" class="btn btn-success btn-sm"><i class="fas fa-plus"></i> Add</a>
    </div>
    <div class="section-body" style="padding:0;">
      <table class="data-table">
        <thead><tr><th>Name</th><th>Role</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>
          <?php if (empty($personnel)): ?>
            <tr><td colspan="4" style="text-align:center;color:var(--muted);padding:20px;">No personnel found.</td></tr>
          <?php else: foreach ($personnel as $p): ?>
            <tr>
              <td><?= htmlspecialchars($p['name']) ?></td>
              <td><?= htmlspecialchars($p['role']) ?></td>
              <td><span class="badge <?= $p['status']==='Active'?'badge-active':'badge-inactive' ?>"><?= htmlspecialchars($p['status']) ?></span></td>
              <td>
                <a href="edit_personnel.php?id=<?= $p['id'] ?>" class="btn btn-primary btn-sm"><i class="fas fa-edit"></i> Edit</a>
                <a href="deactivate_personnel.php?id=<?= $p['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Deactivate?')"><i class="fas fa-ban"></i> Deactivate</a>
              </td>
            </tr>
          <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <div class="section-card">
    <div class="section-header">
      <h2><i class="fas fa-tasks"></i> Task Monitoring</h2>
      <a href="assign_task.php" class="btn btn-success btn-sm"><i class="fas fa-plus"></i> Assign</a>
    </div>
    <div class="section-body" style="padding:0;">
      <table class="data-table">
        <thead><tr><th>Task</th><th>Assigned To</th><th>Status</th></tr></thead>
        <tbody>
          <?php if (empty($tasks)): ?>
            <tr><td colspan="3" style="text-align:center;color:var(--muted);padding:20px;">No tasks found.</td></tr>
          <?php else: foreach ($tasks as $task): ?>
            <tr>
              <td><?= htmlspecialchars($task['task']) ?></td>
              <td><?= htmlspecialchars($task['assigned']) ?></td>
              <td><span class="badge <?= $task['status']==='Completed'?'badge-done':'badge-pending' ?>"><?= htmlspecialchars($task['status']) ?></span></td>
            </tr>
          <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</main>
<script>
  const s=document.getElementById('sidebar'),h=document.getElementById('hamburger'),b=document.getElementById('backdrop');
  h.onclick=()=>{s.classList.toggle('open');b.classList.toggle('show');};
  b.onclick=()=>{s.classList.remove('open');b.classList.remove('show');};
</script>
</body>
</html>
