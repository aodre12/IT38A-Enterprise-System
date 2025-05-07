<?php
// Start session at the top of the page
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Assuming you have a form input for username and password
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Your database connection (adjust according to your DB credentials)
    $pdo = new PDO('mysql:host=localhost;dbname=your_database', 'your_username', 'your_password');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Prepare and execute the query to check credentials
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username AND password = :password");
    $stmt->execute(['username' => $username, 'password' => md5($password)]); // Assuming passwords are hashed
    $user = $stmt->fetch();

    if ($user) {
        // If user is found, set session and redirect to dashboard
        $_SESSION['user_id'] = $user['id']; // Store user id in session
        header("Location: dashboard.php");
        exit();
    } else {
        // Show an error message if login fails
        $error_message = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <?php if (isset($error_message)): ?>
        <p style="color: red;"><?php echo $error_message; ?></p>
    <?php endif; ?>

    <form method="POST" action="login.php">
        <label for="username">Username:</label>
        <input type="text" name="username" id="username" required><br><br>
        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required><br><br>
        <button type="submit">Login</button>
    </form>
</body>
</html>
