<?php
session_start();

// Only allow the developer (adjust as needed)
$developer_username = 'developer'; // Change to your actual username or email
if (!isset($_SESSION['username']) || $_SESSION['username'] !== $developer_username) {
    echo "<h2 style='color:red;text-align:center;margin-top:50px;'>Access Denied. Developer Only.</h2>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Developer Admin Dashboard</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
  <style>
    body { margin: 0; font-family: 'Segoe UI', Arial, sans-serif; background: #f6f7fb; }
    .topbar {
      background: #fff; display: flex; align-items: center; justify-content: space-between;
      padding: 0 30px; height: 60px; box-shadow: 0 1px 4px rgba(0,0,0,0.04);
    }
    .topbar .left, .topbar .right { display: flex; align-items: center; gap: 20px; }
    .topbar .profile { display: flex; align-items: center; gap: 10px; }
    .topbar .profile img { width: 32px; height: 32px; border-radius: 50%; }
    .topbar input[type='text'] {
      padding: 7px 15px; border-radius: 20px; border: 1px solid #ddd; outline: none;
    }
    .sidebar {
      width: 230px; background: #23272e; color: #fff; position: fixed; top: 0; left: 0; height: 100vh;
      display: flex; flex-direction: column; padding-top: 70px; z-index: 100;
    }
    .sidebar .menu, .sidebar .submenu { list-style: none; padding: 0; margin: 0; }
    .sidebar .menu li { padding: 13px 30px; cursor: pointer; transition: background 0.2s; }
    .sidebar .menu li:hover, .sidebar .menu li.active { background: #181b20; }
    .sidebar .menu li i { margin-right: 10px; }
    .sidebar .submenu { padding-left: 20px; background: #23272e; }
    .sidebar .submenu li { font-size: 15px; }
    .main-content {
      margin-left: 230px; padding: 30px; min-height: 100vh;
    }
    .cards { display: flex; gap: 20px; margin-bottom: 30px; }
    .card {
      background: #fff; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.04);
      padding: 20px 30px; flex: 1; display: flex; align-items: center; gap: 15px;
    }
    .card i { font-size: 2rem; }
    .card .info { display: flex; flex-direction: column; }
    .card .info span { font-size: 13px; color: #888; }
    .card .info strong { font-size: 1.3rem; }
    .alert {
      background: #fff3f3; color: #d32f2f; border: 1px solid #f8d7da;
      padding: 12px 20px; border-radius: 6px; margin-bottom: 20px;
    }
    .table-section { background: #fff; border-radius: 10px; padding: 20px; }
    table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    th, td { padding: 12px 10px; text-align: left; }
    th { background: #f6f7fb; }
    tr:nth-child(even) { background: #f9f9f9; }
    .btn-edit, .btn-delete {
      padding: 5px 12px; border: none; border-radius: 4px; color: #fff; cursor: pointer; font-size: 13px;
    }
    .btn-edit { background: #1976d2; margin-right: 5px; }
    .btn-delete { background: #d32f2f; }
    @media (max-width: 900px) {
      .sidebar { width: 60px; }
      .main-content { margin-left: 60px; }
      .sidebar .menu li { padding: 13px 10px; }
      .cards { flex-direction: column; }
    }
  </style>
</head>
<body>
  <div class="topbar">
    <div class="left">
      <a href="#"><i class="fas fa-home"></i> Home</a>
      <a href="#"><i class="fas fa-bars"></i> Contents</a>
      <a href="#"><i class="fas fa-th"></i> Categories</a>
      <a href="#"><i class="fas fa-cog"></i> Settings</a>
    </div>
    <div class="right">
      <input type="text" placeholder="Search content..">
      <div class="profile">
        <img src="https://i.pravatar.cc/32" alt="profile">
        <span><?php echo htmlspecialchars($_SESSION['username']); ?></span>
      </div>
    </div>
  </div>
  <div class="sidebar">
    <ul class="menu">
      <li class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</li>
      <li><i class="fas fa-shopping-cart"></i> Commance</li>
      <li><i class="fas fa-chart-line"></i> Analytics</li>
      <li><i class="fas fa-coins"></i> Crypto</li>
      <li><i class="fas fa-headset"></i> Helpdesk</li>
      <li><i class="fas fa-desktop"></i> Monitoring</li>
      <li><i class="fas fa-dumbbell"></i> Fitness</li>
      <li><i class="fas fa-th-large"></i> Application</li>
      <li><i class="fas fa-cube"></i> Elements</li>
      <li><i class="fas fa-wpforms"></i> Forms</li>
      <li><i class="fas fa-plug"></i> Plugins</li>
      <li><i class="fas fa-table"></i> Datagrid</li>
      <li><i class="fas fa-cogs"></i> Settings</li>
    </ul>
  </div>
  <div class="main-content">
    <div style="display: flex; align-items: center; gap: 20px; margin-bottom: 20px;">
      <button style="background:#23272e;color:#fff;border:none;padding:10px 18px;border-radius:6px;font-weight:bold;cursor:pointer;">
        + New Item
      </button>
      <h2 style="margin:0;">Add new post</h2>
      <button style="margin-left:10px;background:#f6f7fb;border:none;padding:8px 15px;border-radius:6px;cursor:pointer;">
        <i class="fas fa-plus"></i> Add Content
      </button>
      <button style="background:#f6f7fb;border:none;padding:8px 15px;border-radius:6px;cursor:pointer;">
        <i class="fas fa-cog"></i> Settings
      </button>
    </div>
    <div class="cards">
      <div class="card"><i class="fas fa-shopping-bag" style="color:#ff9800"></i>
        <div class="info"><span>Total Sales</span><strong>$2,456</strong></div>
      </div>
      <div class="card"><i class="fas fa-receipt" style="color:#673ab7"></i>
        <div class="info"><span>Total Expenses</span><strong>$3,326</strong></div>
      </div>
      <div class="card"><i class="fas fa-user-friends" style="color:#009688"></i>
        <div class="info"><span>Total Visitors</span><strong>5,325</strong></div>
      </div>
      <div class="card"><i class="fas fa-file-invoice" style="color:#7c3aed"></i>
        <div class="info"><span>Total Orders</span><strong>1,326</strong></div>
      </div>
    </div>
    <div class="table-section">
      <h3>Form title</h3>
      <p style="color:#888;">Sed tortor, sed velit ridiculus ipsum pharetra lacus odio gravida augue enim.</p>
      <div class="alert">
        <i class="fas fa-exclamation-triangle"></i>
        Senectus malesuada suspendisse bibendum elit amet vitae.
      </div>
      <table>
        <thead>
          <tr>
            <th>Table Title</th>
            <th>Table Title</th>
            <th>Table Title</th>
            <th>Table Title</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>Etiam purus in</td>
            <td>Curabitur donec duis</td>
            <td>Morbi pharetra, accumsan</td>
            <td>
              <button class="btn-edit">Edit</button>
              <button class="btn-delete">Delete</button>
            </td>
          </tr>
          <tr>
            <td>Duis eget habitant</td>
            <td>At amet odio</td>
            <td>Commodo eget scelerisque</td>
            <td>
              <button class="btn-edit">Edit</button>
              <button class="btn-delete">Delete</button>
            </td>
          </tr>
          <tr>
            <td>Aliquam velit lacus</td>
            <td>Pellentesque egestas placerat</td>
            <td>Tortor habitant sit</td>
            <td>
              <button class="btn-edit">Edit</button>
              <button class="btn-delete">Delete</button>
            </td>
          </tr>
          <tr>
            <td>Fermentum scelerisque ultricies</td>
            <td>Morbi sagittis nulla</td>
            <td>Quam semper quis</td>
            <td>
              <button class="btn-edit">Edit</button>
              <button class="btn-delete">Delete</button>
            </td>
          </tr>
          <tr>
            <td>Integer semper pellentesque</td>
            <td>Neque turpis enim</td>
            <td>Egestas non sociis</td>
            <td>
              <button class="btn-edit">Edit</button>
              <button class="btn-delete">Delete</button>
            </td>
          </tr>
          <tr>
            <td>Parturient at id</td>
            <td>Sem neque, mattis</td>
            <td>Pellentesque facilisis massa</td>
            <td>
              <button class="btn-edit">Edit</button>
              <button class="btn-delete">Delete</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>