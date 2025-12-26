<?php
include 'config.php';

$user_query = "SELECT COUNT(*) as no_kp FROM user";
$user_result = mysqli_query($conn, $user_query);
if (!$user_result) {
    die("User query failed: " . mysqli_error($conn));
}
$total_user = mysqli_fetch_assoc($user_result)['no_kp'];

$visitor_query = "SELECT COUNT(*) as id FROM visitor";
$visitor_result = mysqli_query($conn, $visitor_query);
if (!$visitor_result) {
    die("Visitor query failed: " . mysqli_error($conn));
}
$total_visitor = mysqli_fetch_assoc($visitor_result)['id'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="dashboard.js"></script>
    <title>KoVoS Dashboard</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f8f9fa;
            color: #333;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .header {
            background: white;
            border-bottom: 1px solid #dee2e6;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 70px;
            z-index: 1100;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .hamburger {
            display: flex;
            flex-direction: column;
            gap: 4px;
            cursor: pointer;
            padding: 0.5rem;
            transition: all 0.3s ease;
        }

        .hamburger span {
            width: 22px;
            height: 3px;
            background: #333;
            transition: all 0.3s ease;
            border-radius: 2px;
            transform-origin: center;
        }

        .hamburger.active span:nth-child(1) {
            transform: rotate(45deg) translate(6px, 6px);
        }

        .hamburger.active span:nth-child(2) {
            opacity: 0;
            transform: translateX(-20px);
        }

        .hamburger.active span:nth-child(3) {
            transform: rotate(-45deg) translate(6px, -6px);
        }

        .logo-kovos {
            height: 55px;
            width: auto;
            object-fit: contain;
        }

        .logo-placeholder {
            height: 50px;
            background: #007bff;
            color: white;
            padding: 0 1rem;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 1.1rem;
        }

        .user-dropdown {
            position: relative;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            background: white;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        user-drop .user-dropdown:hover {
            background: #f8f9fa;
        }

        .user-icon {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #333;
            font-size: 0.8rem;
        }

        .dropdown-menu {
            position: absolute;
            top: 100%;
            right: 0;
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            min-width: 120px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s ease;
            z-index: 1200;
        }

        .dropdown-menu.show {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .dropdown-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1rem;
            cursor: pointer;
            transition: background 0.2s ease;
            border: none;
            background: none;
            width: 100%;
            text-align: left;
            font-size: 0.9rem;
        }

        .dropdown-item:hover {
            background: #f8f9fa;
        }

        .dropdown .sidebar-item {
            padding-left: 50px;
        }

        /* FIXED: Main container layout */
        .main-container {
            display: flex;
            margin-top: 70px;
            min-height: calc(100vh - 70px);
            transition: all 0.3s ease;
        }

        .sidebar {
            width: 280px;
            background: linear-gradient(135deg, rgb(83, 93, 103), rgb(68, 72, 76));
            color: white;
            position: fixed;
            top: 70px;
            left: 0;
            bottom: 0;
            height: calc(100vh - 70px);
            overflow-y: auto;
            z-index: 1000;
            transition: transform 0.3s ease;
        }

        .sidebar.hidden {
            transform: translateX(-100%);
        }

        /* FIXED: Content area that adjusts based on sidebar state */
        .content-area {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
            margin-left: 280px;
            /* Default sidebar width */
            transition: margin-left 0.3s ease;
            width: calc(100% - 280px);
        }

        .content-area.expanded {
            margin-left: 0;
            width: 100%;
        }

        .content-section {
            display: none;
        }

        .content-section.active {
            display: block;
        }

        .dashboard-header {
            margin-bottom: 30px;
        }

        .dashboard-title {
            font-size: 2rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 5px;
        }

        .dashboard-subtitle {
            color: #666;
            font-size: 0.9rem;
        }

        .user-info {
            text-align: right;
            margin-bottom: 1rem;
        }

        .user-greeting {
            font-size: 0.9rem;
            color: #6c757d;
        }

        .user-details {
            font-size: 0.85rem;
            color: #495057;
            margin-top: 0.2rem;
        }

        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }

        .sidebar-overlay.active {
            display: block;
        }

        .footer {
            background: white;
            border-top: 1px solid #dee2e6;
            padding: 1rem 2rem;
            text-align: center;
            color: #6c757d;
            font-size: 0.85rem;
        }

        /* Mobile responsiveness */
        @media (max-width: 768px) {
            .main-container {
                margin-left: 0;
            }

            .content-area {
                margin-left: 0;
                width: 100%;
            }

            .sidebar {
                transform: translateX(-100%);
                width: 250px;
                z-index: 1001;
            }

            .sidebar.open {
                transform: translateX(0);
            }

            .stats-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .scanner-section {
                grid-template-columns: 1fr;
                gap: 15px;
            }

            .controls {
                flex-direction: column;
                align-items: center;
            }

            .btn {
                width: 100%;
                max-width: 200px;
            }

            .header {
                padding: 1rem;
            }

            .content-area {
                padding: 1rem;
            }

            .logo-kovos {
                height: 40px;
            }

            .logo-placeholder {
                height: 40px;
                font-size: 1rem;
            }

            .camera-container,
            .results-panel {
                min-height: 250px;
                max-height: 300px;
            }
        }

        @media (max-width: 480px) {
            .header {
                padding: 0.5rem 1rem;
            }

            .header-left {
                gap: 0.5rem;
            }

            .logo-kovos {
                height: 35px;
            }

            .logo-placeholder {
                height: 35px;
                font-size: 0.9rem;
                padding: 0 0.5rem;
            }

            .user-dropdown {
                padding: 0.3rem 0.5rem;
                font-size: 0.9rem;
            }

            .scanner-container {
                padding: 15px;
            }

            .dashboard-title {
                font-size: 1.5rem;
            }
        }

        /* Stats Grid Styles */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
            margin: 2rem 0;
            padding: 0 1rem;
        }

        .stat-card {
            background: linear-gradient(135deg, rgb(83, 93, 103), rgb(68, 72, 76));
            border-radius: 20px;
            padding: 2rem;
            color: white;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
            position: relative;
            overflow: hidden;
            min-height: 160px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100px;
            height: 100px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            transition: all 0.6s ease;
        }

        .stat-card:hover::before {
            transform: scale(8);
        }

        .stat-card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        }

        .stat-title {
            font-size: 1rem;
            font-weight: 500;
            opacity: 0.9;
            margin-bottom: 1rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            line-height: 1.4;
            position: relative;
            z-index: 2;
        }

        .stat-value {
            font-size: 3rem;
            font-weight: 700;
            line-height: 1;
            position: relative;
            z-index: 2;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        /* Icon styling (optional - you can add icons to your stat cards) */
        .stat-card .stat-icon {
            position: absolute;
            top: 1.5rem;
            right: 1.5rem;
            font-size: 2rem;
            opacity: 0.3;
            transition: all 0.3s ease;
        }

        .stat-card:hover .stat-icon {
            opacity: 0.6;
            transform: rotate(10deg) scale(1.1);
        }

        /* Alternative card designs */
        .stat-card.style-minimal {
            background: white;
            color: #333;
            border: 1px solid #e9ecef;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        .stat-card.style-minimal .stat-value {
            color: #007bff;
            text-shadow: none;
        }

        .stat-card.style-glass {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #333;
        }

        /* Pulse animation for values */
        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }

            100% {
                transform: scale(1);
            }
        }

        .stat-value.animate {
            animation: pulse 2s infinite;
        }

        @keyframes loading {
            0% {
                background-position: 200% 0;
            }

            100% {
                background-position: -200% 0;
            }
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
                gap: 1.5rem;
                margin: 1.5rem 0;
                padding: 0 0.5rem;
            }

            .stat-card {
                padding: 1.5rem;
                min-height: 120px;
            }

            .stat-value {
                font-size: 2.5rem;
            }

            .stat-title {
                font-size: 0.9rem;
            }
        }

        @media (max-width: 480px) {
            .stat-card {
                padding: 1.25rem;
                min-height: 100px;
            }

            .stat-value {
                font-size: 2rem;
            }

            .stat-title {
                font-size: 0.85rem;
                margin-bottom: 0.75rem;
            }
        }
    </style>
</head>

<body>
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

    <header class="header">
        <div class="header-left">
            <div class="hamburger" id="hamburger" onclick="toggleSidebar()">
                <span></span>
                <span></span>
                <span></span>
            </div>
            <img src="image/Logo_KoVS.png" alt="Logo KoVoS" class="logo-kovos"
                onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
            <div class="logo-placeholder" style="display: none;">KoVoS</div>
        </div>

        <div class="user-dropdown" id="userDropdown" onclick="toggleUserMenu()">
            <div class="user-icon"><i class="fas fa-user"></i></div>
            <span>Admin</span>

            <div class="dropdown-menu" id="userMenu">
                <div class="dropdown-item logout" onclick="handleLogout(event)">
                    <i class="fas fa-sign-out-alt dropdown-icon"></i>
                    <span>Logout</span>
                </div>
            </div>
        </div>

    </header>

    <div class="main-container">
        <?php include 'sidebar.php'; ?>

        <!-- Main Content Area -->
        <main class="content-area">
            <!-- Dashboard Section -->
            <div id="dashboard-section" class="content-section active">
                <div class="user-greeting" id="userGreeting">Today : Loading...</div>
                <div class="user-details">BHA900 KOLEJ VOKASIONAL SEPANG</div>

                <div class="dashboard-header">
                    <h1 class="dashboard-title">Dashboard</h1>
                    <p class="dashboard-subtitle" id="dashboardSubtitle">Date : Loading...</p>
                </div>

                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-title">Number of Registered Vehicles</div>
                        <div class="stat-value"><?php echo $total_user; ?></div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-title">Number of Visitors</div>
                        <div class="stat-value"><?php echo $total_visitor; ?></div>
                    </div>
                </div>

                <div class="content-grid">

                    <div class="scanner-container">

                        <?php include 'scan.php'; ?>
                    </div>
                    <br>
                    <footer class="footer">
                        <span id="footerYear">Copyright ©2025 KoVoS [406400-X]. All rights reserved.</span>
                    </footer>
                </div>
            </div>

            <!-- Pendaftar Section -->
            <div id="pendaftar-section" class="content-section">
            </div>

            <!-- Ibu Bapa Section -->
            <div id="ibu-bapa-section" class="content-section">
                <div class="stats-grid">
                    <div class="card">
                        <?php include 'parent_table.php'; ?>
                    </div>
                </div>
            </div>

            <!-- Pelajar Section -->
            <div id="pelajar-section" class="content-section">
                <div class="stats-grid">
                    <div class="card">
                        <?php include 'student_table.php'; ?>
                    </div>
                </div>
            </div>

            <!-- Staf Kakitangan Section -->
            <div id="staf-kakitangan-section" class="content-section">
                <div class="stats-grid">
                    <div class="card">
                        <?php include 'staff_table.php'; ?>
                    </div>
                </div>
            </div>

            <!-- Pelawat Section -->
            <div id="pelawat-section" class="content-section">
                <div class="stats-grid">
                    <div class="card">
                        <?php include 'visitor_table.php'; ?>
                    </div>
                </div>
            </div>

        </main>
    </div>
</body>

</html>