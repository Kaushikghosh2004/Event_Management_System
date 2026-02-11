<?php
session_start();
// Security: Access restricted to logged-in Users
require_once '../includes/config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Hub | Event Management System</title>
    <link rel="stylesheet" href="../assets/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --glass: rgba(255, 255, 255, 0.08);
            --glass-border: rgba(255, 255, 255, 0.15);
            --accent: #60a5fa; /* User Domain Blue */
            --text: #ffffff;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            background: radial-gradient(circle at top right, #1e3a8a, #020617);
            font-family: 'Segoe UI', sans-serif;
            overflow: hidden;
        }

        /* 1. Upper Floating Title */
        .header-title {
            margin: 40px 0;
            background: var(--glass);
            backdrop-filter: blur(15px);
            padding: 15px 60px;
            border-radius: 50px;
            border: 1px solid var(--glass-border);
            color: var(--text);
            font-size: 1.2rem;
            font-weight: 800;
            letter-spacing: 3px;
            text-transform: uppercase;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        }

        /* 🔮 The Main Glass Canvas */
        .portal-canvas {
            background: var(--glass);
            backdrop-filter: blur(30px);
            -webkit-backdrop-filter: blur(30px);
            width: 1050px;
            height: 600px;
            border-radius: 35px;
            border: 1px solid var(--glass-border);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.4);
            display: flex;
            position: relative;
            overflow: hidden;
        }

        /* 2. Interactive Service Sidebar */
        .service-drawer {
            width: 260px;
            background: rgba(255, 255, 255, 0.03);
            border-right: 1px solid var(--glass-border);
            padding: 40px 30px;
        }

        .service-drawer h4 {
            color: var(--accent);
            text-transform: uppercase;
            letter-spacing: 2px;
            font-size: 0.8rem;
            margin-bottom: 30px;
            border-bottom: 1px solid var(--glass-border);
            padding-bottom: 10px;
        }

        .service-list { list-style: none; padding: 0; }
        .service-item {
            display: flex;
            align-items: center;
            gap: 15px;
            color: var(--text);
            margin-bottom: 20px;
            opacity: 0.7;
            transition: 0.3s;
            cursor: pointer;
        }
        .service-item:hover { opacity: 1; transform: translateX(10px); color: var(--accent); }
        .service-item i { font-size: 1rem; width: 20px; text-align: center; }

        /* 3. Main Dashboard Grid */
        .dashboard-content { flex: 1; padding: 50px; text-align: center; }
        
        .welcome-banner {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 60px;
            text-shadow: 0 0 20px rgba(96, 165, 250, 0.5);
        }

        .menu-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 25px;
        }

        /* Interactive Action Cards */
        .btn-action-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--glass-border);
            padding: 35px;
            border-radius: 20px;
            text-decoration: none;
            color: var(--text);
            transition: 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 15px;
        }

        .btn-action-card i { font-size: 2.5rem; color: var(--accent); }
        .btn-action-card span { font-weight: 700; font-size: 1.1rem; text-transform: uppercase; }

        .btn-action-card:hover {
            background: var(--text);
            color: #1e3a8a;
            transform: translateY(-10px) scale(1.05);
            box-shadow: 0 15px 30px rgba(0,0,0,0.3);
        }
        .btn-action-card:hover i { color: #1e3a8a; }

        /* Logout decision node */
        .logout-footer {
            position: absolute;
            bottom: 30px;
            right: 40px;
        }
    </style>
</head>
<body>

    <div class="header-title">Event Management System</div>

    <div class="portal-canvas">
        
        <aside class="service-drawer">
            <h4>Service Registry</h4>
            <div class="service-list">
                <div class="service-item"><i class="fas fa-utensils"></i> <span>Catering</span></div>
                <div class="service-item"><i class="fas fa-seedling"></i> <span>Florist</span></div>
                <div class="service-item"><i class="fas fa-holly-berry"></i> <span>Decoration</span></div>
                <div class="service-item"><i class="fas fa-lightbulb"></i> <span>Lighting</span></div>
                <div class="service-item" style="margin-top:40px; opacity:0.4;"><i class="fas fa-plus"></i> <span>More Coming</span></div>
            </div>
        </aside>

        <main class="dashboard-content">
            <div class="welcome-banner">Welcome, User</div>

            <div class="menu-grid">
                <a href="vendors_list.php" class="btn-action-card">
                    <i class="fas fa-store"></i>
                    <span>Browse Vendors</span>
                </a>

                <a href="cart.php" class="btn-action-card">
                    <i class="fas fa-shopping-basket"></i>
                    <span>Service Cart</span>
                </a>

                <a href="guest_list.php" class="btn-action-card">
                    <i class="fas fa-users-viewfinder"></i>
                    <span>My Guest List</span>
                </a>

                <a href="order_status.php" class="btn-action-card">
                    <i class="fas fa-satellite-dish"></i>
                    <span>Order Status</span>
                </a>
            </div>

            <div class="logout-footer">
                <a href="../logout.php" class="btn-glass-nav" style="color: #f87171; text-decoration: none; border: 1px solid var(--glass-border); padding: 10px 30px; border-radius: 12px; font-weight: bold;">
                    <i class="fas fa-power-off"></i> Secure Logout
                </a>
            </div>
        </main>
    </div>

    <script src="../assets/main.js"></script>
</body>
</html>