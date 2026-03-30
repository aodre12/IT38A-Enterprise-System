<?php
session_start();
require 'config.php';  // DB connection
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

$userId = $_SESSION['user_id'];

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
  <title>User Dashboard</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
  <style>
    body { margin:0; font-family:Arial,sans-serif; background:#fef6ec; }
    .header {
      background:#cce5ff; padding:10px 20px; display:flex; align-items:center;
      border-bottom:1px solid #aaa; position:sticky; top:0; z-index:900;
    }
    .header h2 { margin:0; color:#003049; font-size:1.5rem; }
    .hamburger {
      font-size:24px; cursor:pointer; background:none; border:none; color:#003049;
      position:fixed; left:20px; top:20px; z-index:2001;
    }
    @media(min-width:901px){ .hamburger{display:none;} }

    /* Sidebar */
    .sidebar {
      width:250px; background:#ffffff; border-right:1px solid #e0e0e0;
      position:fixed; top:0; left:0; height:100vh; padding:60px 20px 20px;
      box-shadow:2px 0 5px rgba(0,0,0,0.1); transform:translateX(-100%);
      transition:transform .3s; z-index:1000;
    }
    .sidebar.open{ transform:translateX(0); }
    .sidebar .profile{ display:flex; align-items:center; gap:10px; margin-bottom:30px; }
    .sidebar .profile img{ width:40px; height:40px; border-radius:50%; }
    .sidebar .profile-info .name{ font-weight:bold; font-size:14px; }
    .sidebar .menu-group a {
      display:flex; align-items:center; gap:12px; padding:10px 0; color:#003049;
      text-decoration:none; transition:color .3s;
    }
    .sidebar .menu-group a:hover{ color:#219ebc; }
    .sidebar .menu-group h4 {
      font-size:12px; color:#999; margin:20px 0 10px; text-transform:uppercase;
    }
    #backdrop {
      display:none; position:fixed; top:0; left:0; width:100vw; height:100vh;
      background:rgba(0,0,0,.3); z-index:999;
    }
    #backdrop.show{ display:block; }

    /* Main content */
    .content {
      margin-left:0; padding:20px; transition:margin-left .3s;
    }
    @media(min-width:901px){
      .sidebar{transform:translateX(0);} 
      .content{margin-left:270px;}
    }
    .card-container {
      display:grid; grid-template-columns:repeat(auto-fill,minmax(280px,1fr));
      gap:20px; padding:20px;
    }
    .card {
      background:#fff; padding:20px; border-radius:10px;
      box-shadow:0 0 10px rgba(0,0,0,0.1);
    }
    .card h3 { margin-top:0; margin-bottom:15px; color:#003049; }
    .card ul { list-style:none; padding:0; }
    .card li { padding:10px 0; border-bottom:1px solid #eee; }
    .card li:last-child { border:none; }
    .card button, .card a.button {
      display:inline-block; padding:10px; width:100%; text-align:center;
      background:#007bff; color:white; border:none; border-radius:5px;
      text-decoration:none; cursor:pointer; margin-top:10px;
    }
    .card button:hover, .card a.button:hover { background:#0056b3; }
  </style>
</head>
<body>

  <div id="backdrop"></div>
  <button class="hamburger" id="hamburger"><i class="fas fa-bars"></i></button>

  <div class="sidebar" id="sidebar">
    <div class="profile">
      <img src="https://i.pravatar.cc/40" alt="Avatar">
      <div class="profile-info">
        <p class="name"><?= htmlspecialchars($username) ?></p>
      </div>
    </div>
    <div class="menu-section">
      <div class="menu-group">
        <h4>Menu</h4>
        <a href="user_dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
        <a href="work_orders.php"><i class="fas fa-list"></i> My Work Orders</a>
        <a href="tasks.php"><i class="fas fa-tasks"></i> My Tasks</a>
        <a href="reports.php"><i class="fas fa-chart-line"></i> Reports</a>
        <a href="feedback.php"><i class="fas fa-comment-dots"></i> Submit Feedback</a>
      </div>
      <div class="menu-group">
        <h4>Account</h4>
        <a href="profile.php"><i class="fas fa-user"></i> Profile</a>
        <a href="reset_password.php"><i class="fas fa-key"></i> Reset Password</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
      </div>
    </div>
  </div>

  <header class="header"><h2>Dashboard</h2></header>

  <main class="content">
    <div class="card-container">
      <!-- My Tasks -->
      <div class="card">
        <h3>My Tasks</h3>
        <ul>
          <?php foreach($pendingTasks as $t): ?>
            <li><?= htmlspecialchars($t['task']) ?> <span style="color:#999;font-size:12px;">(Pending)</span></li>
          <?php endforeach; ?>
          <?php foreach($completedTasks as $t): ?>
            <li><?= htmlspecialchars($t['task']) ?> <span style="color:green;font-size:12px;">(Done)</span></li>
          <?php endforeach; ?>
          <?php if(empty($pendingTasks) && empty($completedTasks)): ?>
            <li>No tasks found.</li>
          <?php endif; ?>
        </ul>
      </div>

      <!-- Reports -->
      <div class="card">
        <h3>Reports</h3>
        <a href="reports.php" class="button">View Reports</a>
      </div>

      <!-- Submit Feedback -->
      <div class="card">
        <h3>Feedback</h3>
        <a href="feedback.php" class="button">Send Feedback</a>
      </div>
    </div>
  </main>

  <script>
    const sidebar = document.getElementById('sidebar');
    const hamburger = document.getElementById('hamburger');
    const backdrop  = document.getElementById('backdrop');

    hamburger.onclick = () => {
      sidebar.classList.toggle('open');
      backdrop.classList.toggle('show');
    };
    backdrop.onclick = () => {
      sidebar.classList.remove('open');
      backdrop.classList.remove('show');
    };
  </script>
</body>
</html>
