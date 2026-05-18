<?php
session_start();
require '../Config.php';
require 'layout.php';
if (!isset($_SESSION['user_id'])) { header('Location: ../login.php'); exit; }

$userId   = (int)$_SESSION['user_id'];
$username = $_SESSION['username'];
$error = ''; $success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $np = $_POST['new_password']     ?? '';
    $cp = $_POST['confirm_password'] ?? '';
    if (strlen($np) < 6) $error = "Password must be at least 6 characters.";
    elseif ($np !== $cp) $error = "Passwords do not match.";
    else {
        $h = password_hash($np, PASSWORD_DEFAULT);
        $s = $conn->prepare("UPDATE users SET password=? WHERE id=?");
        $s->bind_param("si", $h, $userId);
        $success = $s->execute() ? "Password updated successfully!" : "Failed to update.";
        $s->close();
    }
}
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Reset Password | UtilityTrack</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
<?php user_styles('
.pw-wrap{position:relative;}
.pw-wrap input{padding-right:44px;}
.pw-toggle{position:absolute;right:13px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--muted);font-size:.9rem;}
.strength-bar{height:4px;border-radius:4px;margin-top:6px;background:var(--border);overflow:hidden;}
.strength-fill{height:100%;border-radius:4px;transition:width .3s,background .3s;}
.strength-label{font-size:.72rem;margin-top:4px;}
'); ?>
</head><body>
<?php
// If admin, redirect to admin reset
if (($_SESSION['role'] ?? '') === 'admin') {
    header('Location: ../admin/reset_password.php'); exit;
}
?>
<?php user_sidebar('reset_password.php'); ?>
<?php user_topbar('Reset Password', $conn); ?>
<main class="main">

  <div class="page-card" style="max-width:460px;">
    <div class="pc-header">
      <h2><i class="fas fa-key" style="color:var(--warning);"></i> Reset Password</h2>
    </div>
    <div class="pc-body">
      <?php if ($error): ?><div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div><?php endif; ?>
      <?php if ($success): ?><div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($success) ?></div><?php endif; ?>

      <form method="POST" id="pwForm">
        <div class="form-group">
          <label>New Password</label>
          <div class="pw-wrap">
            <input type="password" id="np" name="new_password" placeholder="Min. 6 characters" required>
            <button type="button" class="pw-toggle" onclick="togglePw('np','e1')"><i class="fas fa-eye" id="e1"></i></button>
          </div>
          <div class="strength-bar"><div class="strength-fill" id="strengthFill"></div></div>
          <div class="strength-label" id="strengthLabel"></div>
        </div>
        <div class="form-group">
          <label>Confirm Password</label>
          <div class="pw-wrap">
            <input type="password" id="cp" name="confirm_password" placeholder="Repeat your password" required>
            <button type="button" class="pw-toggle" onclick="togglePw('cp','e2')"><i class="fas fa-eye" id="e2"></i></button>
          </div>
        </div>
        <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:12px;">
          <i class="fas fa-save"></i> Update Password
        </button>
      </form>
    </div>
  </div>

</main>
<?php user_sidebar_script(); ?>
<script>
function togglePw(id, eyeId) {
  const inp = document.getElementById(id);
  const eye = document.getElementById(eyeId);
  const show = inp.type === 'password';
  inp.type = show ? 'text' : 'password';
  eye.className = show ? 'fas fa-eye-slash' : 'fas fa-eye';
}
// Password strength
document.getElementById('np').addEventListener('input', function() {
  const v = this.value;
  const fill = document.getElementById('strengthFill');
  const lbl  = document.getElementById('strengthLabel');
  let score = 0;
  if (v.length >= 6) score++;
  if (v.length >= 10) score++;
  if (/[A-Z]/.test(v)) score++;
  if (/[0-9]/.test(v)) score++;
  if (/[^A-Za-z0-9]/.test(v)) score++;
  const levels = [
    {w:'0%',  c:'#e74c3c', t:''},
    {w:'25%', c:'#e74c3c', t:'Weak'},
    {w:'50%', c:'#f39c12', t:'Fair'},
    {w:'75%', c:'#3498db', t:'Good'},
    {w:'90%', c:'#27ae60', t:'Strong'},
    {w:'100%',c:'#27ae60', t:'Very Strong'},
  ];
  const l = levels[Math.min(score, 5)];
  fill.style.width = l.w;
  fill.style.background = l.c;
  lbl.textContent = l.t;
  lbl.style.color = l.c;
});
</script>
</body></html>
