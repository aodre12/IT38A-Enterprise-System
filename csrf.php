<?php
// csrf.php — CSRF token helpers
// Include after session_start()

function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string {
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrf_token()) . '">';
}

function csrf_verify(): bool {
    $token = $_POST['csrf_token'] ?? '';
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Render the admin topbar HTML.
 * Call after including admin_sidebar.php.
 */
function admin_topbar(string $pageTitle, string $username, $conn): void {
    ob_start();
    include __DIR__ . '/notif_bell.php';
    $bell = ob_get_clean();
    echo <<<HTML
<div class="topbar">
  <div style="display:flex;align-items:center;gap:14px;">
    <button class="hamburger" id="hamburger"><i class="fas fa-bars"></i></button>
    <span class="page-title">{$pageTitle}</span>
  </div>
  <div class="topbar-right">
    {$bell}
    <span><i class="fas fa-user-circle"></i> {$username}</span>
  </div>
</div>
HTML;
}
