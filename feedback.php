<?php
session_start();
require 'config.php';

if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    header('Location: admin_dashboard.php');
    exit;
}
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = trim($_POST['message'] ?? '');
    if ($message === '') {
        $error = 'Feedback cannot be empty.';
    } else {
        // Normally insert into DB; here we simulate success
        // $stmt = $conn->prepare("INSERT INTO feedback (user_id,message) VALUES (?,?)");
        // $stmt->bind_param("is", $_SESSION['user_id'], $message);
        // $stmt->execute();
        $success = 'Thank you for your feedback!';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Feedback | UtilityTrack</title>
  <style>
    body { margin:0; font-family:'Segoe UI',sans-serif; background:#fef6ec; display:flex; flex-direction:column; align-items:center; }
    .container { position:relative; background:#fff; width:500px; padding:40px 30px; border-radius:20px; box-shadow:0 0 12px rgba(0,0,0,0.1); margin:60px 0; }
    .header-wave { position:absolute; top:0; left:0; width:100%; height:100px; background:#001F54; border-top-left-radius:20px; border-top-right-radius:20px; clip-path:ellipse(100% 80% at 50% 0%); }
    h2 { margin-top:100px; font-size:28px; color:#000; text-align:center; }
    form { margin-top:30px; }
    textarea { width:100%; height:120px; padding:12px; border:1px solid #ccc; border-radius:8px; font-size:16px; resize:vertical; }
    button { width:100%; padding:12px; margin-top:15px; background:#0056b3; color:#fff; border:none; border-radius:8px; font-size:16px; cursor:pointer; }
    button:hover { background:#003d80; }
    .error { color:red; text-align:center; margin-top:10px; }
    .success { color:green; text-align:center; margin-top:10px; }
    .back-link { display:block; text-align:center; margin-top:20px; color:#0056b3; text-decoration:none; font-weight:bold; }
    .back-link:hover { text-decoration:underline; }
  </style>
</head>
<body>
  <div class="container">
    <div class="header-wave"></div>
    <h2>Submit Feedback</h2>
    <?php if ($error): ?><div class="error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <?php if ($success): ?><div class="success"><?= htmlspecialchars($success) ?></div><?php endif; ?>
    <form method="POST">
      <textarea name="message" placeholder="Your feedback..." required><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
      <button type="submit">Send Feedback</button>
    </form>
    <a class="back-link" href="user_dashboard.php">← Back to Dashboard</a>
  </div>
</body>
</html>
