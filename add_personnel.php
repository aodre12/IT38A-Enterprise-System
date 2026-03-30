<?php
session_start();
require 'config.php';

// Authentication check (optional, add your auth logic)
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: admin_login.php');
    exit;
}

$username = $_SESSION['username'] ?? 'AdminDev';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $role = $_POST['role'] ?? '';
    $status = $_POST['status'] ?? 'Active';

    if ($name && $role) {
        $stmt = $conn->prepare("INSERT INTO personnel (name, role, status) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $role, $status);
        if ($stmt->execute()) {
            header("Location: admin_dashboard.php");
            exit;
        } else {
            $error = "Failed to add personnel.";
        }
    } else {
        $error = "Name and Role are required.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Add Personnel - Utility ERP</title>
    <style>
        /* Use your dashboard styles */
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
    <p>Welcome, <?php echo htmlspecialchars($username); ?></p>
</header>

<div class="container">
    <h2>Add New Personnel</h2>
    <?php if (!empty($error)) : ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required />

        <label for="role">Role:</label>
        <input type="text" id="role" name="role" required />

        <label for="status">Status:</label>
        <select id="status" name="status">
            <option value="Active" selected>Active</option>
            <option value="Inactive">Inactive</option>
        </select>

        <button type="submit">Add Personnel</button>
    </form>

    <a class="back-link" href="admin_dashboard.php">&larr; Back to Dashboard</a>
</div>
</body>
</html>
