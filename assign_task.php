<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: admin_login.php');
    exit;
}

$username = $_SESSION['username'] ?? 'AdminDev';

// Fetch personnel for dropdown
$personnelList = $conn->query("SELECT id, name FROM personnel WHERE status='Active'")->fetch_all(MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $task = $_POST['task'] ?? '';
    $assigned_to = $_POST['assigned_to'] ?? '';
    $status = $_POST['status'] ?? 'Pending';

    if ($task && $assigned_to) {
        $stmt = $conn->prepare("INSERT INTO tasks (task, assigned_to, status) VALUES (?, ?, ?)");
        $stmt->bind_param("sis", $task, $assigned_to, $status);
        if ($stmt->execute()) {
            header("Location: admin_dashboard.php");
            exit;
        } else {
            $error = "Failed to assign task.";
        }
    } else {
        $error = "Task and Assigned To fields are required.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Assign Task - Utility ERP</title>
    <style>
        /* Same styles as dashboard */
        body { font-family: Arial, sans-serif; background: #f5f6fa; margin: 0; }
        header { background: #2c3e50; color: white; padding: 20px; text-align: center; }
        .container { padding: 20px; max-width: 600px; margin: 30px auto; background: #fff; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        h1, h2 { margin-top: 0; }
        label { display: block; margin: 15px 0 5px; }
        input[type=text], select, textarea {
            width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; font-size: 1em;
            box-sizing: border-box;
        }
        button {
            background: #2ecc71; color: white; padding: 12px 20px; border: none; border-radius: 4px;
            cursor: pointer; font-size: 1em;
            margin-top: 20px;
        }
        button:hover { background: #27ae60; }
        .error { color: #e74c3c; margin-top: 10px; }
        a.back-link {
            display: inline-block; margin-top: 15px; color: #2980b9; text-decoration: none;
        }
        a.back-link:hover { text-decoration: underline; }
    </style>
</head>
<body>
<header>
    <h1>Utility ERP - Admin Dashboard</h1>
    <p>Welcome, <?= htmlspecialchars($username) ?></p>
</header>

<div class="container">
    <h2>Assign New Task</h2>
    <?php if (!empty($error)) : ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST">
        <label for="task">Task Description:</label>
        <textarea id="task" name="task" rows="4" required></textarea>

        <label for="assigned_to">Assign To:</label>
        <select id="assigned_to" name="assigned_to" required>
            <option value="">-- Select Personnel --</option>
            <?php foreach ($personnelList as $person): ?>
                <option value="<?= $person['id'] ?>"><?= htmlspecialchars($person['name']) ?></option>
            <?php endforeach; ?>
        </select>

        <label for="status">Status:</label>
        <select id="status" name="status">
            <option value="Pending" selected>Pending</option>
            <option value="Completed">Completed</option>
        </select>

        <button type="submit">Assign Task</button>
    </form>

    <a class="back-link" href="admin_dashboard.php">&larr; Back to Dashboard</a>
</div>
</body>
</html>
