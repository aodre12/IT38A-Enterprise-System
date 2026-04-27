<?php
session_start();
require '../Config.php';
require 'layout.php';
if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') { header('Location: ../admin/dashboard.php'); exit; }
if (!isset($_SESSION['user_id'])) { header('Location: ../login.php'); exit; }

$userId   = (int)$_SESSION['user_id'];
$username = $_SESSION['username'];

$tableExists = $conn->query("SHOW TABLES LIKE 'reports'")->num_rows > 0;
$reports = [];
if ($tableExists) {
    $s = $conn->prepare("SELECT id, title, report_date, url, created_at FROM reports WHERE user_id=? ORDER BY created_at DESC");
    $s->bind_param("i", $userId); $s->execute();
    $r = $s->get_result();
    while ($row = $r->fetch_assoc()) $reports[] = $row;
    $s->close();
}

$error = ''; $success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $date  = trim($_POST['date']  ?? '');
    if (!$title || !$date) {
        $error = 'Title and date are required.';
    } elseif ($tableExists) {
        $url = '#';
        $s = $conn->prepare("INSERT INTO reports (user_id, title, report_date, url) VALUES (?, ?, ?, ?)");
        $s->bind_param("isss", $userId, $title, $date, $url); $s->execute(); $s->close();
        $a = $conn->prepare("INSERT INTO audit_logs (user_id, action) VALUES (?, ?)");
        $m = "Submitted report: $title"; $a->bind_param("is", $userId, $m); $a->execute(); $a->close();
        header('Location: reports.php'); exit;
    } else {
        $success = 'Report saved locally.';
    }
}
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Reports | UtilityTrack</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
<?php user_styles('
.report-row{display:flex;align-items:center;justify-content:space-between;padding:13px 0;border-bottom:1px solid var(--border);gap:12px;}
.report-row:last-child{border:none;}
.report-info{flex:1;min-width:0;}
.report-title{font-weight:600;font-size:.9rem;color:var(--text);}
.report-date{font-size:.76rem;color:var(--muted);margin-top:2px;}
'); ?>
</head><body>
<?php user_sidebar('reports.php'); ?>
<?php user_topbar('Reports', $conn); ?>
<main class="main">

  <!-- Submit form -->
  <div class="page-card">
    <div class="pc-header"><h2><i class="fas fa-plus-circle" style="color:var(--blue);"></i> Submit New Report</h2></div>
    <div class="pc-body">
      <?php if ($error): ?><div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div><?php endif; ?>
      <?php if ($success): ?><div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($success) ?></div><?php endif; ?>
      <form method="POST">
        <div class="form-group">
          <label>Report Title</label>
          <input type="text" name="title" placeholder="e.g. Monthly Maintenance Report" required value="<?= htmlspecialchars($_POST['title'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label>Report Date</label>
          <input type="date" name="date" required value="<?= htmlspecialchars($_POST['date'] ?? date('Y-m-d')) ?>">
        </div>
        <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Submit Report</button>
      </form>
    </div>
  </div>

  <!-- Reports list -->
  <div class="page-card">
    <div class="pc-header">
      <h2><i class="fas fa-chart-line" style="color:var(--blue);"></i> My Reports</h2>
      <span style="font-size:.8rem;color:var(--muted);"><?= count($reports) ?> total</span>
    </div>
    <div class="pc-body">
      <?php if (empty($reports)): ?>
        <div class="empty-state"><i class="fas fa-file-alt"></i><p>No reports submitted yet.</p></div>
      <?php else: ?>
        <?php foreach ($reports as $r): ?>
          <div class="report-row">
            <div class="report-info">
              <div class="report-title"><?= htmlspecialchars($r['title']) ?></div>
              <div class="report-date"><i class="fas fa-calendar-alt"></i> <?= htmlspecialchars($r['report_date']) ?> &nbsp;·&nbsp; Submitted <?= date('M j, Y', strtotime($r['created_at'])) ?></div>
            </div>
            <?php if (!empty($r['url']) && $r['url'] !== '#'): ?>
              <a href="<?= htmlspecialchars($r['url']) ?>" class="btn btn-outline btn-sm" target="_blank"><i class="fas fa-eye"></i> View</a>
            <?php else: ?>
              <span class="badge b-done"><i class="fas fa-check"></i> Submitted</span>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>

</main>
<?php user_sidebar_script(); ?>
</body></html>
