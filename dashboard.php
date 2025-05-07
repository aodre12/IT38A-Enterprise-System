<?php
// Check if the user is logged in
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch user info
$pdo = new PDO('mysql:host=localhost;dbname=your_database', 'your_username', 'your_password');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :user_id");
$stmt->execute(['user_id' => $_SESSION['user_id']]);
$user = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
        /* Your original dashboard styles here */
        /* Sidebar and layout styles as discussed earlier */

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            display: flex;
            height: 100vh;
        }

        .main-container {
            display: flex;
            width: 100%;
        }

        /* Sidebar for Profile */
        .sidebar.profile-sidebar {
            background-color: #2c3e50;
            color: white;
            width: 270px;
            min-width: 60px;
            transition: width 0.3s;
            padding: 1rem;
        }

        .sidebar .menu-item {
            padding: 1rem;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .sidebar .menu-item:hover {
            background-color: #34495e;
        }

        /* Content */
        .dashboard-content {
            flex-grow: 1;
            padding: 2rem;
        }

    </style>
</head>
<body>

    <div class="main-container">

        <!-- Profile Sidebar -->
        <nav class="sidebar profile-sidebar">
            <div class="sidebar-header">
                <h2>Profile</h2>
                <button class="toggle-btn" id="toggleProfileSidebarBtn">
                    <svg viewBox="0 0 24 24" aria-hidden="true" fill="currentColor">
                        <path d="M8.59 16.59L13.17 12 8.59 7.41 10 6l6 6-6 6z"/>
                    </svg>
                </button>
            </div>
            <ul class="menu">
                <li class="menu-item">
                    <a href="view_profile.php">View Profile</a>
                </li>
                <li class="menu-item">
                    <a href="account_settings.php">Account Settings</a>
                </li>
                <li class="menu-item">
                    <a href="logout.php">Logout</a>
                </li>
            </ul>
        </nav>

        <!-- Main Sidebar -->
        <nav class="sidebar main-sidebar">
            <ul class="menu">
                <li class="menu-item">
                    <a href="tasks.php">Tasks</a>
                </li>
                <li class="menu-item">
                    <a href="work_orders.php">Work Orders</a>
                </li>
                <li class="menu-item">
                    <a href="reports.php">Reports</a>
                </li>
                <li class="menu-item">
                    <a href="settings.php">Settings</a>
                </li>
            </ul>
        </nav>

        <!-- Main Content -->
        <div class="dashboard-content">
            <h1>Welcome, <?php echo htmlspecialchars($user['username']); ?>!</h1>
            <p>Here is your dashboard overview.</p>
        </div>

    </div>

    <script>
        document.getElementById('toggleProfileSidebarBtn').addEventListener('click', function() {
            document.querySelector('.profile-sidebar').classList.toggle('collapsed');
        });
    </script>

</body>
</html>
