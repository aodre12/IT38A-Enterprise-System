<?php
/**
 * user/layout.php — shared layout helpers for user pages
 * Include AFTER session_start() and auth checks.
 * Provides: user_sidebar(), user_topbar(), user_styles()
 */

function user_styles(string $extra = ''): void { ?>
<style>
:root{--navy:#001F54;--blue:#0056b3;--accent:#27ae60;--warning:#f39c12;--danger:#e74c3c;--bg:#f0f4f8;--card:#fff;--text:#2c3e50;--muted:#7f8c8d;--border:#e0e6ed;--sw:260px;}
*{margin:0;padding:0;box-sizing:border-box;}
body{font-family:'Segoe UI',sans-serif;background:var(--bg);color:var(--text);display:flex;min-height:100vh;}
/* SIDEBAR */
.sidebar{width:var(--sw);background:var(--navy);color:#fff;display:flex;flex-direction:column;position:fixed;top:0;left:0;height:100vh;z-index:1000;transition:transform .3s;overflow-y:auto;}
.sb-brand{padding:22px 20px 16px;border-bottom:1px solid rgba(255,255,255,.1);}
.sb-brand a{text-decoration:none;display:flex;align-items:center;gap:10px;}
.sb-logo{width:32px;height:32px;background:rgba(79,195,247,.2);border-radius:8px;display:flex;align-items:center;justify-content:center;color:#4fc3f7;font-size:.95rem;}
.sb-brand span{font-size:1.05rem;font-weight:700;color:#fff;}
.sb-user{display:flex;align-items:center;gap:12px;padding:14px 20px;border-bottom:1px solid rgba(255,255,255,.1);}
.sb-av{width:36px;height:36px;border-radius:50%;background:#2980b9;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.95rem;color:#fff;flex-shrink:0;}
.sb-un{font-size:.86rem;font-weight:600;color:#fff;}
.sb-ur{font-size:.7rem;color:rgba(255,255,255,.5);}
.sb-nav{padding:12px 0;flex:1;}
.nav-lbl{font-size:.63rem;text-transform:uppercase;letter-spacing:1.2px;color:rgba(255,255,255,.35);padding:10px 20px 4px;}
.sb-nav a{display:flex;align-items:center;gap:11px;padding:10px 20px;color:rgba(255,255,255,.75);text-decoration:none;font-size:.86rem;transition:background .2s,color .2s;border-left:3px solid transparent;}
.sb-nav a:hover,.sb-nav a.active{background:rgba(255,255,255,.08);color:#fff;border-left-color:#4fc3f7;}
.sb-nav a i{width:16px;text-align:center;font-size:.88rem;}
.sb-foot{padding:14px 20px;border-top:1px solid rgba(255,255,255,.1);}
.sb-foot a{display:flex;align-items:center;gap:10px;color:rgba(255,255,255,.6);text-decoration:none;font-size:.83rem;}
.sb-foot a:hover{color:#fff;}
/* TOPBAR */
.topbar{position:fixed;top:0;left:var(--sw);right:0;height:56px;background:#fff;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;padding:0 22px;z-index:900;box-shadow:0 1px 4px rgba(0,0,0,.06);}
.topbar-l{display:flex;align-items:center;gap:12px;}
.page-title{font-size:.98rem;font-weight:600;}
.topbar-r{display:flex;align-items:center;gap:10px;}
.tu{display:flex;align-items:center;gap:8px;font-size:.82rem;color:var(--muted);}
.hamburger{display:none;background:none;border:none;font-size:1.2rem;cursor:pointer;color:var(--text);}
/* MAIN */
.main{margin-left:var(--sw);margin-top:56px;padding:24px;flex:1;min-width:0;}
/* CARDS */
.page-card{background:var(--card);border-radius:14px;box-shadow:0 2px 10px rgba(0,0,0,.06);overflow:hidden;margin-bottom:20px;}
.pc-header{display:flex;align-items:center;justify-content:space-between;padding:18px 22px;border-bottom:1px solid var(--border);}
.pc-header h2{font-size:1rem;font-weight:700;color:var(--text);display:flex;align-items:center;gap:8px;}
.pc-body{padding:22px;}
/* BADGES */
.badge{display:inline-flex;align-items:center;padding:3px 10px;border-radius:20px;font-size:.72rem;font-weight:700;}
.b-pending{background:#fff3cd;color:#856404;}
.b-done{background:#d4edda;color:#155724;}
.b-progress{background:#cce5ff;color:#004085;}
.b-cancelled{background:#f8d7da;color:#721c24;}
/* BUTTONS */
.btn{display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border:none;border-radius:8px;font-size:.83rem;font-weight:600;cursor:pointer;text-decoration:none;transition:opacity .2s,transform .1s;}
.btn:hover{opacity:.88;transform:translateY(-1px);}
.btn:active{transform:translateY(0);}
.btn-primary{background:var(--blue);color:#fff;}
.btn-success{background:var(--accent);color:#fff;}
.btn-danger{background:var(--danger);color:#fff;}
.btn-sm{padding:5px 12px;font-size:.78rem;}
.btn-outline{background:transparent;border:1.5px solid var(--blue);color:var(--blue);}
.btn-outline:hover{background:var(--blue);color:#fff;}
/* FORM */
.form-group{margin-bottom:18px;}
.form-group label{display:block;font-size:.83rem;font-weight:600;color:var(--text);margin-bottom:6px;}
.form-group input,.form-group textarea,.form-group select{width:100%;padding:10px 13px;border:1.5px solid var(--border);border-radius:9px;font-size:.9rem;font-family:inherit;transition:border-color .2s,box-shadow .2s;background:#fafafa;}
.form-group input:focus,.form-group textarea:focus,.form-group select:focus{outline:none;border-color:var(--blue);box-shadow:0 0 0 3px rgba(0,86,179,.1);background:#fff;}
.form-group textarea{resize:vertical;min-height:110px;}
.alert{padding:12px 14px;border-radius:9px;font-size:.85rem;margin-bottom:18px;display:flex;align-items:center;gap:8px;}
.alert-error{background:#fdf2f2;color:#c0392b;border:1px solid #f5c6cb;}
.alert-success{background:#f0fdf4;color:#166534;border:1px solid #bbf7d0;}
/* TABLE */
.data-table{width:100%;border-collapse:collapse;}
.data-table th{background:#f7f9fc;padding:11px 14px;text-align:left;font-size:.75rem;text-transform:uppercase;letter-spacing:.5px;color:var(--muted);border-bottom:1px solid var(--border);}
.data-table td{padding:12px 14px;border-bottom:1px solid var(--border);font-size:.86rem;vertical-align:middle;}
.data-table tr:last-child td{border-bottom:none;}
.data-table tr:hover td{background:#f7f9fc;}
/* EMPTY */
.empty-state{text-align:center;padding:40px 20px;color:var(--muted);}
.empty-state i{font-size:2.5rem;margin-bottom:12px;display:block;opacity:.3;}
.empty-state p{font-size:.88rem;}
/* BACKDROP */
#backdrop{display:none;position:fixed;inset:0;background:rgba(0,0,0,.4);z-index:999;}
#backdrop.show{display:block;}
@media(max-width:900px){
  .sidebar{transform:translateX(-100%);}
  .sidebar.open{transform:translateX(0);}
  .topbar{left:0;}
  .main{margin-left:0;}
  .hamburger{display:block;}
}
<?= $extra ?>
</style>
<?php }

function user_sidebar(string $active = ''): void {
    $u = $_SESSION['username'] ?? 'User';
    $pages = [
        'dashboard.php'      => ['fas fa-home',         'Dashboard'],
        'work_orders.php'    => ['fas fa-clipboard-list','Work Orders'],
        'tasks.php'          => ['fas fa-tasks',         'My Tasks'],
        'reports.php'        => ['fas fa-chart-line',    'Reports'],
        'feedback.php'       => ['fas fa-comment-dots',  'Feedback'],
    ];
    $account = [
        'profile.php'        => ['fas fa-user',          'Profile'],
        'reset_password.php' => ['fas fa-key',           'Reset Password'],
    ];
    echo '<div id="backdrop"></div>';
    echo '<aside class="sidebar" id="sidebar">';
    echo '<div class="sb-brand"><a href="../index.php"><div class="sb-logo"><i class="fas fa-bolt"></i></div><span>UtilityTrack</span></a></div>';
    echo '<div class="sb-user"><div class="sb-av">' . strtoupper(substr($u,0,1)) . '</div><div><div class="sb-un">' . htmlspecialchars($u) . '</div><div class="sb-ur">User</div></div></div>';
    echo '<nav class="sb-nav"><div class="nav-lbl">Menu</div>';
    foreach ($pages as $href => [$icon, $label]) {
        $cls = (basename($active) === $href || $active === $href) ? ' class="active"' : '';
        echo "<a href=\"{$href}\"{$cls}><i class=\"{$icon}\"></i> {$label}</a>";
    }
    echo '<div class="nav-lbl">Account</div>';
    foreach ($account as $href => [$icon, $label]) {
        $cls = (basename($active) === $href || $active === $href) ? ' class="active"' : '';
        echo "<a href=\"{$href}\"{$cls}><i class=\"{$icon}\"></i> {$label}</a>";
    }
    echo '</nav>';
    echo '<div class="sb-foot"><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></div>';
    echo '</aside>';
}

function user_topbar(string $title, $conn): void {
    $u = $_SESSION['username'] ?? '';
    ob_start();
    include __DIR__ . '/../admin/notif_bell.php';
    $bell = ob_get_clean();
    echo <<<HTML
<div class="topbar">
  <div class="topbar-l">
    <button class="hamburger" id="hamburger"><i class="fas fa-bars"></i></button>
    <span class="page-title">{$title}</span>
  </div>
  <div class="topbar-r">
    {$bell}
    <div class="tu"><i class="fas fa-user-circle" style="font-size:1.1rem;color:#2980b9;"></i><span>{$u}</span></div>
  </div>
</div>
HTML;
}

function user_sidebar_script(): void { ?>
<script>
const _s=document.getElementById('sidebar'),_h=document.getElementById('hamburger'),_b=document.getElementById('backdrop');
_h.onclick=()=>{_s.classList.toggle('open');_b.classList.toggle('show');};
_b.onclick=()=>{_s.classList.remove('open');_b.classList.remove('show');};
</script>
<?php }
