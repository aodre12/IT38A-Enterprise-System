<?php
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background-color: #FEF6E4;
            color: #222222;
        }

        .navbar {
            background-color: #001F54;
            padding: 20px;
            color: white;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
        }

        .content {
            padding: 40px;
            text-align: center;
        }

        .card {
            background-color: #ffffff;
            padding: 30px;
            margin: 0 auto;
            width: 400px;
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .logout-button {
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #0056b3;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
        }

        .logout-button:hover {
            background-color: #003d80;
        }
    </style>
</head>
<body>

    <div class="navbar">
        Welcome, <?= htmlspecialchars($username) ?>
    </div>

    <div class="content">
        <div class="card">
            <h2>Dashboard</h2>
            <p>This is your personalized dashboard.</p>
            <form method="POST" action="logout.php">
                <button type="submit" class="logout-button">Logout</button>
            </form>
        </div>
    </div>

</body>
</html>
