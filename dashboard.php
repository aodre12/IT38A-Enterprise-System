<!-- dashboard.php -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Dashboard</title>
  <link rel="stylesheet" href="style.css">
  <style>
    :root {
      --primary-color: #4A90E2;
      --background-color: #FFF6EB;
      --tile-background: #ffffff;
      --sidebar-text: #1a1a1a;
      --border-radius: 12px;
      --font-main: 'Arial', sans-serif;
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
      flex-direction: column;
      min-height: 100vh;
    }

    /* Top Bar */
    .top-bar {
      background: var(--primary-color);
      height: 60px;
      display: flex;
      align-items: center;
      padding: 0 20px;
      justify-content: space-between;
      color: white;
    }

    .menu-icon {
      font-size: 26px;
      cursor: pointer;
    }

    .search-bar {
      background: white;
      padding: 8px 15px;
      border-radius: 20px;
      width: 250px;
      border: none;
      outline: none;
      font-size: 14px;
    }

    .avatar {
      width: 40px;
      height: 40px;
      background: #d0e6ff;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 20px;
      cursor: pointer;
    }

    /* Main Content */
    .dashboard {
      padding: 20px;
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      grid-gap: 20px;
      margin-top: 20px;
    }

    .tile {
      background: var(--tile-background);
      border-radius: var(--border-radius);
      padding: 20px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    .tile h3 {
      margin-bottom: 10px;
      font-size: 18px;
    }

    .tile ul {
      list-style: none;
      padding-left: 0;
    }

    .tile ul li {
      padding: 6px 0;
      font-size: 14px;
    }

    .checklist li::before {
      content: "✔️ ";
      margin-right: 6px;
      color: green;
    }

    .small-tile {
      text-align: center;
      font-size: 16px;
      padding: 25px 10px;
    }
  </style>
</head>
<body>

  <!-- Top Navigation Bar -->
  <div class="top-bar">
    <div class="menu-icon">☰</div>
    <input class="search-bar" type="text" placeholder="Search...">
    <div class="avatar">👤</div>
  </div>

  <!-- Dashboard Grid Layout -->
  <div class="dashboard">
    <!-- Dashboard Overview -->
    <div class="tile">
      <h3>Dashboard</h3>
      <ul>
        <li>Work order overview</li>
        <li>Assign Task</li>
        <li>Task Status</li>
      </ul>
    </div>

    <!-- Work Orders -->
    <div class="tile">
      <h3>Work Orders</h3>
      <ul>
        <li>Order #1234</li>
        <li>Order #1235</li>
        <li>Order #1236</li>
      </ul>
    </div>

    <!-- Pending Tasks -->
    <div class="tile">
      <h3>Pending Task</h3>
      <ul>
        <li>Electrical Setup</li>
        <li>Plumbing Repair</li>
      </ul>
    </div>

    <!-- Completed Tasks -->
    <div class="tile">
      <h3>Completed Task</h3>
      <ul class="checklist">
        <li>Floor Polishing</li>
        <li>HVAC Installation</li>
        <li>Painting</li>
      </ul>
    </div>

    <!-- Small quick access tiles -->
    <div class="tile small-tile">
      <h4>Work Orders</h4>
    </div>

    <div class="tile small-tile">
      <h4>Recent Work Orders</h4>
    </div>

    <div class="tile small-tile">
      <h4>Create Work Orders</h4>
    </div>

    <!-- Reports -->
    <div class="tile" style="grid-column: span 3;">
      <h3>Reports</h3>
      <p>Generate and view your work reports here.</p>
    </div>

  </div>

</body>
</html>
