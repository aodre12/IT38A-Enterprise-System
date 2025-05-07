<?php
// Start a session or check if the user is logged in
session_start();

// Check if the user is logged in (you can modify this as per your authentication logic)
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login if not authenticated
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>Dashboard</title>
    <style>
        /* Reset and base */
        * {
            box-sizing: border-box;
        }
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f7fa;
            color: #333;
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        /* Sidebar container */
        .sidebar {
            background: #2c3e50;
            color: #ecf0f1;
            width: 270px;
            min-width: 60px; /* Collapsed width */
            transition: width 0.3s ease;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        /* Collapsed state */
        .sidebar.collapsed {
            width: 60px;
        }

        /* Sidebar header with toggle button */
        .sidebar-header {
            padding: 1.3rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #1a252f;
            user-select: none;
        }

        .sidebar-header h2 {
            font-size: 1.25rem;
            font-weight: 600;
            margin: 0;
            white-space: nowrap;
            opacity: 1;
            transition: opacity 0.3s ease;
        }
        .sidebar.collapsed .sidebar-header h2 {
            opacity: 0;
        }

        /* Toggle button */
        .toggle-btn {
            width: 32px;
            height: 32px;
            background: none;
            border: none;
            cursor: pointer;
            outline-offset: 4px;
            color: #ecf0f1;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.3s ease;
        }
        .sidebar.collapsed .toggle-btn {
            transform: rotate(180deg);
        }
        .toggle-btn svg {
            width: 20px;
            height: 20px;
        }

        /* Menu list */
        .menu {
            list-style: none;
            padding: 0;
            margin: 0;
            flex-grow: 1;
            overflow-y: auto;
        }

        .menu-item {
            display: flex;
            align-items: center;
            padding: 1rem 1.6rem;
            font-size: 1rem;
            cursor: pointer;
            white-space: nowrap;
            transition: background-color 0.2s ease;
        }
        .menu-item:hover,
        .menu-item:focus {
            background: #34495e;
        }
        .menu-item:focus {
            outline: 2px solid #2980b9;
            outline-offset: -2px;
        }

        /* Icons placeholders - using inline SVG for each item */
        .menu-item svg {
            flex-shrink: 0;
            width: 20px;
            height: 20px;
            fill: #bdc3c7;
            margin-right: 15px;
            transition: fill 0.3s ease;
        }
        .sidebar.collapsed .menu-item svg {
            margin: 0 auto;
        }

        /* Hide text when collapsed */
        .sidebar.collapsed .menu-item span.text {
            display: none;
        }

        /* Scrollbar styling for menu */
        .menu::-webkit-scrollbar {
            width: 6px;
        }
        .menu::-webkit-scrollbar-track {
            background: transparent;
        }
        .menu::-webkit-scrollbar-thumb {
            background-color: rgba(255,255,255,0.15);
            border-radius: 3px;
        }

        /* Footer with logout */
        .sidebar-footer {
            padding: 1rem 1.6rem;
            border-top: 1px solid rgba(255,255,255,0.1);
        }

        .sidebar-footer .menu-item {
            padding: 1rem 0;
            margin: 0;
            border: none;
            cursor: pointer;
            color: #e74c3c;
        }
        .sidebar-footer .menu-item:hover,
        .sidebar-footer .menu-item:focus {
            background: rgba(231, 76, 60, 0.15);
            color: #e74c3c;
        }

        /* Responsive for small height screen: ensure no vertical scrolling inside the sidebar */
        @media (max-height: 600px) {
            .sidebar {
                height: 100vh;
            }
            body {
                align-items: stretch;
            }
        }
    </style>
</head>
<body>

<!-- Sidebar navigation -->
<nav class="sidebar" aria-label="Profile sidebar">
    <div class="sidebar-header">
        <h2>Profile</h2>
        <button class="toggle-btn" aria-label="Toggle sidebar" aria-expanded="true" id="toggleSidebarBtn" aria-controls="sidebarMenu">
            <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="currentColor">
                <path d="M8.59 16.59L13.17 12 8.59 7.41 10 6l6 6-6 6z"/>
            </svg>
        </button>
    </div>
    <ul class="menu" id="sidebarMenu" role="menu" tabindex="0">
        <li class="menu-item" role="menuitem" tabindex="0" aria-label="View Profile">
            <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                <circle cx="12" cy="8" r="4" />
                <path d="M4 20c0-4 8-4 8-4s8 0 8 4v1H4v-1z"/>
            </svg>
            <span class="text">View Profile</span>
        </li>
        <li class="menu-item" role="menuitem" tabindex="0" aria-label="Account Settings">
            <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                <path d="M12 8a4 4 0 1 0 0 8 4 4 0 0 0 0-8zm0-6a2 2 0 1 1 0 4 2 2 0 0 1 0-4zM4.22 19H19.8l-2.25-6H6.5l-2.28 6z"/>
                <circle cx="12" cy="12" r="10" fill="none"/>
            </svg>
            <span class="text">Account Settings</span>
        </li>
        <li class="menu-item" role="menuitem" tabindex="0" aria-label="My Orders">
            <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                <path d="M3 6h18v2H3V6zm0 6h10v2H3v-2zm0 6h6v2H3v-2z"/>
                <rect x="15" y="6" width="6" height="12" rx="1"/>
            </svg>
            <span class="text">My Orders</span>
        </li>
        <li class="menu-item" role="menuitem" tabindex="0" aria-label="Notifications">
            <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                <path d="M12 22a2 2 0 0 0 2-2H10a2 2 0 0 0 2 2zm6-6V11c0-3.07-1.64-5.64-5-6.32V4a1 1 0 1 0-2 0v.68C7.64 5.36 6 7.92 6 11v5l-1 1v1h14v-1l-1-1z"/>
            </svg>
            <span class="text">Notifications</span>
        </li>
        <li class="menu-item" role="menuitem" tabindex="0" aria-label="Saved Reports">
            <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                <path d="M6 2h9l5 5v15a1 1 0 0 1-1 1H6a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2zM14 3v4h4"/>
                <rect x="8" y="11" width="8" height="2"/>
                <rect x="8" y="15" width="5" height="2"/>
            </svg>
            <span class="text">Saved Reports</span>
        </li>
        <li class="menu-item" role="menuitem" tabindex="0" aria-label="Settings">
            <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                <path d="M19.14 12.94a1.94 1.94 0 0 0 0-1.88l2.03-1.57-2-3.46-2.58.98a6.68 6.68 0 0 0-1.6-.93L14.5 3h-5l-.49 3.08a6.68 6.68 0 0 0-1.6.93l-2.58-.98-2 3.46 2.03 1.57a1.94 1.94 0 0 0 0 1.88L3.44 14.5l2 3.46 2.58-.98c.48.34 1 .6 1.6.93L9.5 21h5l.49-3.08c.6-.33 1.12-.59 1.6-.93l2.58.98 2-3.46-2.03-1.58zM12 15a3 3 0 1 1 0-6 3 3 0 0 1 0 6z"/>
            </svg>
            <span class="text">Settings</span>
        </li>
    </ul>
    <div class="sidebar-footer">
        <button class="menu-item" aria-label="Logout" tabindex="0" role="menuitem">
            <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="currentColor" style="fill:#e74c3c;">
                <path d="M16 13v-2H7V8l-5 4 5 4v-3zM20 3H8a2 2 0 0 0-2 2v5h2V5h12v14H8v-5H6v5a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2z"/>
            </svg>
            <span class="text">Logout</span>
        </button>
    </div>
</nav>

<!-- Dashboard content -->
<div class="dashboard-content" style="flex-grow: 1; padding: 20px;">
    <!-- Your existing dashboard content here -->
    <h1>Welcome to Your Dashboard</h1>
    <p>Dashboard content goes here.</p>
</div>

<script>
  (function () {
    const sidebar = document.querySelector('.sidebar');
    const toggleBtn = document.getElementById('toggleSidebarBtn');
    const menu = document.getElementById('sidebarMenu');

    toggleBtn.addEventListener('click', () => {
      const isCollapsed = sidebar.classList.toggle('collapsed');
      toggleBtn.setAttribute('aria-expanded', !isCollapsed);
    });

    // Keyboard accessibility: space or enter triggers click on menu items
    const menuItems = sidebar.querySelectorAll('.menu-item');
    menuItems.forEach(item => {
      item.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' || e.key === ' ') {
          e.preventDefault();
          item.click();
        }
      });
    });
  })();
</script>
</body>
</html>
