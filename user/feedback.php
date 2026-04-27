<?php
session_start();
require '../Config.php';
require 'layout.php';
if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') { header('Location: ../admin/dashboard.php'); exit; }
if (!isset($_SESSION['user_id'])) { header('Location: ../login.php'); exit; }

$userId   = (int)$_SESSION['user_id'];
$username = $_SESSION['username'];

$tableExists = $conn->query("SHOW TABLES LIKE 'feedback'")->num_rows > 0;

// Past feedback
$past = [];
if ($tableExists) {
    $s = $conn->prepare("SELECT message, created_at FROM feedback WHERE user_id=? ORDER BY created_at DESC LIMIT 10");
    $s->bind_param("i", $userId); $s->execute();
    $r = $s->get_result();
    while ($row = $r->fetch_assoc()) $past[] = $row;
    $s->close();
}

$error = ''; $success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $msg = trim($_POST['message'] ?? '');
    if (!$msg) {
        $error = 'Feedback cannot be empty.';
    } elseif ($tableExists) {
        $s = $conn->prepare("INSERT INTO feedback (user_id, message) VALUES (?, ?)");
        $s->bind_param("is", $userId, $msg); $s->execute(); $s->close();
        $a = $conn->prepare("INSERT INTO audit_logs (user_id, action) VALUES (?, ?)");
        $am = 'Submitted feedback'; $a->bind_param("is", $userId, $am); $a->execute(); $a->close();
        $success = 'Thank you! Your feedback has been submitted.';
        header('Location: feedback.php?sent=1'); exit;
    } else {
        $success = 'Thank you for your feedback!';
    }
}
if (isset($_GET['sent'])) $success = 'Thank you! Your feedback has been submitted.';
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Feedback | UtilityTrack</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
<?php user_styles('
.char-count{font-size:.75rem;color:var(--muted);text-align:right;margin-top:4px;}
.past-item{padding:14px 0;border-bottom:1px solid var(--border);}
.past-item:last-child{border:none;}
.past-msg{font-size:.88rem;color:var(--text);line-height:1.6;}
.past-time{font-size:.74rem;color:var(--muted);margin-top:5px;}
.rating-stars{display:flex;gap:6px;margin-bottom:6px;}
.rating-stars i{font-size:1.2rem;color:#e0e0e0;cursor:pointer;transition:color .15s;}
.rating-stars i.active,.rating-stars i:hover{color:#f39c12;}
'); ?>
</head><body>
<?php user_sidebar('feedback.php'); ?>
<?php user_topbar('Feedback', $conn); ?>
<main class="main">

  <!-- Submit feedback -->
  <div class="page-card">
    <div class="pc-header">
      <h2><i class="fas fa-comment-dots" style="color:var(--blue);"></i> Submit Feedback</h2>
    </div>
    <div class="pc-body">
      <?php if ($error): ?><div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div><?php endif; ?>
      <?php if ($success): ?><div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($success) ?></div><?php endif; ?>

      <p style="font-size:.88rem;color:var(--muted);margin-bottom:18px;">We value your feedback. Let us know how we can improve your experience.</p>

      <form method="POST" id="fbForm">
        <div class="form-group">
          <label>Your Feedback</label>
          <textarea name="message" id="fbMsg" placeholder="Share your thoughts, suggestions, or concerns..." maxlength="1000" required><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
          <div class="char-count"><span id="charCount">0</span> / 1000</div>
        </div>
        <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:12px;">
          <i class="fas fa-paper-plane"></i> Send Feedback
        </button>
      </form>
    </div>
  </div>

  <!-- Past feedback -->
  <?php if (!empty($past)): ?>
  <div class="page-card">
    <div class="pc-header">
      <h2><i class="fas fa-history" style="color:var(--muted);"></i> Your Previous Feedback</h2>
    </div>
    <div class="pc-body">
      <?php foreach ($past as $p): ?>
        <div class="past-item">
          <div class="past-msg"><?= htmlspecialchars($p['message']) ?></div>
          <div class="past-time"><i class="fas fa-clock"></i> <?= date('M j, Y g:i a', strtotime($p['created_at'])) ?></div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>

</main>
<?php user_sidebar_script(); ?>
<script>
const ta = document.getElementById('fbMsg');
const cc = document.getElementById('charCount');
if (ta && cc) {
  const update = () => { cc.textContent = ta.value.length; };
  ta.addEventListener('input', update);
  update();
}
</script>
</body></html>
