<?php
// TEMPORARY DEBUG — DELETE AFTER USE
require 'Config.php';

$username = 'lorbelleganzan';
$password = 'ganzan15';

$stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->store_result();

echo "<pre style='font-family:monospace;font-size:14px;padding:20px;'>";

if ($stmt->num_rows === 0) {
    echo "❌ User '$username' NOT FOUND in database.\n\n";
    echo "Run setup_admin.php first!\n";
} else {
    $stmt->bind_result($id, $uname, $hash, $role);
    $stmt->fetch();
    echo "✅ User found!\n";
    echo "ID:       $id\n";
    echo "Username: $uname\n";
    echo "Role:     $role\n";
    echo "Hash:     $hash\n\n";

    $verify = password_verify($password, $hash);
    echo "Password '$password' matches: " . ($verify ? "✅ YES" : "❌ NO") . "\n\n";

    if (!$verify) {
        echo "--- Fixing password now ---\n";
        $newHash = password_hash($password, PASSWORD_DEFAULT);
        $upd = $conn->prepare("UPDATE users SET password=?, role='admin' WHERE id=?");
        $upd->bind_param("si", $newHash, $id);
        if ($upd->execute()) {
            echo "✅ Password updated! Try logging in now.\n";
        } else {
            echo "❌ Update failed: " . $conn->error . "\n";
        }
    }
}

echo "</pre>";
echo "<br><a href='login.php' style='font-family:sans-serif;'>→ Go to Login</a>";
?>
