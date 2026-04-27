<?php
session_start();
require '../Config.php';
require 'layout.php';
if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') { header('Location: ../admin/dashboard.php'); exit; }
if (!isset($_SESSION['user_id'])) { header('Location: ../login.php'); exit; }

$userId   = (int)$_SESSION['user_id'];
$username = $_SESSION['username'];
$error = ''; $success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = trim($_POST['name']  ?? '');
    $email = trim($_POST['email'] ?? '');
    if (!$name || !$email) $error = "Name and Email are required.";
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $error = "Invalid email format.";
    else {
        $s = $conn->prepare("UPDATE personnel SET name=?, email=? WHERE id=?");
        $s->bind_param("ssi", $name, $email, $userId);
        $success = $s->execute() ? "Profile updated!" : "Failed to update.";
        $s->close();
    }
}

// Fetch
$name = $email = $role = $status = '';
$s = $conn->prepare("SELECT name, email, role, status FROM personnel WHERE id=?");
$s->bind_param("i", $userId); $s->execute();
$s->bind_result($name, $email, $role, $status); $s->fetch(); $s->close();

// Also get user created_at
$uc = $conn->prepare("SELECT created_at FROM users WHERE id=?");
$uc->bind_param("i", $userId); $uc->execute();
$uc->bind_result($createdAt); $uc->fetch(); $uc->close();
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>My Profile | UtilityTrack</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
<?php user_styles('
.profile-avatar{width:80px;height:80px;border-radius:50%;background:linear-gradient(135deg,#001F54,#2980b9);display:flex;align-items:center;justify-content:center;font-size:2rem;font-weight:800;color:#fff;margin:0 auto 16px;}
.profile-header{text-align:center;padding:24px 22px;border-bottom:1px solid var(--border);}
.profile-header .pname{font-size:1.1rem;font-weight:700;}
.profile-header .pusername{font-size:.82rem;color:var(--muted);}
.info-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:20px;}
.info-item{background:var(--bg);border-radius:10px;padding:14px;}
.info-item .ii-label{font-size:.72rem;text-transform:uppercase;letter-spacing:.5px;color:var(--muted);margin-bottom:4px;}
.info-item .ii-value{font-size:.9rem;font-weight:600;color:var(--text);}
'); ?>
</head><body>
<?php user_sidebar('profile.php'); ?>
<?php user_topbar('My Profile', $conn); ?>
<main class="main">

  <div class="page-card" style="max-width:560px;">
    <!-- Profile header -->
    <div class="profile-header">
      <div class="profile-avatar"><?= strtoupper(substr($username,0,1)) ?></div>
      <div class="pname"><?= htmlspecialchars($name ?: $username) ?></div>
      <div class="pusername">@<?= htmlspecialchars($username) ?></div>
    </div>

    <!-- Info grid -->
    <div class="pc-body">
      <div class="info-grid">
        <div class="info-item">
          <div class="ii-label"><i class="fas fa-user-tag"></i> Role</div>
          <div class="ii-value"><?= htmlspecialchars($role ?: 'User') ?></div>
        </div>
        <div class="info-item">
          <div class="ii-label"><i class="fas fa-circle"></i> Status</div>
          <div class="ii-value">
            <span class="badge <?= ($status==='Active'||!$status)?'b-done':'b-cancelled' ?>"><?= htmlspecialchars($status ?: 'Active') ?></span>
          </div>
        </div>
        <?php if ($createdAt): ?>
        <div class="info-item" style="grid-column:1/-1;">
          <div class="ii-label"><i class="fas fa-calendar-alt"></i> Member Since</div>
          <div class="ii-value"><?= date('F j, Y', strtotime($createdAt)) ?></div>
        </div>
        <?php endif; ?>
      </div>

      <!-- Edit form -->
      <div style="border-top:1px solid var(--border);padding-top:20px;margin-top:4px;">
        <p style="font-size:.85rem;font-weight:600;margin-bottom:14px;color:var(--text);">Edit Profile</p>
        <?php if ($error): ?><div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div><?php endif; ?>
        <?php if ($success): ?><div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($success) ?></div><?php endif; ?>
        <form method="POST">
          <div class="form-group">
            <label>Full Name</label>
            <input type="text" name="name" value="<?= htmlspecialchars($name) ?>" placeholder="Your full name" required>
          </div>
          <div class="form-group">
            <label>Email Address</label>
            <input type="email" name="email" value="<?= htmlspecialchars($email) ?>" placeholder="your@email.com" required>
          </div>
          <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:12px;">
            <i class="fas fa-save"></i> Save Changes
          </button>
        </form>
      </div>
    </div>
  </div>

</main>
<?php user_sidebar_script(); ?>
</body></html>
