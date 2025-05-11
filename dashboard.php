<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Dashboard</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #fef6ec;
      margin: 0;
      display: flex;
      min-height: 100vh;
    }

    .sidebar {
      width: 70px;
      background: #181c23;
      color: #fff;
      display: flex;
      flex-direction: column;
      align-items: center;
      padding: 20px 0;
      transition: width 0.3s;
      position: relative;
      overflow: hidden;
    }
    .sidebar.expanded {
      width: 250px;
      align-items: flex-start;
    }
    .sidebar .menu-btn {
      background: none;
      border: none;
      color: #7c7cff;
      font-size: 24px;
      margin-bottom: 30px;
      cursor: pointer;
      align-self: flex-start;
      margin-left: 20px;
    }
    .sidebar .nav {
      width: 100%;
      flex: 1;
      overflow: hidden;
    }
    .sidebar .nav ul {
      list-style: none;
      padding: 0;
      margin: 0;
      width: 100%;
    }
    .sidebar .nav li {
      width: 100%;
      margin-bottom: 10px;
    }
    .sidebar .nav a {
      display: flex;
      align-items: center;
      padding: 10px 20px;
      color: #fff;
      text-decoration: none;
      border-radius: 8px;
      transition: background 0.2s;
      font-size: 16px;
      width: calc(100% - 40px);
      margin: 0 10px;
    }
    .sidebar .nav a.active, .sidebar .nav a:hover {
      background: #5959e6;
      color: #fff;
    }
    .sidebar .nav i {
      margin-right: 0;
      font-size: 18px;
      width: 24px;
      text-align: center;
    }
    .sidebar.expanded .nav a span {
      display: inline;
      margin-left: 15px;
    }
    .sidebar .nav a span {
      display: none;
    }
    .sidebar .user {
      margin-top: auto;
      width: 100%;
      padding: 20px 0 0 0;
      border-top: 1px solid #333;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .sidebar.expanded .user {
      justify-content: flex-start;
      padding-left: 20px;
    }
    .sidebar .user i {
      font-size: 22px;
      margin-right: 0;
    }
    .sidebar.expanded .user span {
      display: inline;
      margin-left: 15px;
    }
    .sidebar .user span {
      display: none;
    }

    /* Main content */
    .main-content {
      flex: 1;
      padding: 0;
      background: #fef6ec;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    /* Dashboard styles */
    .header {
      background-color: #23242a;
      padding: 10px 20px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      border-bottom: 1px solid #222;
      color: #fff;
    }
    .header h2 {
      margin: 0;
    }
    .header .user-icon {
      font-size: 24px;
      cursor: pointer;
    }
    .search-bar {
      margin: 20px;
      display: flex;
      justify-content: center;
    }
    .search-bar input {
      padding: 10px;
      border-radius: 15px;
      border: 1px solid #333;
      width: 300px;
      background: #23242a;
      color: #fff;
    }
    .content {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
      gap: 24px;
      padding: 32px;
    }
    .restore-card {
      background: #23242a;
      border-radius: 16px;
      box-shadow: 0 4px 24px rgba(0,0,0,0.25);
      color: #fff;
      padding: 0;
      min-height: 220px;
      display: flex;
      flex-direction: column;
      overflow: hidden;
    }
    .restore-header {
      display: flex;
      align-items: center;
      gap: 16px;
      padding: 24px 24px 8px 24px;
      background: rgba(0,0,0,0.08);
    }
    .restore-icon {
      font-size: 2.2rem;
      border-radius: 12px;
      padding: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .restore-files { background: #a259ff; }
    .restore-bookmarks { background: #ffb300; }
    .restore-contacts { background: #3ec6ff; }
    .restore-calendars { background: #ff4d4f; }
    .restore-title {
      font-size: 1.35rem;
      font-weight: 600;
      margin: 0;
    }
    .restore-status {
      font-size: 1.05rem;
      color: #bdbdbd;
      margin-left: 2px;
      margin-top: 2px;
    }
    .restore-body {
      flex: 1;
      padding: 16px 24px 24px 24px;
      display: flex;
      align-items: flex-start;
      font-size: 1.08rem;
      color: #bdbdbd;
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
    .create-work-order {
      background-color: #fff;
      border: 1px solid #ccc;
      padding: 25px;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
      margin: 20px;
    }
    .hidden {
      display: none;
    }
    .create-work-order input,
    .create-work-order textarea {
      width: 100%;
      padding: 10px;
      margin-bottom: 10px;
      border: 1px solid #ccc;
      border-radius: 5px;
    }
    .create-work-order button {
      padding: 10px 15px;
      background-color: #007bff;
      color: white;
      border: none;
      border-radius: 5px;
      margin-right: 10px;
      cursor: pointer;
    }
    .create-work-order button:hover {
      background-color: #0056b3;
    }
    .cancel-btn {
      background-color: #ccc;
      color: black;
    }
    .cancel-btn:hover {
      background-color: #999;
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
  </style>
</head>
<body>
  <div class="sidebar" id="sidebar">
    <button class="menu-btn" onclick="toggleSidebar()">
      <i class="fa fa-bars"></i>
    </button>
    <nav class="nav">
      <ul>
        <li>
          <a href="#" class="active"><i class="fa fa-clock"></i><span>Dashbaord</span></a>
        </li>
        <li>
          <a href="#"><i class="fa fa-home"></i><span>Home</span></a>
        </li>
        <li>
          <a href="#"><i class="fa fa-table"></i><span>Products</span></a>
        </li>
        <li>
          <a href="#"><i class="fa fa-users"></i><span>Customers</span></a>
        </li>
      </ul>
    </nav>
    <div class="user">
      <i class="fa fa-user"></i>
      <span>Yousaf</span>
    </div>
  </div>
  <div class="main-content">
    <div class="header">
      <h2>DASHBOARD</h2>
    </div>
    <div class="search-bar">
      <input type="text" placeholder="Search...">
    </div>
    <main class="content">
      <div class="restore-card">
        <div class="restore-header">
          <span class="restore-icon restore-files"><i class="fa fa-file-alt"></i></span>
          <div>
            <div class="restore-title">Restore Files</div>
            <div class="restore-status">No Files</div>
          </div>
        </div>
        <div class="restore-body">-</div>
      </div>
      <div class="restore-card">
        <div class="restore-header">
          <span class="restore-icon restore-bookmarks"><i class="fa fa-bookmark"></i></span>
          <div>
            <div class="restore-title">Restore Bookmarks</div>
            <div class="restore-status">No Bookmarks</div>
          </div>
        </div>
        <div class="restore-body">-</div>
      </div>
      <div class="restore-card">
        <div class="restore-header">
          <span class="restore-icon restore-contacts"><i class="fa fa-user-circle"></i></span>
          <div>
            <div class="restore-title">Restore Contacts</div>
            <div class="restore-status">1 Archive</div>
          </div>
        </div>
        <div class="restore-body">Apr 13, 2025 7:59 AM</div>
      </div>
      <div class="restore-card">
        <div class="restore-header">
          <span class="restore-icon restore-calendars"><i class="fa fa-calendar-alt"></i></span>
          <div>
            <div class="restore-title">Restore Calendars</div>
            <div class="restore-status">No Archives</div>
          </div>
        </div>
        <div class="restore-body">-</div>
      </div>
    </main>
    <!-- Work Order Form (hidden initially) -->
    <div id="create-work-order" class="create-work-order hidden">
      <h3>Create New Work Order</h3>
      <form>
        <label>
          Title:
          <input type="text" placeholder="Enter title" required>
        </label><br>
        <label>
          Description:
          <textarea placeholder="Describe the task" required></textarea>
        </label><br>
        <label>
          Due Date:
          <input type="date" required>
        </label><br>
        <button type="submit">Submit</button>
        <button type="button" class="cancel-btn" onclick="hideWorkOrder()">Cancel</button>
      </form>
    </div>
    <!-- Work Orders List (hidden initially) -->
    <div id="work-orders-list" class="work-orders-list">
      <h3>Work Orders List</h3>
      <ul>
        <li>Work Order #1: Fix server issue</li>
        <li>Work Order #2: Update software</li>
        <li>Work Order #3: Backup system</li>
      </ul>
    </div>
  </div>
  <script>
    function toggleSidebar() {
      document.getElementById('sidebar').classList.toggle('expanded');
    }
    function showWorkOrder() {
      document.getElementById('create-work-order').classList.remove('hidden');
      window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' });
    }
    function hideWorkOrder() {
      document.getElementById('create-work-order').classList.add('hidden');
    }
    function showWorkOrders() {
      const workOrdersList = document.getElementById('work-orders-list');
      workOrdersList.classList.toggle('hidden');
    }
  </script>
</body>
</html>