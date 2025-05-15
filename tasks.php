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

// Load tasks
$tasksFile = 'tasks.json';
if (file_exists($tasksFile)) {
    $tasksData = json_decode(file_get_contents($tasksFile), true);
} else {
    $tasksData = ['pending'=>[], 'completed'=>[]];
}

// Handle marking a task complete
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['action'])) {
    if ($_POST['action']==='complete' && isset($_POST['task_id'])) {
        $id = $_POST['task_id'];
        foreach ($tasksData['pending'] as $i => $t) {
            if ($t['id'] === $id) {
                $tasksData['completed'][] = $t;
                array_splice($tasksData['pending'], $i, 1);
                break;
            }
        }
    }
    // Handle new task
    if ($_POST['action']==='add' && !empty(trim($_POST['new_task']))) {
        $tasksData['pending'][] = [
            'id'=>uniqid(),
            'task'=>trim($_POST['new_task']),
            'created_at'=>date('Y-m-d H:i:s')
        ];
    }
    file_put_contents($tasksFile, json_encode($tasksData, JSON_PRETTY_PRINT));
    header('Location: tasks.php');
    exit;
}

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
      <?php foreach ($tasksData['pending'] as $t): ?>
      <li>
        <span><?= htmlspecialchars($t['task']) ?></span>
        <form method="POST" action="tasks.php">
          <input type="hidden" name="action" value="complete">
          <input type="hidden" name="task_id" value="<?= htmlspecialchars($t['id']) ?>">
          <button type="submit">✓</button>
        </form>
      </li>
      <?php endforeach; ?>

      <?php if (empty($tasksData['pending'])): ?>
        <li><em>No pending tasks.</em></li>
      <?php endif; ?>

      <?php foreach ($tasksData['completed'] as $t): ?>
      <li style="opacity:0.6;">
        <span><?= htmlspecialchars($t['task']) ?> (Done)</span>
      </li>
      <?php endforeach; ?>

      <?php if (empty($tasksData['completed'])): ?>
        <li><em>No completed tasks.</em></li>
      <?php endif; ?>
    </ul>

    <a class="back-link" href="user_dashboard.php">← Back to Dashboard</a>
  </div>
</body>
</html>
