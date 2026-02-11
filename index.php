<?php
require_once 'includes/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Management System | Home</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --glass: rgba(255, 255, 255, 0.05);
            --glass-border: rgba(255, 255, 255, 0.12);
            --emerald: #10b981;
            --ocean: #3b82f6;
            --text: #ffffff;
        }

        body {
            margin: 0;
            min-height: 100vh;
            background: radial-gradient(at 0% 0%, #0f172a 0, transparent 50%),
                        radial-gradient(at 100% 100%, #064e3b 0, transparent 50%),
                        #020617;
            font-family: 'Segoe UI', sans-serif;
            color: var(--text);
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .hero {
            text-align: center;
            padding: 100px 20px 60px;
        }

        .hero h1 { 
            font-size: 3.5rem; 
            margin: 0; 
            letter-spacing: 2px; 
            background: linear-gradient(to right, #ffffff, var(--ocean));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-weight: 900;
            text-transform: uppercase;
        }

        .portal-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
            max-width: 1200px;
            width: 90%;
            margin-bottom: 60px;
        }

        .portal-node {
            background: var(--glass);
            backdrop-filter: blur(25px);
            padding: 50px 30px;
            border-radius: 40px;
            border: 1px solid var(--glass-border);
            text-align: center;
            transition: 0.4s ease;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .portal-node:hover { transform: translateY(-10px); }
        .vendor-node:hover { border-color: var(--emerald); }
        .user-node:hover { border-color: var(--ocean); }

        .portal-node i { font-size: 3rem; margin-bottom: 20px; display: block; }
        .vendor-node i { color: var(--emerald); }
        .user-node i { color: var(--ocean); }

        .btn-node {
            display: inline-block;
            width: 100%;
            padding: 14px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 800;
            text-transform: uppercase;
            margin-top: 15px;
            transition: 0.3s;
            box-sizing: border-box;
        }

        .btn-vendor { background: var(--emerald); color: #064e3b; }
        .btn-user { background: var(--ocean); color: #0f172a; }
        
        .btn-outline {
            background: transparent;
            border: 1px solid var(--ocean);
            color: var(--ocean);
        }
        .btn-outline:hover { background: var(--ocean); color: #0f172a; }
    </style>
</head>
<body>

    <section class="hero">
        <h1>Event Management System</h1>
        <p style="opacity:0.6; font-size:1.1rem;">Professional Architecture for Planning & Execution</p>
    </section>

    <div class="portal-grid">
        <div class="portal-node user-node">
            <div>
                <i class="fas fa-user-shield"></i>
                <h2>Admin</h2>
                <p style="font-size:0.85rem; opacity:0.6; margin-bottom:25px;">System governance and user oversight.</p>
            </div>
            <div>
                <a href="admin/login.php" class="btn-node btn-user">Login</a>
                <a href="admin/signup.php" class="btn-node btn-outline">New Admin?</a>
            </div>
        </div>

        <div class="portal-node vendor-node">
            <div>
                <i class="fas fa-store"></i>
                <h2>Vendor</h2>
                <p style="font-size:0.85rem; opacity:0.6; margin-bottom:25px;">Inventory control and service requests.</p>
            </div>
            <div>
                <a href="vendor/login.php" class="btn-node btn-vendor">Command Hub</a>
            </div>
        </div>

        <div class="portal-node user-node">
            <div>
                <i class="fas fa-users"></i>
                <h2>User</h2>
                <p style="font-size:0.85rem; opacity:0.6; margin-bottom:25px;">Browse events, cart, and bookings.</p>
            </div>
            <div>
                <a href="user/login.php" class="btn-node btn-user">Login</a>
                <a href="user_signup.php" class="btn-node btn-outline">Create Account</a>
            </div>
        </div>
    </div>

</body>
</html>