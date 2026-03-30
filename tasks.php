<?php
// tasks.php
session_start();
require 'config.php';

// Redirect admins
if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    header('Location: admin_dashboard.php');
    exit;
}
// Require login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// User id for DB operations
$userId = $_SESSION['user_id'];

// Handle task actions (DB-backed)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action === 'complete' && isset($_POST['task_id'])) {
        $taskId = $_POST['task_id'];

        // Only allow completing tasks assigned to this user
        $stmt = $conn->prepare("UPDATE tasks SET status='Completed' WHERE id=? AND assigned_to=? AND status!='Completed'");
        $stmt->bind_param("ii", $taskId, $userId);
        $stmt->execute();

        // Optional audit trail
        $stmt->close();
        $audit = $conn->prepare("INSERT INTO audit_logs (user_id, action) VALUES (?, ?)");
        $auditMsg = 'Completed a task (ID: ' . $taskId . ')';
        $audit->bind_param("is", $userId, $auditMsg);
        $audit->execute();
        $audit->close();

        header('Location: tasks.php');
        exit;
    }

    if ($action === 'add' && !empty(trim($_POST['new_task']))) {
        $taskText = trim($_POST['new_task']);

        $stmt = $conn->prepare("INSERT INTO tasks (task, assigned_to, status) VALUES (?, ?, 'Pending')");
        $stmt->bind_param("si", $taskText, $userId);
        $stmt->execute();
        $stmt->close();

        // Optional audit trail
        $audit = $conn->prepare("INSERT INTO audit_logs (user_id, action) VALUES (?, ?)");
        $auditMsg = 'Added a new task: ' . $taskText;
        $audit->bind_param("is", $userId, $auditMsg);
        $audit->execute();
        $audit->close();

        header('Location: tasks.php');
        exit;
    }
}

// Load tasks from DB
$stmt = $conn->prepare("SELECT id, task, status FROM tasks WHERE assigned_to=? ORDER BY id DESC");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$pendingTasks = [];
$completedTasks = [];
while ($row = $result->fetch_assoc()) {
    if (isset($row['status']) && $row['status'] === 'Completed') {
        $completedTasks[] = $row;
    } else {
        $pendingTasks[] = $row;
    }
}
$stmt->close();

$username = $_SESSION['username'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Tasks | UtilityTrack</title>
  <style>
    body { margin:0; font-family:'Segoe UI',sans-serif; background:#fef6ec; display:flex; flex-direction:column; align-items:center; }
    .container { position:relative; background:#fff; width:500px; max-width:90%; padding:40px 30px; border-radius:20px; box-shadow:0 0 12px rgba(0,0,0,0.1); margin:60px 0; }
    .header-wave { position:absolute; top:0; left:0; width:100%; height:100px; background:#001F54; border-top-left-radius:20px; border-top-right-radius:20px; clip-path:ellipse(100% 80% at 50% 0%); }
    h2 { margin-top:100px; font-size:28px; color:#000; text-align:center; }
    form { margin-top:30px; display:flex; gap:10px; }
    input[type="text"] { flex:1; padding:10px; border:1px solid #ccc; border-radius:8px; font-size:16px; }
    button { padding:10px 15px; background:#0056b3; color:#fff; border:none; border-radius:8px; cursor:pointer; }
    button:hover { background:#003d80; }
    .task-list { margin-top:30px; list-style:none; padding:0; }
    .task-list li { padding:12px; border-bottom:1px solid #eee; display:flex; justify-content:space-between; align-items:center; }
    .task-list li:last-child { border:none; }
    .task-list form { margin:0; }
    .back-link { display:block; text-align:center; margin-top:20px; color:#0056b3; text-decoration:none; font-weight:bold; }
    .back-link:hover { text-decoration:underline; }
  </style>
</head>
<body>
  <div class="container">
    <div class="header-wave"></div>
    <h2>My Tasks</h2>

    <!-- Add New Task -->
    <form method="POST" action="tasks.php">
      <input type="hidden" name="action" value="add">
      <input type="text" name="new_task" placeholder="New task description..." required>
      <button type="submit">Add</button>
    </form>

    <!-- Pending & Completed Tasks -->
    <ul class="task-list">
      <?php foreach ($pendingTasks as $t): ?>
      <li>
        <span><?= htmlspecialchars($t['task']) ?></span>
        <form method="POST" action="tasks.php">
          <input type="hidden" name="action" value="complete">
          <input type="hidden" name="task_id" value="<?= htmlspecialchars($t['id']) ?>">
          <button type="submit">✓</button>
        </form>
      </li>
      <?php endforeach; ?>

      <?php if (empty($pendingTasks)): ?>
        <li><em>No pending tasks.</em></li>
      <?php endif; ?>

      <?php foreach ($completedTasks as $t): ?>
      <li style="opacity:0.6;">
        <span><?= htmlspecialchars($t['task']) ?> (Done)</span>
      </li>
      <?php endforeach; ?>

      <?php if (empty($completedTasks)): ?>
        <li><em>No completed tasks.</em></li>
      <?php endif; ?>
    </ul>

    <a class="back-link" href="user_dashboard.php">← Back to Dashboard</a>
  </div>
</body>
</html>
