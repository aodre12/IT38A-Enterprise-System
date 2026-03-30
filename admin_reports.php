<?php
session_start();
require 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: admin_login.php');
    exit;
}

// Handle delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_report_id'])) {
    $deleteId = (int)$_POST['delete_report_id'];

    // Delete report
    $stmt = $conn->prepare("DELETE FROM reports WHERE id=?");
    $stmt->bind_param("i", $deleteId);
    $stmt->execute();
    $stmt->close();

    // Audit trail
    $adminUserId = (int)($_SESSION['user_id'] ?? 0);
    $audit = $conn->prepare("INSERT INTO audit_logs (user_id, action) VALUES (?, ?)");
    $auditMsg = 'Deleted a report (ID: ' . $deleteId . ')';
    $audit->bind_param("is", $adminUserId, $auditMsg);
    $audit->execute();
    $audit->close();

    header('Location: admin_reports.php');
    exit;
}

// Table existence (so you can run without SQL yet)
$reportsTableExists = false;
$res = $conn->query("SHOW TABLES LIKE 'reports'");
if ($res && $res->num_rows > 0) {
    $reportsTableExists = true;
}

$reports = [];
if ($reportsTableExists) {
    $stmt = $conn->prepare("
        SELECT r.id, u.username, r.title, r.report_date, r.url, r.created_at
        FROM reports r
        JOIN users u ON r.user_id = u.id
        ORDER BY r.created_at DESC
    ");
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $reports[] = $row;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Reports | UtilityTrack</title>
  <style>
    body { font-family: Arial, sans-serif; background:#f5f6fa; margin:0; }
    header { background:#2c3e50; color:#fff; padding:20px; text-align:center; }
    .container { padding:20px; }
    table { width:100%; border-collapse:collapse; background:#fff; border-radius:8px; overflow:hidden; }
    th, td { padding:12px 14px; border-bottom:1px solid #eaeaea; text-align:left; }
    th { background:#2980b9; color:#fff; }
    tr:last-child td { border-bottom:none; }
    .btn { padding:8px 12px; border:none; border-radius:6px; cursor:pointer; font-weight:bold; }
    .btn-danger { background:#e74c3c; color:#fff; }
    .btn-primary { background:#3498db; color:#fff; text-decoration:none; display:inline-block; }
    .toolbar { margin:15px 0; }
    .note { background:#fff; padding:15px; border-radius:8px; margin-top:15px; }
  </style>
</head>
<body>
  <header>
    <h1>Manage Reports</h1>
    <p>Admin Panel</p>
  </header>

  <div class="container">
    <div class="toolbar">
      <a class="btn-primary" href="admin_dashboard.php">← Back to Dashboard</a>
    </div>

    <?php if (!$reportsTableExists): ?>
      <div class="note">
        <strong>Reports table not found.</strong><br>
        Run <code>add_reports_feedback_tables.sql</code> in your database once.
      </div>
    <?php else: ?>
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>User</th>
            <th>Title</th>
            <th>Date</th>
            <th>URL</th>
            <th>Created At</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($reports)): ?>
            <tr><td colspan="7">No reports found.</td></tr>
          <?php else: ?>
            <?php foreach ($reports as $r): ?>
              <tr>
                <td><?= (int)$r['id'] ?></td>
                <td><?= htmlspecialchars($r['username']) ?></td>
                <td><?= htmlspecialchars($r['title']) ?></td>
                <td><?= htmlspecialchars($r['report_date']) ?></td>
                <td><?= htmlspecialchars($r['url'] ?? '#') ?></td>
                <td><?= htmlspecialchars($r['created_at']) ?></td>
                <td>
                  <form method="POST" action="admin_reports.php" onsubmit="return confirm('Delete this report?');">
                    <input type="hidden" name="delete_report_id" value="<?= (int)$r['id'] ?>">
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

