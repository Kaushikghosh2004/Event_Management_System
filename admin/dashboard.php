<?php
session_start();
// Security Check: Ensure Admin role
require_once '../includes/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Command Center | VIDYAVERSE</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --glass: rgba(255, 255, 255, 0.1);
            --glass-border: rgba(255, 255, 255, 0.2);
            --accent: #3b82f6;
            --text: #ffffff;
        }

        body {
            margin: 0;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', sans-serif;
            /* Dynamic Mesh Gradient Background */
            background: radial-gradient(at top left, #1e3a8a, transparent),
                        radial-gradient(at bottom right, #1e40af, transparent),
                        radial-gradient(at top right, #3b82f6, transparent),
                        #0f172a;
            overflow: hidden;
        }

        /* The Main Glass Canvas */
        .admin-canvas {
            background: var(--glass);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            width: 900px;
            height: 550px;
            border-radius: 24px;
            border: 1px solid var(--glass-border);
            padding: 40px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
        }

        /* Top Nav Glass Buttons */
        .nav-buttons { display: flex; justify-content: space-between; }
        
        .btn-glass {
            background: var(--glass);
            border: 1px solid var(--glass-border);
            padding: 12px 30px;
            border-radius: 12px;
            text-decoration: none;
            color: var(--text);
            font-weight: 600;
            backdrop-filter: blur(10px);
            transition: 0.3s all ease;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn-glass:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }

        /* Glowing Welcome Banner */
        .welcome-banner {
            text-align: center;
            padding: 20px;
            margin-bottom: 20px;
        }

        .welcome-banner h1 {
            color: var(--text);
            font-size: 2.5rem;
            margin: 0;
            letter-spacing: 2px;
            text-shadow: 0 0 20px rgba(59, 130, 246, 0.5);
        }

        .welcome-banner p {
            color: rgba(255, 255, 255, 0.7);
            font-size: 1rem;
            margin-top: 10px;
        }

        /* Interactive Maintenance Cards */
        .maintenance-grid {
            display: flex;
            justify-content: center;
            gap: 40px;
            margin-top: 20px;
        }

        .maintenance-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--glass-border);
            width: 250px;
            padding: 30px;
            border-radius: 20px;
            text-align: center;
            color: var(--text);
            text-decoration: none;
            transition: 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .maintenance-card i {
            font-size: 3rem;
            margin-bottom: 20px;
            color: var(--accent);
            filter: drop-shadow(0 0 10px var(--accent));
        }

        .maintenance-card h3 { margin: 10px 0; font-size: 1.2rem; }

        .maintenance-card:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: scale(1.08);
            border-color: var(--accent);
            box-shadow: 0 0 30px rgba(59, 130, 246, 0.3);
        }

        /* System Pulse Indicator */
        .status-dot {
            position: absolute;
            bottom: 20px;
            right: 40px;
            display: flex;
            align-items: center;
            gap: 10px;
            color: rgba(255, 255, 255, 0.5);
            font-size: 0.8rem;
        }

        .pulse {
            width: 10px;
            height: 10px;
            background: #10b981;
            border-radius: 50%;
            box-shadow: 0 0 10px #10b981;
            animation: pulse-animation 2s infinite;
        }

        @keyframes pulse-animation {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(16, 185, 129, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
        }
    </style>
</head>
<body>

<div class="admin-canvas">
    <div class="nav-buttons">
        <a href="../index.php" class="btn-glass"><i class="fas fa-home"></i> Home</a>
        <a href="logout.php" class="btn-glass" style="border-color: #ef4444; color: #f87171;"><i class="fas fa-power-off"></i> LogOut</a>
    </div>

    <div class="welcome-banner">
        <h1>Welcome Admin</h1>
        <p>System Maintenance & Governance Portal</p>
    </div>

    <div class="maintenance-grid">
        <a href="maintain_users.php" class="maintenance-card">
            <i class="fas fa-users-cog"></i>
            <h3>Maintain User</h3>
            <p style="font-size: 0.8rem; opacity: 0.6;">Manage memberships and profiles</p>
        </a>

        <a href="maintain_vendors.php" class="maintenance-card">
            <i class="fas fa-store-alt"></i>
            <h3>Maintain Vendor</h3>
            <p style="font-size: 0.8rem; opacity: 0.6;">Governance & Vendor Tiers</p>
        </a>
    </div>

    <div class="status-dot">
        <div class="pulse"></div>
        Core Systems Active
    </div>
</div>

</body>
</html>