<?php
session_start();
require_once '../includes/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Maintain Vendor | Admin Command</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --glass: rgba(255, 255, 255, 0.1);
            --glass-border: rgba(255, 255, 255, 0.15);
            --accent: #10b981; /* Emerald green for Vendor Governance */
            --text: #ffffff;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: radial-gradient(at 0% 100%, #064e3b 0, transparent 50%),
                        radial-gradient(at 100% 0%, #065f46 0, transparent 50%),
                        #020617;
            font-family: 'Segoe UI', system-ui, sans-serif;
            overflow: hidden;
        }

        .maintain-canvas {
            background: var(--glass);
            backdrop-filter: blur(30px);
            -webkit-backdrop-filter: blur(30px);
            width: 900px;
            padding: 40px;
            border-radius: 30px;
            border: 1px solid var(--glass-border);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6);
            position: relative;
            animation: slideUp 0.6s cubic-bezier(0.23, 1, 0.32, 1);
        }

        @keyframes slideUp { from { opacity: 0; transform: translateY(40px); } to { opacity: 1; transform: translateY(0); } }

        .header-nav { display: flex; justify-content: space-between; margin-bottom: 60px; }
        
        .btn-glass-nav {
            background: var(--glass);
            border: 1px solid var(--glass-border);
            padding: 12px 35px;
            border-radius: 15px;
            text-decoration: none;
            color: var(--text);
            font-weight: 600;
            transition: 0.3s;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn-glass-nav:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: scale(1.05);
            box-shadow: 0 0 20px rgba(16, 185, 129, 0.3);
        }

        .maintenance-section {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            display: grid;
            grid-template-columns: 220px 1fr;
            gap: 20px;
            padding: 25px;
            margin-bottom: 30px;
            transition: 0.3s;
        }

        .maintenance-section:hover {
            background: rgba(255, 255, 255, 0.06);
            border-color: var(--accent);
        }

        .action-label {
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--accent);
            color: #064e3b;
            border-radius: 15px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.9rem;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4);
        }

        .sub-actions { display: flex; gap: 20px; align-items: center; }

        .btn-action-card {
            flex: 1;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--glass-border);
            padding: 20px;
            border-radius: 15px;
            text-decoration: none;
            color: var(--text);
            text-align: center;
            transition: 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .btn-action-card i { font-size: 1.5rem; color: var(--accent); }
        .btn-action-card span { font-weight: 600; font-size: 1.1rem; }

        .btn-action-card:hover {
            background: var(--text);
            color: #064e3b;
            transform: translateY(-8px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.4);
        }

        .btn-action-card:hover i { color: #065f46; }

    </style>
</head>
<body>

<div class="maintain-canvas">
    <div class="header-nav">
        <a href="dashboard.php" class="btn-glass-nav"><i class="fas fa-arrow-left"></i> Dashboard</a>
        <a href="logout.php" class="btn-glass-nav" style="color: #fca5a5;"><i class="fas fa-sign-out-alt"></i> LogOut</a>
    </div>

    <div class="maintenance-section">
        <div class="action-label">Subscriptions</div>
        <div class="sub-actions">
            <a href="add_membership.php?type=vendor" class="btn-action-card">
                <i class="fas fa-id-card"></i>
                <span>Assign Tier</span>
            </a>
            <a href="update_membership.php?type=vendor" class="btn-action-card">
                <i class="fas fa-sync-alt"></i>
                <span>Renew Tier</span>
            </a>
        </div>
    </div>

    <div class="maintenance-section">
        <div class="action-label">Vendor Control</div>
        <div class="sub-actions">
            <a href="add_vendor.php" class="btn-action-card">
                <i class="fas fa-store-plus"></i>
                <span>Register New</span>
            </a>
            <a href="manage_vendors.php" class="btn-action-card">
                <i class="fas fa-tasks"></i>
                <span>Update Profile</span>
            </a>
        </div>
    </div>

    <p style="text-align: center; color: rgba(255,255,255,0.2); font-size: 0.75rem; margin-top: 40px; letter-spacing: 3px;">
        ADMIN COMMAND PANEL | EVENT MANAGEMENT SYSTEM
    </p>
</div>

</body>
</html>