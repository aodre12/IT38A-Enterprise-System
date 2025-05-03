<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Dashboard</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      padding: 20px;
      background-color: #f9f9f9;
    }

    .dashboard {
      display: flex;
      gap: 15px;
      flex-wrap: wrap;
      margin-bottom: 20px;
    }

    .dashboard-box {
      padding: 20px;
      background-color: #f0f0f0;
      cursor: pointer;
      border: 2px solid transparent;
      transition: 0.3s;
      border-radius: 8px;
      flex: 1 1 200px;
      text-align: center;
      font-weight: bold;
    }

    .dashboard-box:hover {
      background-color: #e0e0e0;
      border-color: #999;
    }

    .dashboard-box.active {
      background-color: #007bff;
      color: white;
      border-color: #0056b3;
    }

    .work-order-form {
      padding: 15px;
      background-color: #ffffff;
      border: 1px solid #ccc;
      border-radius: 8px;
      max-width: 400px;
      box-shadow: 0 0 8px rgba(0,0,0,0.1);
    }

    .hidden {
      display: none;
    }

    .work-order-form form {
      display: flex;
      flex-direction: column;
      gap: 10px;
    }

    .work-order-form input,
    .work-order-form textarea {
      padding: 8px;
      font-size: 14px;
      width: 100%;
      border: 1px solid #ccc;
      border-radius: 5px;
    }

    .work-order-form button {
      padding: 8px;
      font-size: 14px;
      border: none;
      border-radius: 5px;
      background-color: #007bff;
      color: white;
      cursor: pointer;
    }

    .work-order-form button:hover {
      background-color: #0056b3;
    }

    .work-order-form button[type="button"] {
      background-color: #ccc;
      color: #000;
    }

    .work-order-form button[type="button"]:hover {
      background-color: #999;
    }
  </style>
</head>
<body>

  <h2>Welcome to the Dashboard</h2>

  <div class="dashboard">
    <div class="dashboard-box" onclick="toggleBox(this)">Home</div>
    <div class="dashboard-box" onclick="toggleBox(this)">Profile</div>
    <div class="dashboard-box" onclick="toggleBox(this)">Orders</div>
    <div class="dashboard-box" onclick="toggleBox(this)">Notifications</div>
    <div class="dashboard-box" onclick="toggleBox(this)">Saved Reports</div>
    <div class="dashboard-box" onclick="toggleBox(this)">Settings</div>
    <div class="dashboard-box" onclick="toggleBox(this)">Help & Support</div>
    <div class="dashboard-box" onclick="toggleBox(this)">Feedback</div>
    <div class="dashboard-box" onclick="toggleBox(this)">Logout</div>
    <div class="dashboard-box" onclick="showWorkOrder()">Create Work Order</div>
  </div>

  <div id="work-order-form" class="work-order-form hidden">
    <h3>Create Work Order</h3>
    <form>
      <label>Title:
        <input type="text" placeholder="Work title" required />
      </label>
      <label>Description:
        <textarea placeholder="Details..." required></textarea>
      </label>
      <label>Date:
        <input type="date" required />
      </label>
      <div style="display: flex; gap: 10px;">
        <button type="submit">Submit</button>
        <button type="button" onclick="hideWorkOrder()">Cancel</button>
      </div>
    </form>
  </div>

  <script>
    function toggleBox(el) {
      el.classList.toggle('active');
    }

    function showWorkOrder() {
      document.getElementById('work-order-form').classList.remove('hidden');
    }

    function hideWorkOrder() {
      document.getElementById('work-order-form').classList.add('hidden');
    }
  </script>

</body>
</html>
