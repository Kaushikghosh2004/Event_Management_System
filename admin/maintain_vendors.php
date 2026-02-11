<?php
session_start();
// Security: Session role verification
require_once '../includes/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Vendor Governance | Event Management System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --glass: rgba(255, 255, 255, 0.08);
            --glass-border: rgba(255, 255, 255, 0.15);
            --accent: #10b981; /* Emerald accent for Vendor Domain */
            --text: #ffffff;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            /* Deep Space Background for Glass Contrast */
            background: radial-gradient(at 0% 100%, #064e3b 0, transparent 50%),
                        radial-gradient(at 100% 0%, #065f46 0, transparent 50%),
                        #020617;
            font-family: 'Segoe UI', system-ui, sans-serif;
            overflow-x: hidden;
        }

        /* Upper Floating Header */
        .header-title {
            margin-top: 40px;
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

        /* 🔮 Main Glass Canvas */
        .maintain-vendor-canvas {
            margin-top: 50px;
            background: var(--glass);
            backdrop-filter: blur(30px);
            -webkit-backdrop-filter: blur(30px);
            width: 900px;
            padding: 45px;
            border-radius: 35px;
            border: 1px solid var(--glass-border);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6);
            position: relative;
            animation: slideIn 0.7s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        @keyframes slideIn { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }

        /* Navigation Header */
        .nav-header { display: flex; justify-content: space-between; margin-bottom: 60px; }
        
        .btn-glass-ui {
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

        .btn-glass-ui:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: scale(1.05);
            box-shadow: 0 0 20px rgba(16, 185, 129, 0.3);
        }

        /* Maintenance Rows */
        .menu-row {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            display: grid;
            grid-template-columns: 240px 1fr;
            gap: 20px;
            padding: 25px;
            margin-bottom: 30px;
            transition: 0.3s;
        }

        .menu-row:hover { background: rgba(255, 255, 255, 0.06); border-color: var(--accent); }

        .label-pill {
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--accent);
            color: #064e3b;
            border-radius: 15px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.85rem;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4);
        }

        .action-group { display: flex; gap: 20px; }

        /* Interactive Action Tiles */
        .btn-action-tile {
            flex: 1;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--glass-border);
            padding: 20px;
            border-radius: 18px;
            text-decoration: none;
            color: var(--text);
            text-align: center;
            transition: 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .btn-action-tile i { font-size: 1.6rem; color: var(--accent); }
        .btn-action-tile span { font-weight: 600; font-size: 1.1rem; }

        .btn-action-tile:hover {
            background: var(--text);
            color: #064e3b;
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.4);
        }

        .btn-action-tile:hover i { color: #065f46; }

    </style>
</head>
<body>

    <div class="header-title">Event Management System</div>

    <div class="maintain-vendor-canvas">
        <div class="nav-header">
            <a href="dashboard.php" class="btn-glass-ui"><i class="fas fa-th-large"></i> Home</a>
            <a href="logout.php" class="btn-glass-ui" style="color: #fca5a5;"><i class="fas fa-power-off"></i> LogOut</a>
        </div>

        <div class="menu-row">
            <div class="label-pill">Vendor Membership</div>
            <div class="action-group">
                <a href="add_membership.php?type=vendor" class="btn-action-tile">
                    <i class="fas fa-id-card"></i>
                    <span>Add New</span>
                </a>
                <a href="update_membership.php?type=vendor" class="btn-action-tile">
                    <i class="fas fa-sync-alt"></i>
                    <span>Update Record</span>
                </a>
            </div>
        </div>

        <div class="menu-row">
            <div class="label-pill">Governance</div>
            <div class="action-group">
                <a href="add_vendor.php" class="btn-action-tile">
                    <i class="fas fa-store-plus"></i>
                    <span>Register</span>
                </a>
                <a href="update_vendor.php" class="btn-action-tile">
                    <i class="fas fa-user-edit"></i>
                    <span>Modify Profile</span>
                </a>
            </div>
        </div>

        <p style="text-align: center; color: rgba(255,255,255,0.2); font-size: 0.7rem; margin-top: 40px; letter-spacing: 4px;">
            ADMIN PANEL | SYSTEM SECURE NODE
        </p>
    </div>

</body>
</html>