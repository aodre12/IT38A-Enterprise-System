<?php
$adminStyles = '
:root {
  --sidebar-bg: #001F54;
  --sidebar-hover: #0a3a8a;
  --accent: #2980b9;
  --accent2: #27ae60;
  --danger: #e74c3c;
  --warning: #f39c12;
  --bg: #f0f4f8;
  --card-bg: #ffffff;
  --text: #2c3e50;
  --muted: #7f8c8d;
  --border: #e0e6ed;
}
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family:"Segoe UI",sans-serif; background:var(--bg); color:var(--text); display:flex; min-height:100vh; }
.sidebar { width:260px; background:var(--sidebar-bg); color:#fff; display:flex; flex-direction:column; position:fixed; top:0; left:0; height:100vh; z-index:1000; transition:transform .3s; overflow-y:auto; }
.sidebar-brand { padding:28px 24px 20px; border-bottom:1px solid rgba(255,255,255,0.1); }
.sidebar-brand h1 { font-size:1.3rem; font-weight:700; color:#fff; }
.sidebar-brand p { font-size:0.78rem; color:rgba(255,255,255,0.55); margin-top:4px; }
.sidebar-user { display:flex; align-items:center; gap:12px; padding:18px 24px; border-bottom:1px solid rgba(255,255,255,0.1); }
.sidebar-user .avatar { width:40px; height:40px; border-radius:50%; background:var(--accent); display:flex; align-items:center; justify-content:center; font-size:1.1rem; font-weight:700; color:#fff; flex-shrink:0; }
.sidebar-user .info .name { font-size:0.9rem; font-weight:600; }
.sidebar-user .info .role { font-size:0.75rem; color:rgba(255,255,255,0.55); }
.sidebar-nav { padding:16px 0; flex:1; }
.nav-section-label { font-size:0.68rem; text-transform:uppercase; letter-spacing:1.2px; color:rgba(255,255,255,0.4); padding:12px 24px 6px; }
.sidebar-nav a { display:flex; align-items:center; gap:12px; padding:11px 24px; color:rgba(255,255,255,0.8); text-decoration:none; font-size:0.9rem; transition:background .2s,color .2s; border-left:3px solid transparent; }
.sidebar-nav a:hover, .sidebar-nav a.active { background:var(--sidebar-hover); color:#fff; border-left-color:#4fc3f7; }
.sidebar-nav a i { width:18px; text-align:center; font-size:0.95rem; }
.sidebar-footer { padding:16px 24px; border-top:1px solid rgba(255,255,255,0.1); }
.sidebar-footer a { display:flex; align-items:center; gap:10px; color:rgba(255,255,255,0.7); text-decoration:none; font-size:0.88rem; }
.sidebar-footer a:hover { color:#fff; }
.topbar { position:fixed; top:0; left:260px; right:0; height:60px; background:#fff; border-bottom:1px solid var(--border); display:flex; align-items:center; justify-content:space-between; padding:0 28px; z-index:900; box-shadow:0 1px 4px rgba(0,0,0,0.06); }
.topbar .page-title { font-size:1.1rem; font-weight:600; color:var(--text); }
.topbar .topbar-right { display:flex; align-items:center; gap:16px; }
.topbar .topbar-right span { font-size:0.85rem; color:var(--muted); }
.hamburger { display:none; background:none; border:none; font-size:1.3rem; cursor:pointer; color:var(--text); }
.main { margin-left:260px; margin-top:60px; padding:28px; flex:1; min-width:0; }
.stats-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(200px,1fr)); gap:20px; margin-bottom:28px; }
.stat-card { background:var(--card-bg); border-radius:12px; padding:22px 20px; box-shadow:0 2px 8px rgba(0,0,0,0.06); display:flex; align-items:center; gap:16px; border-left:4px solid var(--accent); }
.stat-card.green { border-left-color:var(--accent2); }
.stat-card.orange { border-left-color:var(--warning); }
.stat-card.red { border-left-color:var(--danger); }
.stat-card .icon { width:48px; height:48px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:1.3rem; color:#fff; background:var(--accent); flex-shrink:0; }
.stat-card.green .icon { background:var(--accent2); }
.stat-card.orange .icon { background:var(--warning); }
.stat-card.red .icon { background:var(--danger); }
.stat-card .stat-info .label { font-size:0.78rem; color:var(--muted); text-transform:uppercase; letter-spacing:.5px; }
.stat-card .stat-info .value { font-size:1.8rem; font-weight:700; color:var(--text); line-height:1.2; }
.section-card { background:var(--card-bg); border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,0.06); margin-bottom:28px; overflow:hidden; }
.section-header { display:flex; align-items:center; justify-content:space-between; padding:18px 24px; border-bottom:1px solid var(--border); }
.section-header h2 { font-size:1rem; font-weight:600; color:var(--text); }
.section-body { padding:20px 24px; }
.data-table { width:100%; border-collapse:collapse; }
.data-table th { background:#f7f9fc; padding:11px 14px; text-align:left; font-size:0.78rem; text-transform:uppercase; letter-spacing:.5px; color:var(--muted); border-bottom:1px solid var(--border); }
.data-table td { padding:12px 14px; border-bottom:1px solid var(--border); font-size:0.88rem; vertical-align:middle; }
.data-table tr:last-child td { border-bottom:none; }
.data-table tr:hover td { background:#f7f9fc; }
.badge { display:inline-block; padding:3px 10px; border-radius:20px; font-size:0.75rem; font-weight:600; }
.badge-active { background:#d4edda; color:#155724; }
.badge-inactive { background:#f8d7da; color:#721c24; }
.badge-pending { background:#fff3cd; color:#856404; }
.badge-done { background:#d4edda; color:#155724; }
.btn { display:inline-flex; align-items:center; gap:6px; padding:7px 14px; border:none; border-radius:7px; font-size:0.82rem; font-weight:600; cursor:pointer; text-decoration:none; transition:opacity .2s; }
.btn:hover { opacity:.85; }
.btn-primary { background:var(--accent); color:#fff; }
.btn-success { background:var(--accent2); color:#fff; }
.btn-danger { background:var(--danger); color:#fff; }
.btn-warning { background:var(--warning); color:#fff; }
.btn-sm { padding:5px 10px; font-size:0.78rem; }
.quick-actions { display:flex; flex-wrap:wrap; gap:10px; }
#backdrop { display:none; position:fixed; inset:0; background:rgba(0,0,0,.4); z-index:999; }
#backdrop.show { display:block; }
@media (max-width:900px) {
  .sidebar { transform:translateX(-100%); }
  .sidebar.open { transform:translateX(0); }
  .topbar { left:0; }
  .main { margin-left:0; }
  .hamburger { display:block; }
}
';
