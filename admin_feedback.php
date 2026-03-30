<?php
session_start();
require 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: admin_login.php');
    exit;
}

// Handle delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_feedback_id'])) {
    $deleteId = (int)$_POST['delete_feedback_id'];

    $stmt = $conn->prepare("DELETE FROM feedback WHERE id=?");
    $stmt->bind_param("i", $deleteId);
    $stmt->execute();
    $stmt->close();

    // Audit trail
    $adminUserId = (int)($_SESSION['user_id'] ?? 0);
    $audit = $conn->prepare("INSERT INTO audit_logs (user_id, action) VALUES (?, ?)");
    $auditMsg = 'Deleted feedback (ID: ' . $deleteId . ')';
    $audit->bind_param("is", $adminUserId, $auditMsg);
    $audit->execute();
    $audit->close();

    header('Location: admin_feedback.php');
    exit;
}

$feedbackTableExists = false;
$res = $conn->query("SHOW TABLES LIKE 'feedback'");
if ($res && $res->num_rows > 0) {
    $feedbackTableExists = true;
}

$feedbackRows = [];
if ($feedbackTableExists) {
    $stmt = $conn->prepare("
        SELECT f.id, u.username, f.message, f.created_at
        FROM feedback f
        JOIN users u ON f.user_id = u.id
        ORDER BY f.created_at DESC
    ");
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $feedbackRows[] = $row;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Feedback | UtilityTrack</title>
  <style>
    body { font-family: Arial, sans-serif; background:#f5f6fa; margin:0; }
    header { background:#2c3e50; color:#fff; padding:20px; text-align:center; }
    .container { padding:20px; }
    table { width:100%; border-collapse:collapse; background:#fff; border-radius:8px; overflow:hidden; }
    th, td { padding:12px 14px; border-bottom:1px solid #eaeaea; text-align:left; vertical-align:top; }
    th { background:#2980b9; color:#fff; }
    tr:last-child td { border-bottom:none; }
    .btn { padding:8px 12px; border:none; border-radius:6px; cursor:pointer; font-weight:bold; }
    .btn-danger { background:#e74c3c; color:#fff; }
    .btn-primary { background:#3498db; color:#fff; text-decoration:none; display:inline-block; }
    .toolbar { margin:15px 0; }
    .note { background:#fff; padding:15px; border-radius:8px; margin-top:15px; }
    textarea { width:100%; height:80px; border:1px solid #ddd; border-radius:8px; padding:10px; resize:vertical; background:#fafafa; }
  </style>
</head>
<body>
  <header>
    <h1>Manage Feedback</h1>
    <p>Admin Panel</p>
  </header>

  <div class="container">
    <div class="toolbar">
      <a class="btn-primary" href="admin_dashboard.php">← Back to Dashboard</a>
    </div>

    <?php if (!$feedbackTableExists): ?>
      <div class="note">
        <strong>Feedback table not found.</strong><br>
        Run <code>add_reports_feedback_tables.sql</code> in your database once.
      </div>
    <?php else: ?>
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>User</th>
            <th>Message</th>
            <th>Created At</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($feedbackRows)): ?>
            <tr><td colspan="5">No feedback found.</td></tr>
          <?php else: ?>
            <?php foreach ($feedbackRows as $f): ?>
              <tr>
                <td><?= (int)$f['id'] ?></td>
                <td><?= htmlspecialchars($f['username']) ?></td>
                <td>
                  <textarea readonly><?= htmlspecialchars($f['message']) ?></textarea>
                </td>
                <td><?= htmlspecialchars($f['created_at']) ?></td>
                <td>
                  <form method="POST" action="admin_feedback.php" onsubmit="return confirm('Delete this feedback?');">
                    <input type="hidden" name="delete_feedback_id" value="<?= (int)$f['id'] ?>">
                    <button class="btn btn-danger" type="submit">Delete</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>
</body>
</html>

