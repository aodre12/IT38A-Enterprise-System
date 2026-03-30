<?php
session_start();
require 'config.php'; // This should connect to your database

// Authentication check (admin-only)
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: admin_login.php');
    exit;
}

if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    header('Location: admin_login.php');
    exit;
}

$username = $_SESSION['username']; // from admin login session

// Fetch stats from DB (sample queries)
$workOrders = $conn->query("SELECT COUNT(*) FROM work_orders")->fetch_row()[0];
$personnelActive = $conn->query("SELECT COUNT(*) FROM personnel WHERE status='Active'")->fetch_row()[0];
$pendingTasks = $conn->query("SELECT COUNT(*) FROM tasks WHERE status='Pending'")->fetch_row()[0];
$completedTasks = $conn->query("SELECT COUNT(*) FROM tasks WHERE status='Completed'")->fetch_row()[0];

// Fetch personnel list
$personnel = $conn->query("SELECT id, name, role, status FROM personnel")->fetch_all(MYSQLI_ASSOC);

// Fetch task list
$tasks = $conn->query("SELECT t.task, p.name AS assigned, t.status 
                       FROM tasks t
                       JOIN personnel p ON t.assigned_to = p.id")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f6fa; margin: 0; }
        header { background: #2c3e50; color: white; padding: 20px; text-align: center; }
        .container { padding: 20px; }
        .card-container { display: flex; gap: 20px; margin-bottom: 20px; }
        .card {
            flex: 1;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .card h3 { margin: 0 0 10px; }
        .card p { font-size: 1.5em; color: #2980b9; }
        table { width: 100%; background: white; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; border: 1px solid #ccc; text-align: left; }
        th { background: #2980b9; color: white; }
        .btn { padding: 6px 12px; border: none; border-radius: 4px; cursor: pointer; }
        .btn-edit { background: #3498db; color: white; }
        .btn-delete { background: #e74c3c; color: white; }
        .btn-action { margin: 10px 5px 0 0; background: #2ecc71; color: white; }
    </style>
</head>
<body>
<header>
    <h1>Admin Dashboard - Utility ERP</h1>
    <p>Welcome, <?php echo htmlspecialchars($username); ?></p>
</header>
<div class="container">
    <div class="card-container">
        <div class="card"><h3>Work Orders</h3><p><?= $workOrders ?></p></div>
        <div class="card"><h3>Active Personnel</h3><p><?= $personnelActive ?></p></div>
        <div class="card"><h3>Pending Tasks</h3><p><?= $pendingTasks ?></p></div>
        <div class="card"><h3>Completed Tasks</h3><p><?= $completedTasks ?></p></div>
    </div>

    <h2>Personnel Management</h2>
    <button class="btn btn-action" onclick="location.href='add_personnel.php'">+ Add Personnel</button>
    <table>
        <thead>
            <tr><th>Name</th><th>Role</th><th>Status</th><th>Actions</th></tr>
        </thead>
        <tbody>
            <?php foreach ($personnel as $p): ?>
            <tr>
                <td><?= htmlspecialchars($p['name']) ?></td>
                <td><?= htmlspecialchars($p['role']) ?></td>
                <td><?= htmlspecialchars($p['status']) ?></td>
                <td>
                    <button class="btn btn-edit" onclick="location.href='edit_personnel.php?id=<?= $p['id'] ?>'">Edit</button>
                    <button class="btn btn-delete" onclick="if(confirm('Deactivate this personnel?')) location.href='deactivate_personnel.php?id=<?= $p['id'] ?>'">Deactivate</button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h2>Task Assignment & Monitoring</h2>
    <button class="btn btn-action" onclick="location.href='assign_task.php'">+ Assign Task</button>
    <table>
        <thead>
            <tr><th>Task</th><th>Assigned To</th><th>Status</th></tr>
        </thead>
        <tbody>
            <?php foreach ($tasks as $task): ?>
            <tr>
                <td><?= htmlspecialchars($task['task']) ?></td>
                <td><?= htmlspecialchars($task['assigned']) ?></td>
                <td><?= htmlspecialchars($task['status']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h2>System Tools</h2>
    <button class="btn btn-action" onclick="location.href='reset_password.php'">Reset Password</button>
    <button class="btn btn-action" onclick="location.href='update_access.php'">Update Access</button>
    <button class="btn btn-action" onclick="location.href='configure_system.php'">Configure System</button>
    <button class="btn btn-action" onclick="location.href='create_work.php'">+ Create Work Order</button>
    <button class="btn btn-action" onclick="location.href='admin_work_orders.php'">Manage Work Orders</button>
    <button class="btn btn-action" onclick="location.href='admin_reports.php'">Manage Reports</button>
    <button class="btn btn-action" onclick="location.href='admin_feedback.php'">Manage Feedback</button>
    <button class="btn btn-action" onclick="location.href='view_audit_logs.php'">View Audit Logs</button>
</div>
</body>
</html>
