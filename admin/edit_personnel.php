<?php
session_start();
require '../Config.php';
require '../csrf.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') { header('Location: ../login.php'); exit; }
$username = $_SESSION['username'];
$id = (int)($_GET['id'] ?? 0);
if (!$id) { header('Location: dashboard.php'); exit; }
$stmt = $conn->prepare("SELECT name, role, status FROM personnel WHERE id=?");
$stmt->bind_param("i", $id); $stmt->execute();
$p = $stmt->get_result()->fetch_assoc();
if (!$p) { header('Location: dashboard.php'); exit; }
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify()) { $error = 'Invalid request.'; }
    else {
        $name = trim($_POST['name'] ?? ''); $role = trim($_POST['role'] ?? ''); $status = $_POST['status'] ?? 'Active';
        if ($name && $role) {
            $u = $conn->prepare("UPDATE personnel SET name=?, role=?, status=? WHERE id=?");
            $u->bind_param("sssi", $name, $role, $status, $id);
            if ($u->execute()) { header('Location: dashboard.php'); exit; }
            else $error = "Failed to update.";
        } else $error = "Name and Role are required.";
    }
}
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Edit Personnel | UtilityTrack</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
<style><?php include __DIR__.'/shared_styles.php'; echo $adminStyles; ?>
.form-group{margin-bottom:18px;}.form-group label{display:block;font-weight:600;font-size:.88rem;margin-bottom:6px;color:var(--text);}
.form-group input,.form-group select{width:100%;padding:10px 12px;border:1px solid var(--border);border-radius:8px;font-size:.9rem;}
.form-group input:focus,.form-group select:focus{outline:none;border-color:var(--accent);}
.form-error{color:var(--danger);font-size:.85rem;margin-bottom:14px;}
</style></head><body>
<?php include __DIR__.'/sidebar.php'; ?>
<div class="topbar">
  <div style="display:flex;align-items:center;gap:14px;"><button class="hamburger" id="hamburger"><i class="fas fa-bars"></i></button><span class="page-title">Edit Personnel</span></div>
  <div class="topbar-right"><?php include __DIR__.'/notif_bell.php'; ?><span><i class="fas fa-user-circle"></i> <?= htmlspecialchars($username) ?></span></div>
</div>
<main class="main">
  <div class="section-card" style="max-width:520px;">
    <div class="section-header"><h2><i class="fas fa-user-edit"></i> Edit Personnel</h2></div>
    <div class="section-body">
      <?php if($error): ?><div class="form-error"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div><?php endif; ?>
      <form method="POST">
        <?= csrf_field() ?>
        <div class="form-group"><label>Full Name</label><input type="text" name="name" value="<?= htmlspecialchars($p['name']) ?>" required></div>
        <div class="form-group"><label>Role</label><input type="text" name="role" value="<?= htmlspecialchars($p['role']) ?>" required></div>
        <div class="form-group"><label>Status</label>
          <select name="status">
            <option value="Active" <?= $p['status']==='Active'?'selected':'' ?>>Active</option>
            <option value="Inactive" <?= $p['status']==='Inactive'?'selected':'' ?>>Inactive</option>
          </select>
        </div>
        <button type="submit" class="btn btn-success" style="width:100%;justify-content:center;padding:11px;"><i class="fas fa-save"></i> Save Changes</button>
      </form>
      <a href="dashboard.php" class="btn btn-primary" style="width:100%;justify-content:center;padding:11px;margin-top:10px;"><i class="fas fa-arrow-left"></i> Back</a>
    </div>
  </div>
</main>
<script>const s=document.getElementById('sidebar'),h=document.getElementById('hamburger'),b=document.getElementById('backdrop');h.onclick=()=>{s.classList.toggle('open');b.classList.toggle('show');};b.onclick=()=>{s.classList.remove('open');b.classList.remove('show');};</script>
</body></html>
