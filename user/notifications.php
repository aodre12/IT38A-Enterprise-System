<?php
// notifications.php — AJAX endpoint for bell notifications
session_start();
require '../Config.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

header('Content-Type: application/json');
$userId = (int)$_SESSION['user_id'];

// Mark as read
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_read'])) {
    $conn->prepare("UPDATE notifications SET is_read=1 WHERE user_id=?")->bind_param("i", $userId);
    $stmt = $conn->prepare("UPDATE notifications SET is_read=1 WHERE user_id=?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    echo json_encode(['success' => true]);
    exit;
}

// Fetch unread count + recent notifications
$notifTableExists = $conn->query("SHOW TABLES LIKE 'notifications'")->num_rows > 0;
if (!$notifTableExists) {
    echo json_encode(['count' => 0, 'items' => []]);
    exit;
}

$stmt = $conn->prepare("SELECT id, message, link, is_read, created_at FROM notifications WHERE user_id=? ORDER BY created_at DESC LIMIT 10");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$items  = [];
$unread = 0;
while ($row = $result->fetch_assoc()) {
    if (!$row['is_read']) $unread++;
    $items[] = $row;
}
$stmt->close();

echo json_encode(['count' => $unread, 'items' => $items]);
