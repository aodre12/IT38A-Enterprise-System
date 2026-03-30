<?php
session_start();
require 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: admin_login.php');
    exit;
}

$allowedStatuses = ['Pending', 'In Progress', 'Completed', 'Cancelled'];

// Handle status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['work_order_id'], $_POST['status'])) {
    $workOrderId = (int)$_POST['work_order_id'];
    $newStatus = $_POST['status'];

    if (in_array($newStatus, $allowedStatuses, true)) {
        $stmt = $conn->prepare("UPDATE work_orders SET status=? WHERE id=?");
        $stmt->bind_param("si", $newStatus, $workOrderId);
        $stmt->execute();
        $stmt->close();

        // Audit trail
        $adminUserId = (int)($_SESSION['user_id'] ?? 0);
        $audit = $conn->prepare("INSERT INTO audit_logs (user_id, action) VALUES (?, ?)");
        $auditMsg = 'Updated work order status to ' . $newStatus . ' (ID: ' . $workOrderId . ')';
        $audit->bind_param("is", $adminUserId, $auditMsg);
        $audit->execute();
        $audit->close();
    }

    header('Location: admin_work_orders.php');
    exit;
}

$workOrders = [];
$stmt = $conn->prepare("
    SELECT
      w.id,
      w.title,
      w.description,
      w.assigned_to,
      p_assigned.name AS assigned_name,
      w.status,
      w.due_date,
      w.created_by,
      p_created.name AS created_by_name,
      w.created_at,
      w.updated_at
    FROM work_orders w
    LEFT JOIN personnel p_assigned ON w.assigned_to = p_assigned.id
    LEFT JOIN personnel p_created ON w.created_by = p_created.id
    ORDER BY w.created_at DESC
");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $workOrders[] = $row;
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Work Orders | UtilityTrack</title>
  <style>
    body { font-family: Arial, sans-serif; background:#f5f6fa; margin:0; }
    header { background:#2c3e50; color:#fff; padding:20px; text-align:center; }
    .container { padding:20px; }
    table { width:100%; border-collapse:collapse; background:#fff; border-radius:8px; overflow:hidden; }
    th, td { padding:12px 14px; border-bottom:1px solid #eaeaea; text-align:left; vertical-align:top; }
    th { background:#2980b9; color:#fff; }
    tr:last-child td { border-bottom:none; }
    .btn { padding:8px 12px; border:none; border-radius:6px; cursor:pointer; font-weight:bold; }
    .btn-primary { background:#3498db; color:#fff; text-decoration:none; display:inline-block; }
    .note { background:#fff; padding:15px; border-radius:8px; margin-top:15px; }
    textarea { width:100%; height:70px; border:1px solid #ddd; border-radius:8px; padding:10px; resize:vertical; background:#fafafa; }
    select { padding:8px 10px; border-radius:6px; border:1px solid #ccc; }
    .toolbar { margin:15px 0; }
  </style>
</head>
<body>
  <header>
    <h1>Manage Work Orders</h1>
    <p>Admin Panel</p>
  </header>

  <div class="container">
    <div class="toolbar">
      <a class="btn-primary" href="admin_dashboard.php">← Back to Dashboard</a>
    </div>

    <?php if (empty($workOrders)): ?>
      <div class="note">No work orders found.</div>
    <?php else: ?>
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Description</th>
            <th>Assigned</th>
            <th>Due Date</th>
            <th>Status</th>
            <th>Created At</th>
            <th>Update</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($workOrders as $w): ?>
            <tr>
              <td><?= (int)$w['id'] ?></td>
              <td><?= htmlspecialchars($w['title']) ?></td>
              <td>
                <textarea readonly><?= htmlspecialchars($w['description'] ?? '') ?></textarea>
              </td>
              <td>
                <?= htmlspecialchars($w['assigned_name'] ?? '') ?>
              </td>
              <td><?= htmlspecialchars($w['due_date'] ?? '') ?></td>
              <td><?= htmlspecialchars($w['status'] ?? '') ?></td>
              <td><?= htmlspecialchars($w['created_at'] ?? '') ?></td>
              <td>
                <form method="POST" action="admin_work_orders.php">
                  <input type="hidden" name="work_order_id" value="<?= (int)$w['id'] ?>">
                  <select name="status">
                    <?php foreach ($allowedStatuses as $s): ?>
                      <option value="<?= htmlspecialchars($s) ?>" <?= ($w['status'] === $s) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($s) ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                  <button class="btn" style="background:#27ae60; color:#fff; margin-top:8px; width:100%;" type="submit">
                    Save
                  </button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>
</body>
</html>

