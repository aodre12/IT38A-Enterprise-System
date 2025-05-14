<?php
// Start the session
session_start();

// Load tasks from the tasks.json file
$tasksFile = 'tasks.json';
if (file_exists($tasksFile)) {
    $tasksData = json_decode(file_get_contents($tasksFile), true);
} else {
    // Initialize an empty task array if no file exists
    $tasksData = ['pending' => [], 'completed' => []];
}

// Handle marking a task as completed
if (isset($_POST['task_id']) && isset($_POST['action']) && $_POST['action'] == 'complete') {
    $taskId = $_POST['task_id'];
    // Mark the task as completed
    $taskKey = array_search($taskId, array_column($tasksData['pending'], 'id'));
    if ($taskKey !== false) {
        $task = $tasksData['pending'][$taskKey];
        unset($tasksData['pending'][$taskKey]);
        $tasksData['completed'][] = $task;
    }
    // Save the updated tasks data back to the file
    file_put_contents($tasksFile, json_encode($tasksData));
    header("Location: dashboard.php");
    exit;
}

// Handle adding a new task
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['new_task'])) {
    $newTask = [
        'id' => uniqid(),
        'task' => $_POST['new_task'],
        'created_at' => date('Y-m-d H:i:s'),
    ];
    $tasksData['pending'][] = $newTask;
    file_put_contents($tasksFile, json_encode($tasksData));
    header("Location: dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Dashboard</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #fef6ec;
      margin: 0;
    }

    .header {
      background-color: #cce5ff;
      padding: 10px 20px;
      display: flex;
      align-items: center;
      border-bottom: 1px solid #aaa;
      position: sticky;
      top: 0;
      z-index: 900;
    }

    .header h2 {
      margin: 0;
      color: #003049;
      font-size: 1.5rem;
    }

    .search-bar {
      margin: 20px;
      display: flex;
      justify-content: center;
    }

    .search-bar input {
      padding: 10px;
      border-radius: 15px;
      border: 1px solid #ccc;
      width: 300px;
    }

    .content {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
      gap: 20px;
      padding: 20px;
      margin-left: 250px;
      transition: margin-left 0.3s;
    }

    .card {
      background-color: white;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }

    .card h3 {
      margin-top: 0;
      margin-bottom: 10px;
    }

    .task-list, .work-list {
      list-style: none;
      padding-left: 0;
    }

    .task-list li, .work-list li {
      margin: 5px 0;
      padding-left: 10px;
      border-left: 3px solid #ccc;
    }

    .checkbox {
      margin-right: 5px;
    }

    .card button {
      padding: 10px;
      background-color: #007bff;
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      width: 100%;
      margin-top: 10px;
    }

    .card button:hover {
      background-color: #0056b3;
    }

    .work-orders-list {
      display: none;
      padding: 15px;
      background-color: white;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
      margin-top: 20px;
    }

    .work-orders-list ul {
      list-style: none;
      padding-left: 0;
    }

    .work-orders-list li {
      padding: 10px;
      border-bottom: 1px solid #eee;
    }

    /* Hamburger styles */
    .hamburger {
      font-size: 24px;
      cursor: pointer;
      background: none;
      border: none;
      color: #003049;
      z-index: 2001;
      position: fixed;
      left: 20px;
      top: 20px;
      display: block;
    }
    @media (min-width: 901px) {
      .hamburger {
        display: none;
      }
    }

    /* Sidebar styles */
    .sidebar {
      width: 250px;
      background-color: #ffffff;
      border-right: 1px solid #e0e0e0;
      display: flex;
      flex-direction: column;
      justify-content: flex-start;
      position: fixed;
      height: 100vh;
      padding: 60px 20px 20px 20px; /* Add top padding for hamburger */
      left: 0;
      top: 0;
      transition: transform 0.3s ease;
      z-index: 1000;
    }
    .sidebar.closed {
      transform: translateX(-100%);
    }
    .sidebar .profile {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 20px 0;
      border-bottom: 1px solid #eee;
    }
    .sidebar .profile img {
      width: 40px;
      height: 40px;
      border-radius: 50%;
    }
    .sidebar .profile-info {
      display: flex;
      flex-direction: column;
    }
    .sidebar .profile-info .name {
      font-weight: bold;
      font-size: 14px;
    }
    .sidebar .profile-info .email {
      font-size: 12px;
      color: #777;
    }
    .sidebar .menu-section {
      flex-grow: 1;
      margin-top: 30px;
    }
    .sidebar .menu-group h4 {
      font-size: 12px;
      color: #999;
      margin: 20px 0 10px;
      text-transform: uppercase;
    }
    .sidebar .menu-group a {
      text-decoration: none;
      color: #003049;
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 10px 0;
      transition: 0.3s;
      position: relative;
    }
    .sidebar .menu-group a:hover {
      color: #219ebc;
    }
    .sidebar .badge {
      font-size: 12px;
      padding: 2px 8px;
      border-radius: 12px;
      margin-left: auto;
    }
    .sidebar .green {
      background-color: #90be6d;
      color: white;
    }
    .sidebar .yellow {
      background-color: #f9c74f;
      color: white;
    }
    @media (max-width: 900px) {
      .sidebar {
        transform: translateX(-100%);
      }
      .sidebar.open {
        transform: translateX(0);
      }
      .content {
        margin-left: 0 !important;
      }
    }
    @media (max-width: 900px) {
      .content {
        margin-left: 0;
      }
    }

/* Optional: backdrop overlay */
    #backdrop {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      height: 100vh;
      width: 100vw;
      background: rgba(0, 0, 0, 0.3);
      z-index: 999;
    }

    #backdrop.show {
      display: block;
    }
  </style>
</head>
<body>
  <!-- Backdrop for mobile sidebar -->
  <div id="backdrop"></div>

  </style>
</head>
<body>
  <!-- Sidebar -->
  <div class="sidebar closed" id="sidebar">
    <div class="profile">
      <img src="https://i.pravatar.cc/40" alt="Profile" />
      <div class="profile-info">
        <p class="name">John Doe</p>
        <p class="email">doeejon@gmail.com</p>
      </div>
    </div>
    <div class="menu-section">
      <div class="menu-group">
        <h4>MAIN</h4>
        <a href="#"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a href="#"><i class="fas fa-clipboard-list"></i> Job Schedule</a>
        <a href="#"><i class="fas fa-tasks"></i> Work Orders</a>
        <a href="#"><i class="fas fa-boxes"></i> Asset Inventory</a>
        <a href="#"><i class="fas fa-chart-line"></i> Reports</a>
        <a href="#"><i class="fas fa-calendar-alt"></i> Calendar</a>
      </div>
      <div class="menu-group">
        <h4>ACCOUNT</h4>
        <a href="#"><i class="fas fa-bell"></i> Notifications <span class="badge green">24</span></a>
        <a href="#"><i class="fas fa-comments"></i> Chat <span class="badge yellow">8</span></a>
        <a href="#"><i class="fas fa-life-ring"></i> Help & Support</a>
        <a href="#"><i class="fas fa-cog"></i> Settings</a>
      </div>
    </div>
  </div>
  <!-- Header with Hamburger -->
  <div class="header">
    <button class="hamburger" id="hamburger"><i class="fas fa-bars"></i></button>
    <h2>DASHBOARD</h2>
  </div>
  <div class="search-bar">
    <input type="text" placeholder="Search...">
  </div>
  <main class="content">
    <div class="card">
      <h3>Dashboard</h3>
      <ul class="task-list">
        <li>Work order overview</li>
        <li>Assign Task</li>
        <li>Task Status</li>
      </ul>
    </div>

    <div class="card">
      <h3>Work Orders</h3>
      <ul class="work-list">
        <li><a href="show_work_orders.php"><button>Show Work Orders</button></a></li>
      </ul>
    </div>

    <div class="card">
      <h3>Pending Tasks</h3>
      <ul class="task-list">
        <?php foreach ($tasksData['pending'] as $task): ?>
          <li>
            <form method="POST" style="display:inline;">
              <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
              <input type="hidden" name="action" value="complete">
              <button type="submit">✔ Complete</button>
            </form>
            <?php echo htmlspecialchars($task['task']); ?>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>

    <div class="card">
      <h3>Completed Tasks</h3>
      <ul class="task-list">
        <?php foreach ($tasksData['completed'] as $task): ?>
          <li><?php echo htmlspecialchars($task['task']); ?></li>
        <?php endforeach; ?>
      </ul>
    </div>

    <div class="card">
      <h3>Create Work Orders</h3>
      <a href="create_work.php"><button>➕ New Order</button></a>
    </div>

    <div class="card" style="grid-column: 1 / -1;">
      <h3>Reports</h3>
      <p>No reports available.</p>
    </div>
  </main>
  <script>
    // Hamburger toggle for sidebar
    const hamburger = document.getElementById('hamburger');
    const sidebar = document.getElementById('sidebar');
    hamburger.addEventListener('click', function() {
      sidebar.classList.toggle('closed');
      sidebar.classList.toggle('open');
    });
    // Optional: close sidebar when clicking outside on small screens
    document.addEventListener('click', function(e) {
      if (window.innerWidth <= 900 && !sidebar.contains(e.target) && !hamburger.contains(e.target)) {
        sidebar.classList.add('closed');
        sidebar.classList.remove('open');
      }
    });
  </script>
</body>
</html>