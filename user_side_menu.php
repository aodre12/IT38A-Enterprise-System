<!-- user_side_menu.php -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Side Menu</title>
  <link rel="stylesheet" href="css/style.css">
  <style>
    :root {
      --primary-color: #1f3fae; /* Deep Blue */
      --background-color: #FFF6EB;
      --text-white: #ffffff;
      --tile-background: #ffffff;
      --font-main: 'Arial', sans-serif;
      --border-radius: 12px;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: var(--font-main);
    }

    body {
      background: var(--background-color);
      display: flex;
      min-height: 100vh;
    }

    /* Sidebar */
    .sidebar {
      background: var(--primary-color);
      width: 250px;
      height: 100vh;
      padding: 20px 15px;
      display: flex;
      flex-direction: column;
      border-top-right-radius: var(--border-radius);
      border-bottom-right-radius: var(--border-radius);
      color: var(--text-white);
    }

    .sidebar-header {
      display: flex;
      align-items: center;
      margin-bottom: 40px;
    }

    .sidebar-header img {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      margin-right: 10px;
      background: #d0e6ff;
      padding: 5px;
    }

    .sidebar-header span {
      font-size: 14px;
      font-weight: 400;
    }

    .menu-item {
      margin-bottom: 20px;
      font-size: 18px;
      font-weight: bold;
      cursor: pointer;
      transition: 0.3s;
    }

    .menu-item:hover {
      text-decoration: underline;
    }

    /* Main Dashboard Area */
    .main {
      flex: 1;
      padding: 30px;
    }

    .work-orders, .completed-tasks {
      background: var(--tile-background);
      border-radius: var(--border-radius);
      padding: 20px;
      margin-bottom: 20px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .tiles {
      display: flex;
      gap: 20px;
    }

    .tile {
      background: var(--tile-background);
      flex: 1;
      padding: 20px;
      text-align: center;
      border-radius: var(--border-radius);
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
  </style>
</head>

<body>

  <!-- Sidebar Section -->
  <div class="sidebar">
    <div class="sidebar-header">
      <img src="avatar.png" alt="User Avatar">
      <div>
        <span>WELCOME,</span><br>
        <span><strong>USERNAME</strong></span>
      </div>
    </div>

    <div class="menu-item">Home</div>
    <div class="menu-item">My Profile</div>
    <div class="menu-item">My Orders</div>
    <div class="menu-item">Notifications</div>
    <div class="menu-item">Saved Reports</div>
    <div class="menu-item">Settings</div>
    <div class="menu-item">Help & Support</div>
    <div class="menu-item">Feedback</div>
    <div class="menu-item">Logout</div>
  </div>

  <!-- Main Section -->
  <div class="main">
    <div class="work-orders">
      <h3>Work Orders</h3>
      <ul>
        <li>Order #1001</li>
        <li>Order #1002</li>
        <li>Order #1003</li>
      </ul>
    </div>

    <div class="completed-tasks">
      <h3>Completed Tasks</h3>
      <ul>
        <li>✔️ Maintenance Task 1</li>
        <li>✔️ Maintenance Task 2</li>
        <li>✔️ Maintenance Task 3</li>
      </ul>
    </div>

    <div class="tiles">
      <div class="tile">Recent Orders</div>
      <div class="tile">Create Work Orders</div>
    </div>
  </div>

</body>
</html>
