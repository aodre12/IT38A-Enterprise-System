<?php
// reports.php
session_start();
require 'config.php'; // your DB connection, if you later switch to a database

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

// Load existing reports
$reportsFile = 'reports.json';
if (file_exists($reportsFile)) {
    $reports = json_decode(file_get_contents($reportsFile), true);
} else {
    $reports = [];
}

// Handle new report submission
$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $date  = trim($_POST['date']  ?? '');

    if ($title === '' || $date === '') {
        $error = 'Both title and date are required.';
    } else {
        $new = [
            'title' => $title,
            'date'  => $date,
            'url'   => '#'  // or you could collect a URL field
        ];
        $reports[] = $new;
        file_put_contents($reportsFile, json_encode($reports, JSON_PRETTY_PRINT));
        $success = 'Report added.';
    }
}

$username = $_SESSION['username'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Reports | UtilityTrack</title>
  <style>
    body { margin:0; font-family:'Segoe UI',sans-serif; background:#fef6ec; display:flex; flex-direction:column; align-items:center; }
    .container { position:relative; background:#fff; width:500px; max-width:90%; padding:40px 30px; border-radius:20px; box-shadow:0 0 12px rgba(0,0,0,0.1); margin:60px 0; }
    .header-wave { position:absolute; top:0; left:0; width:100%; height:100px; background:#001F54; border-top-left-radius:20px; border-top-right-radius:20px; clip-path:ellipse(100% 80% at 50% 0%); }
    h2 { margin-top:100px; font-size:28px; color:#000; text-align:center; }
    form { margin-top:30px; }
    label { display:block; margin-top:15px; font-weight:bold; }
    input[type="text"], input[type="date"] {
      width:100%; padding:10px; margin-top:5px; border:1px solid #ccc; border-radius:8px;
    }
    button { width:100%; padding:12px; margin-top:20px; background:#0056b3; color:#fff; border:none; border-radius:8px; font-size:16px; cursor:pointer; }
    button:hover { background:#003d80; }
    .message { text-align:center; margin-top:10px; }
    .error { color:red; }
    .success { color:green; }
    .report-list { margin-top:30px; list-style:none; padding:0; }
    .report-list li { padding:12px; border-bottom:1px solid #eee; display:flex; justify-content:space-between; align-items:center; }
    .report-list li:last-child { border:none; }
    .report-list a { text-decoration:none; color:#007bff; font-weight:600; padding:6px 12px; border:1px solid #007bff; border-radius:6px; transition:background .3s,color .3s; }
    .report-list a:hover { background:#007bff; color:#fff; }
    .back-link { display:block; text-align:center; margin-top:20px; color:#0056b3; text-decoration:none; font-weight:bold; }
    .back-link:hover { text-decoration:underline; }
  </style>
</head>
<body>
  <div class="container">
    <div class="header-wave"></div>
    <h2>Reports</h2>

    <?php if ($error): ?>
      <div class="message error"><?= htmlspecialchars($error) ?></div>
    <?php elseif ($success): ?>
      <div class="message success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <!-- Add New Report Form -->
    <form method="POST">
      <label for="title">Report Title</label>
      <input type="text" id="title" name="title" required>

      <label for="date">Date</label>
      <input type="date" id="date" name="date" required>

      <button type="submit">Add Report</button>
    </form>

    <!-- Existing Reports List -->
    <ul class="report-list">
      <?php if (empty($reports)): ?>
        <li>No reports available.</li>
      <?php else: ?>
        <?php foreach ($reports as $r): ?>
          <li>
            <span><?= htmlspecialchars($r['title']) ?> <small>(<?= htmlspecialchars($r['date']) ?>)</small></span>
            <a href="<?= htmlspecialchars($r['url']) ?>">View</a>
          </li>
        <?php endforeach; ?>
      <?php endif; ?>
    </ul>

    <a class="back-link" href="user_dashboard.php">← Back to Dashboard</a>
  </div>
</body>
</html>
