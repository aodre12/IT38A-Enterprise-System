<?php
session_start();
require_once 'config.php';

$error = '';
$success = '';

// Admin-only access
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: admin_login.php');
    exit;
}

// Personnel list for assigning work orders
$personnelList = $conn->query("SELECT id, name FROM personnel ORDER BY status DESC, name ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $due_date = $_POST['due_date'] ?? '';
    $assigned_to = $_POST['assigned_to'] ?? null;
    $created_by = $_SESSION['user_id'];

    if (empty($title) || empty($description) || empty($due_date)) {
        $error = 'All fields are required.';
    } else {
        // Allow optional assignment
        if ($assigned_to === '' || $assigned_to === null) {
            $assigned_to = null;
        }

        $stmt = $conn->prepare("INSERT INTO work_orders (title, description, assigned_to, due_date, created_by) VALUES (?, ?, ?, ?, ?)");
        // title (s), description (s), assigned_to (i), due_date (s), created_by (i)
        $stmt->bind_param("ssisi", $title, $description, $assigned_to, $due_date, $created_by);

        if ($stmt->execute()) {
            $success = 'Work order created successfully.';
        } else {
            $error = 'Failed to create work order: ' . $conn->error;
        }

        $stmt->close();
        $conn->close();
        header('Location: admin_dashboard.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Create Work Order</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #fef6ec;
      margin: 0;
      padding: 40px;
    }

    .container {
      max-width: 500px;
      margin: auto;
      background-color: #fff;
      padding: 30px;
      border-radius: 15px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }

    h2 {
      text-align: center;
      margin-bottom: 20px;
    }

    label {
      display: block;
      margin-bottom: 8px;
      font-weight: bold;
    }

    input[type="text"],
    textarea,
    input[type="date"] {
      width: 100%;
      padding: 10px;
      margin-bottom: 15px;
      border: 1px solid #ccc;
      border-radius: 5px;
    }

    button {
      background-color: #007bff;
      color: white;
      border: none;
      padding: 12px 20px;
      border-radius: 5px;
      cursor: pointer;
      width: 100%;
    }

    button:hover {
      background-color: #0056b3;
    }

    .success {
      color: green;
      text-align: center;
      margin-bottom: 15px;
    }

    .error {
      color: red;
      text-align: center;
      margin-bottom: 15px;
    }

    .back-link {
      display: block;
      text-align: center;
      margin-top: 20px;
      text-decoration: none;
      color: #007bff;
    }

    .back-link:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Create Work Order</h2>

    <?php if ($error): ?>
      <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php elseif ($success): ?>
      <div class="success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="POST" action="">
      <label>Title</label>
      <input type="text" name="title" required>

      <label>Description</label>
      <textarea name="description" required></textarea>

      <label>Due Date</label>
      <input type="date" name="due_date" required>

      <label>Assign to (optional)</label>
      <select name="assigned_to">
        <option value="">Unassigned</option>
        <?php if ($personnelList): ?>
          <?php while ($p = $personnelList->fetch_assoc()): ?>
            <option value="<?= (int)$p['id'] ?>"><?= htmlspecialchars($p['name']) ?></option>
          <?php endwhile; ?>
        <?php endif; ?>
      </select>

      <button type="submit">Submit Work Order</button>
    </form>

    <a class="back-link" href="admin_dashboard.php">← Back to Dashboard</a>
  </div>
</body>
</html>
