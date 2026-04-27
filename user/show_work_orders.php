<?php
session_start();
require '../Config.php';
require 'layout.php';
if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') { header('Location: ../admin/dashboard.php'); exit; }
if (!isset($_SESSION['user_id'])) { header('Location: ../login.php'); exit; }

$userId   = (int)$_SESSION['user_id'];
$username = $_SESSION['username'];

// Search
$search = trim($_GET['q'] ?? '');

// Load all work orders
$sql = "SELECT id, title, description, status, due_date, created_at FROM work_orders WHERE assigned_to=? OR created_by=? ORDER BY id DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $userId, $userId);
$stmt->execute();
$res = $stmt->get_result();
$all = [];
while ($r = $res->fetch_assoc()) {
    if ($search && stripos($r['title'].$r['description'], $search) === false) continue;
    $all[] = $r;
}
$stmt->close();

$pending   = array_filter($all, fn($r) => $r['status'] !== 'Completed' && $r['status'] !== 'Cancelled');
$completed = array_filter($all, fn($r) => $r['status'] === 'Completed');
$cancelled = array_filter($all, fn($r) => $r['status'] === 'Cancelled');

function statusBadge(string $s): string {
    return match($s) {
        'Completed'   => '<span class="badge b-done">Completed</span>',
        'In Progress' => '<span class="badge b-progress">In Progress</span>',
        'Cancelled'   => '<span class="badge b-cancelled">Cancelled</span>',
        default       => '<span class="badge b-pending">Pending</span>',
    };
}
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Work Orders | UtilityTrack</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
<?php user_styles('
.wo-card{background:var(--card);border-radius:12px;padding:18px 20px;border:1px solid var(--border);margin-bottom:12px;transition:box-shadow .2s;}
.wo-card:hover{box-shadow:0 4px 16px rgba(0,0,0,.08);}
.wo-title{font-weight:700;font-size:.95rem;margin-bottom:6px;color:var(--text);}
.wo-meta{display:flex;align-items:center;gap:14px;flex-wrap:wrap;font-size:.78rem;color:var(--muted);}
.wo-meta i{margin-right:3px;}
.search-bar{display:flex;gap:10px;margin-bottom:20px;}
.search-bar input{flex:1;}
.stats-mini{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:20px;}
.sm-card{background:var(--card);border-radius:12px;padding:14px;box-shadow:0 2px 8px rgba(0,0,0,.05);text-align:center;border-top:3px solid var(--blue);}
.sm-card.o{border-top-color:var(--warning);}.sm-card.g{border-top-color:var(--accent);}.sm-card.r{border-top-color:var(--danger);}
.sm-num{font-size:1.6rem;font-weight:800;}.sm-lbl{font-size:.7rem;color:var(--muted);text-transform:uppercase;letter-spacing:.4px;}
'); ?>
</head><body>
<?php user_sidebar('work_orders.php'); ?>
<?php user_topbar('Work Orders', $conn); ?>
<main class="main">

  <!-- Stats -->
  <div class="stats-mini">
    <div class="sm-card o"><div class="sm-num"><?= count($pending) ?></div><div class="sm-lbl">Pending</div></div>
    <div class="sm-card g"><div class="sm-num"><?= count($completed) ?></div><div class="sm-lbl">Completed</div></div>
    <div class="sm-card r"><div class="sm-num"><?= count($cancelled) ?></div><div class="sm-lbl">Cancelled</div></div>
  </div>

  <!-- Search -->
  <form method="GET" class="search-bar">
    <div class="form-group" style="flex:1;margin:0;">
      <input type="text" name="q" placeholder="Search work orders..." value="<?= htmlspecialchars($search) ?>">
    </div>
    <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Search</button>
    <?php if ($search): ?><a href="work_orders.php" class="btn btn-outline"><i class="fas fa-times"></i> Clear</a><?php endif; ?>
  </form>

  <!-- Pending / In Progress -->
  <div class="page-card">
    <div class="pc-header">
      <h2><i class="fas fa-hourglass-half" style="color:var(--warning);"></i> Active Work Orders</h2>
      <span class="badge b-pending"><?= count($pending) ?></span>
    </div>
    <div class="pc-body">
      <?php if (empty($pending)): ?>
        <div class="empty-state"><i class="fas fa-clipboard"></i><p>No active work orders.</p></div>
      <?php else: foreach ($pending as $w): ?>
        <div class="wo-card">
          <div class="wo-title"><?= htmlspecialchars($w['title']) ?></div>
          <?php if ($w['description']): ?>
            <p style="font-size:.83rem;color:var(--muted);margin:4px 0 8px;"><?= htmlspecialchars(substr($w['description'],0,120)) ?><?= strlen($w['description'])>120?'…':'' ?></p>
          <?php endif; ?>
          <div class="wo-meta">
            <?= statusBadge($w['status']) ?>
            <?php if ($w['due_date']): ?>
              <span><i class="fas fa-calendar-alt"></i> Due: <?= htmlspecialchars($w['due_date']) ?></span>
            <?php endif; ?>
            <span><i class="fas fa-clock"></i> <?= date('M j, Y', strtotime($w['created_at'])) ?></span>
          </div>
        </div>
      <?php endforeach; endif; ?>
    </div>
  </div>

  <!-- Completed -->
  <?php if (!empty($completed)): ?>
  <div class="page-card">
    <div class="pc-header">
      <h2><i class="fas fa-check-circle" style="color:var(--accent);"></i> Completed</h2>
      <span class="badge b-done"><?= count($completed) ?></span>
    </div>
    <div class="pc-body">
      <?php foreach ($completed as $w): ?>
        <div class="wo-card" style="opacity:.75;">
          <div class="wo-title"><?= htmlspecialchars($w['title']) ?></div>
          <div class="wo-meta"><?= statusBadge($w['status']) ?><span><i class="fas fa-clock"></i> <?= date('M j, Y', strtotime($w['created_at'])) ?></span></div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>

</main>
<?php user_sidebar_script(); ?>
</body></html>
