<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="background">
    <div class="tile">
        <h2>Login</h2>
        <form method="POST" action="login_process.php">
            <input type="text" name="username" placeholder="Username" class="input-field" required><br>
            <input type="password" name="password" placeholder="Password" class="input-field" required><br>
            <button type="submit" class="button">Login</button>
        </form>
    </div>
</body>
</html>
