<?php
session_start();
require '../Config.php';
header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') { header('Location: ../admin/dashboard.php'); exit; }
if (!isset($_SESSION['user_id'])) { header('Location: ../login.php'); exit; }
$userId = (int)$_SESSION['user_id'];
$username = $_SESSION['username'];
$stmt = $conn->prepare("SELECT id, task, status FROM tasks WHERE assigned_to=? ORDER BY id DESC LIMIT 10");
$stmt->bind_param("i", $userId); $stmt->execute(); $result = $stmt->get_result();
$pendingTasks = []; $completedTasks = [];
while ($row = $result->fetch_assoc()) { if ($row['status']==='Completed') $completedTasks[]=$row; else $pendingTasks[]=$row; }
$stmt->close();
$woStmt = $conn->prepare("SELECT COUNT(*) FROM work_orders WHERE assigned_to=? OR created_by=?");
$woStmt->bind_param("ii",$userId,$userId); $woStmt->execute(); $woStmt->bind_result($totalWorkOrders); $woStmt->fetch(); $woStmt->close();
$woRecent = [];
$ws = $conn->prepare("SELECT id,title,status,due_date FROM work_orders WHERE assigned_to=? OR created_by=? ORDER BY id DESC LIMIT 5");
$ws->bind_param("ii",$userId,$userId); $ws->execute(); $wr=$ws->get_result();
while ($row=$wr->fetch_assoc()) $woRecent[]=$row; $ws->close();
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Dashboard | UtilityTrack</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
<style>
:root{--navy:#001F54;--blue:#0056b3;--accent:#27ae60;--warning:#f39c12;--danger:#e74c3c;--bg:#f0f4f8;--card:#fff;--text:#2c3e50;--muted:#7f8c8d;--border:#e0e6ed;--sw:260px;}
*{margin:0;padding:0;box-sizing:border-box;}
body{font-family:'Segoe UI',sans-serif;background:var(--bg);color:var(--text);display:flex;min-height:100vh;}
.sidebar{width:var(--sw);background:var(--navy);color:#fff;display:flex;flex-direction:column;position:fixed;top:0;left:0;height:100vh;z-index:1000;transition:transform .3s;overflow-y:auto;}
.sb-brand{padding:22px 20px 16px;border-bottom:1px solid rgba(255,255,255,.1);}
.sb-brand a{text-decoration:none;display:flex;align-items:center;gap:10px;}
.sb-brand .logo{width:32px;height:32px;background:rgba(79,195,247,.2);border-radius:8px;display:flex;align-items:center;justify-content:center;color:#4fc3f7;font-size:.95rem;}
.sb-brand span{font-size:1.05rem;font-weight:700;color:#fff;}
.sb-user{display:flex;align-items:center;gap:12px;padding:14px 20px;border-bottom:1px solid rgba(255,255,255,.1);}
.sb-user .av{width:36px;height:36px;border-radius:50%;background:#2980b9;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.95rem;color:#fff;flex-shrink:0;}
.sb-user .un{font-size:.86rem;font-weight:600;}.sb-user .ur{font-size:.7rem;color:rgba(255,255,255,.5);}
.sb-nav{padding:12px 0;flex:1;}
.nav-lbl{font-size:.63rem;text-transform:uppercase;letter-spacing:1.2px;color:rgba(255,255,255,.35);padding:10px 20px 4px;}
.sb-nav a{display:flex;align-items:center;gap:11px;padding:10px 20px;color:rgba(255,255,255,.75);text-decoration:none;font-size:.86rem;transition:background .2s,color .2s;border-left:3px solid transparent;}
.sb-nav a:hover,.sb-nav a.active{background:rgba(255,255,255,.08);color:#fff;border-left-color:#4fc3f7;}
.sb-nav a i{width:16px;text-align:center;font-size:.88rem;}
.sb-foot{padding:14px 20px;border-top:1px solid rgba(255,255,255,.1);}
.sb-foot a{display:flex;align-items:center;gap:10px;color:rgba(255,255,255,.6);text-decoration:none;font-size:.83rem;}
.sb-foot a:hover{color:#fff;}
.topbar{position:fixed;top:0;left:var(--sw);right:0;height:56px;background:#fff;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;padding:0 22px;z-index:900;box-shadow:0 1px 4px rgba(0,0,0,.06);}
.topbar-l{display:flex;align-items:center;gap:12px;}.page-title{font-size:.98rem;font-weight:600;}
.topbar-r{display:flex;align-items:center;gap:10px;}
.tu{display:flex;align-items:center;gap:8px;font-size:.82rem;color:var(--muted);}
.hamburger{display:none;background:none;border:none;font-size:1.2rem;cursor:pointer;color:var(--text);}
.main{margin-left:var(--sw);margin-top:56px;padding:22px;flex:1;min-width:0;}
.stats-row{display:grid;grid-template-columns:repeat(auto-fill,minmax(170px,1fr));gap:14px;margin-bottom:22px;}
.sc{background:var(--card);border-radius:12px;padding:16px;box-shadow:0 2px 8px rgba(0,0,0,.05);display:flex;align-items:center;gap:12px;border-left:4px solid var(--blue);}
.sc.g{border-left-color:var(--accent);}.sc.o{border-left-color:var(--warning);}
.sc .si{width:42px;height:42px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:1.1rem;color:#fff;background:var(--blue);flex-shrink:0;}
.sc.g .si{background:var(--accent);}.sc.o .si{background:var(--warning);}
.sc .sl{font-size:.7rem;color:var(--muted);text-transform:uppercase;letter-spacing:.4px;}
.sc .sv{font-size:1.5rem;font-weight:700;line-height:1.2;}
.g2{display:grid;grid-template-columns:1fr 1fr;gap:18px;margin-bottom:18px;}
.card{background:var(--card);border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,.05);overflow:hidden;}
.ch{display:flex;align-items:center;justify-content:space-between;padding:14px 18px;border-bottom:1px solid var(--border);}
.ch h3{font-size:.9rem;font-weight:600;}
.ch a{font-size:.76rem;color:#2980b9;text-decoration:none;font-weight:600;}
.cb{padding:14px 18px;}
.tl{list-style:none;padding:0;}
.tl li{display:flex;align-items:center;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--border);font-size:.83rem;}
.tl li:last-child{border:none;}
.tn{flex:1;min-width:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;margin-right:8px;}
.badge{display:inline-block;padding:2px 8px;border-radius:20px;font-size:.7rem;font-weight:600;}
.bp{background:#fff3cd;color:#856404;}.bd{background:#d4edda;color:#155724;}
.bpr{background:#cce5ff;color:#004085;}.bc{background:#f8d7da;color:#721c24;}
.es{text-align:center;padding:22px;color:var(--muted);font-size:.83rem;}
.es i{font-size:1.6rem;margin-bottom:6px;display:block;opacity:.4;}
.ql{display:grid;grid-template-columns:1fr 1fr;gap:10px;}
.qi{display:flex;align-items:center;gap:10px;padding:11px 13px;border-radius:10px;text-decoration:none;color:var(--text);background:var(--bg);border:1px solid var(--border);transition:background .2s,border-color .2s;font-size:.83rem;font-weight:600;}
.qi:hover{background:#e8f4fd;border-color:#2980b9;color:#2980b9;}
.qi i{font-size:.95rem;width:18px;text-align:center;}
#backdrop{display:none;position:fixed;inset:0;background:rgba(0,0,0,.4);z-index:999;}
#backdrop.show{display:block;}
@media(max-width:900px){.sidebar{transform:translateX(-100%)}.sidebar.open{transform:translateX(0)}.topbar{left:0}.main{margin-left:0}.hamburger{display:block}.g2{grid-template-columns:1fr}}
</style>
</head><body>
<div id="backdrop"></div>
<aside class="sidebar" id="sidebar">
  <div class="sb-brand"><a href="../index.php"><div class="logo"><i class="fas fa-bolt"></i></div><span>UtilityTrack</span></a></div>
  <div class="sb-user"><div class="av"><?= strtoupper(substr($username,0,1)) ?></div><div><div class="un"><?= htmlspecialchars($username) ?></div><div class="ur">User</div></div></div>
  <nav class="sb-nav">
    <div class="nav-lbl">Menu</div>
    <a href="dashboard.php" class="active"><i class="fas fa-home"></i> Dashboard</a>
    <a href="work_orders.php"><i class="fas fa-clipboard-list"></i> Work Orders</a>
    <a href="tasks.php"><i class="fas fa-tasks"></i> My Tasks</a>
    <a href="reports.php"><i class="fas fa-chart-line"></i> Reports</a>
    <a href="feedback.php"><i class="fas fa-comment-dots"></i> Feedback</a>
    <div class="nav-lbl">Account</div>
    <a href="profile.php"><i class="fas fa-user"></i> Profile</a>
    <a href="reset_password.php"><i class="fas fa-key"></i> Reset Password</a>
  </nav>
  <div class="sb-foot"><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></div>
</aside>
<div class="topbar">
  <div class="topbar-l"><button class="hamburger" id="hamburger"><i class="fas fa-bars"></i></button><span class="page-title">Dashboard</span></div>
  <div class="topbar-r">
    <?php include __DIR__.'/../admin/notif_bell.php'; ?>
    <div class="tu"><i class="fas fa-user-circle" style="font-size:1.1rem;color:#2980b9;"></i><span><?= htmlspecialchars($username) ?></span></div>
  </div>
</div>
<main class="main">
  <div class="stats-row">
    <div class="sc"><div class="si"><i class="fas fa-tasks"></i></div><div><div class="sl">Pending Tasks</div><div class="sv"><?= count($pendingTasks) ?></div></div></div>
    <div class="sc g"><div class="si"><i class="fas fa-check-circle"></i></div><div><div class="sl">Completed</div><div class="sv"><?= count($completedTasks) ?></div></div></div>
    <div class="sc o"><div class="si"><i class="fas fa-clipboard-list"></i></div><div><div class="sl">Work Orders</div><div class="sv"><?= $totalWorkOrders ?></div></div></div>
  </div>
  <div class="g2">
    <div class="card">
      <div class="ch"><h3><i class="fas fa-tasks" style="color:#f39c12;margin-right:5px;"></i> My Tasks</h3><a href="tasks.php">View all →</a></div>
      <div class="cb">
        <?php if(empty($pendingTasks)&&empty($completedTasks)): ?><div class="es"><i class="fas fa-clipboard"></i>No tasks yet.</div>
        <?php else: ?><ul class="tl">
          <?php foreach(array_slice($pendingTasks,0,4) as $t): ?><li><span class="tn"><?= htmlspecialchars($t['task']) ?></span><span class="badge bp">Pending</span></li><?php endforeach; ?>
          <?php foreach(array_slice($completedTasks,0,2) as $t): ?><li><span class="tn" style="opacity:.6;"><?= htmlspecialchars($t['task']) ?></span><span class="badge bd">Done</span></li><?php endforeach; ?>
        </ul><?php endif; ?>
      </div>
    </div>
    <div class="card">
      <div class="ch"><h3><i class="fas fa-clipboard-list" style="color:#2980b9;margin-right:5px;"></i> Work Orders</h3><a href="work_orders.php">View all →</a></div>
      <div class="cb">
        <?php if(empty($woRecent)): ?><div class="es"><i class="fas fa-folder-open"></i>No work orders.</div>
        <?php else: ?><ul class="tl">
          <?php foreach($woRecent as $wo):
            $wb=match($wo['status']){'Completed'=>'bd','In Progress'=>'bpr','Cancelled'=>'bc',default=>'bp'}; ?>
            <li><span class="tn"><?= htmlspecialchars($wo['title']) ?></span><span class="badge <?= $wb ?>"><?= htmlspecialchars($wo['status']) ?></span></li>
          <?php endforeach; ?>
        </ul><?php endif; ?>
      </div>
    </div>
  </div>
  <div class="card">
    <div class="ch"><h3><i class="fas fa-th" style="color:#8e44ad;margin-right:5px;"></i> Quick Access</h3></div>
    <div class="cb">
      <div class="ql">
        <a href="tasks.php"        class="qi"><i class="fas fa-tasks"         style="color:#f39c12;"></i> My Tasks</a>
        <a href="work_orders.php"  class="qi"><i class="fas fa-clipboard-list"style="color:#2980b9;"></i> Work Orders</a>
        <a href="reports.php"      class="qi"><i class="fas fa-chart-line"    style="color:#8e44ad;"></i> Reports</a>
        <a href="feedback.php"     class="qi"><i class="fas fa-comment-dots"  style="color:#27ae60;"></i> Feedback</a>
        <a href="profile.php"      class="qi"><i class="fas fa-user"          style="color:#e67e22;"></i> Profile</a>
        <a href="reset_password.php" class="qi"><i class="fas fa-key"         style="color:#e74c3c;"></i> Reset Password</a>
      </div>
    </div>
  </div>
</main>
<script>const s=document.getElementById('sidebar'),h=document.getElementById('hamburger'),b=document.getElementById('backdrop');h.onclick=()=>{s.classList.toggle('open');b.classList.toggle('show');};b.onclick=()=>{s.classList.remove('open');b.classList.remove('show');};</script>
</body></html>
