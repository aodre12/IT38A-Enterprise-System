<?php
// admin/sidebar.php — shared admin sidebar
$__currentPage = basename($_SERVER['PHP_SELF']);
$__username    = $_SESSION['username'] ?? 'Admin';

function adminNavLink($href, $icon, $label, $current) {
    $active = (basename($href) === $current) ? ' class="active"' : '';
    echo "<a href=\"{$href}\"{$active}><i class=\"fas fa-{$icon}\"></i> {$label}</a>";
}
?>
<div id="backdrop"></div>
<aside class="sidebar" id="sidebar">
  <div class="sidebar-brand">
    <h1><i class="fas fa-bolt"></i> UtilityTrack</h1>
    <p>Enterprise Resource System</p>
  </div>
  <div class="sidebar-user">
    <div class="avatar"><?= strtoupper(substr($__username, 0, 1)) ?></div>
    <div class="info">
      <div class="name"><?= htmlspecialchars($__username) ?></div>
      <div class="role">Administrator</div>
    </div>
  </div>
  <nav class="sidebar-nav">
    <div class="nav-section-label">Main</div>
    <?php adminNavLink('dashboard.php',       'tachometer-alt', 'Dashboard',        $__currentPage); ?>
    <div class="nav-section-label">Management</div>
    <?php adminNavLink('add_personnel.php',   'user-plus',      'Add Personnel',    $__currentPage); ?>
    <?php adminNavLink('assign_task.php',     'tasks',          'Assign Task',      $__currentPage); ?>
    <?php adminNavLink('create_work.php',     'plus-circle',    'Create Work Order',$__currentPage); ?>
    <?php adminNavLink('work_orders.php',     'clipboard-list', 'Work Orders',      $__currentPage); ?>
    <?php adminNavLink('manage_users.php',    'users',          'Manage Users',     $__currentPage); ?>
    <div class="nav-section-label">Reports & Logs</div>
    <?php adminNavLink('reports.php',         'chart-bar',      'Reports',          $__currentPage); ?>
    <?php adminNavLink('feedback.php',        'comment-dots',   'Feedback',         $__currentPage); ?>
    <?php adminNavLink('view_audit_logs.php', 'history',        'Audit Logs',       $__currentPage); ?>
    <div class="nav-section-label">System</div>
    <?php adminNavLink('reset_password.php',  'key',            'Reset Password',   $__currentPage); ?>
    <?php adminNavLink('update_access.php',   'shield-alt',     'Update Access',    $__currentPage); ?>
    <?php adminNavLink('configure_system.php','cog',            'Configure System', $__currentPage); ?>
  </nav>
  <div class="sidebar-footer">
    <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
  </div>
</aside>
