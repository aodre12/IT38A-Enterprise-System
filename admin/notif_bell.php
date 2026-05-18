<?php
// admin/notif_bell.php
$_notifUserId = (int)($_SESSION['user_id'] ?? 0);
$_notifTableExists = false;
if (!empty($conn)) {
    $r = $conn->query("SHOW TABLES LIKE 'notifications'");
    $_notifTableExists = ($r && $r->num_rows > 0);
}
$_unreadCount = 0;
$_notifItems  = [];
if ($_notifTableExists && $_notifUserId) {
    $s = $conn->prepare("SELECT id, message, link, is_read, created_at FROM notifications WHERE user_id=? ORDER BY created_at DESC LIMIT 8");
    $s->bind_param("i", $_notifUserId);
    $s->execute();
    $res = $s->get_result();
    while ($row = $res->fetch_assoc()) {
        if (!$row['is_read']) $_unreadCount++;
        $_notifItems[] = $row;
    }
    $s->close();
}
?>
<style>
.notif-wrap{position:relative;}
.notif-btn{background:none;border:none;cursor:pointer;font-size:1.2rem;color:#7f8c8d;position:relative;padding:6px;border-radius:8px;transition:background .2s;}
.notif-btn:hover{background:#f0f4f8;color:#2c3e50;}
.notif-badge{position:absolute;top:2px;right:2px;background:#e74c3c;color:#fff;font-size:0.6rem;font-weight:700;width:16px;height:16px;border-radius:50%;display:flex;align-items:center;justify-content:center;pointer-events:none;}
.notif-dropdown{display:none;position:absolute;right:0;top:calc(100% + 8px);width:320px;background:#fff;border-radius:12px;box-shadow:0 8px 30px rgba(0,0,0,0.15);z-index:9999;overflow:hidden;}
.notif-dropdown.open{display:block;}
.notif-header{display:flex;align-items:center;justify-content:space-between;padding:14px 16px;border-bottom:1px solid #e0e6ed;font-weight:600;font-size:0.9rem;color:#2c3e50;}
.notif-header button{background:none;border:none;cursor:pointer;font-size:0.75rem;color:#2980b9;font-weight:600;}
.notif-list{max-height:300px;overflow-y:auto;}
.notif-item{display:flex;gap:12px;padding:12px 16px;border-bottom:1px solid #f0f4f8;text-decoration:none;transition:background .15s;}
.notif-item:hover{background:#f7f9fc;}
.notif-item.unread{background:#eef6ff;}
.notif-item .ni-icon{width:36px;height:36px;border-radius:50%;background:#e8f4fd;display:flex;align-items:center;justify-content:center;color:#2980b9;flex-shrink:0;font-size:0.9rem;}
.notif-item.unread .ni-icon{background:#2980b9;color:#fff;}
.notif-item .ni-text{flex:1;min-width:0;}
.notif-item .ni-msg{font-size:0.83rem;color:#2c3e50;line-height:1.4;}
.notif-item .ni-time{font-size:0.72rem;color:#7f8c8d;margin-top:3px;}
.notif-empty{padding:24px;text-align:center;color:#7f8c8d;font-size:0.85rem;}
</style>
<div class="notif-wrap" id="notifWrap">
  <button class="notif-btn" id="notifBtn" title="Notifications">
    <i class="fas fa-bell"></i>
    <?php if ($_unreadCount > 0): ?>
      <span class="notif-badge"><?= min($_unreadCount,9) ?><?= $_unreadCount>9?'+':'' ?></span>
    <?php endif; ?>
  </button>
  <div class="notif-dropdown" id="notifDropdown">
    <div class="notif-header">
      <span>Notifications<?php if($_unreadCount>0): ?> <span style="color:#e74c3c;">(<?= $_unreadCount ?>)</span><?php endif; ?></span>
      <?php if($_unreadCount>0): ?><button id="markAllRead">Mark all read</button><?php endif; ?>
    </div>
    <div class="notif-list">
      <?php if(empty($_notifItems)): ?>
        <div class="notif-empty"><i class="fas fa-bell-slash" style="font-size:1.5rem;margin-bottom:8px;display:block;"></i>No notifications</div>
      <?php else: foreach($_notifItems as $n): ?>
        <a href="<?= htmlspecialchars($n['link']) ?>" class="notif-item <?= !$n['is_read']?'unread':'' ?>">
          <div class="ni-icon"><i class="fas fa-info"></i></div>
          <div class="ni-text">
            <div class="ni-msg"><?= htmlspecialchars($n['message']) ?></div>
            <div class="ni-time"><?= date('M j, g:i a', strtotime($n['created_at'])) ?></div>
          </div>
        </a>
      <?php endforeach; endif; ?>
    </div>
  </div>
</div>
<script>
(function(){
  const btn=document.getElementById('notifBtn'),drop=document.getElementById('notifDropdown'),wrap=document.getElementById('notifWrap'),markBtn=document.getElementById('markAllRead');
  btn.addEventListener('click',e=>{e.stopPropagation();drop.classList.toggle('open');});
  document.addEventListener('click',e=>{if(!wrap.contains(e.target))drop.classList.remove('open');});
  if(markBtn){markBtn.addEventListener('click',()=>{fetch('../user/notifications.php',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:'mark_read=1'}).then(()=>location.reload());});}
})();
</script>
