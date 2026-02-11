<?php
session_start();
require_once '../includes/config.php';

$error = "";
if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    // Professional Auth Logic
    if ($username === "Admin" && $password === "Admin") {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['role'] = 'admin';
    } else {
        $error = "Access Denied: Invalid Credentials";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Authorize | Admin Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --glass: rgba(255, 255, 255, 0.08);
            --glass-border: rgba(255, 255, 255, 0.15);
            --accent: #60a5fa;
            --text: #ffffff;
        }

        body {
            margin: 0;
            height: 100vh;
            display: flex;
            flex-direction: column; /* Stack header and form */
            align-items: center;
            background: radial-gradient(circle at top right, #1e3a8a, #020617);
            font-family: 'Segoe UI', sans-serif;
            overflow: hidden;
        }

        /* 1. Upper Part: Floating Glass Header */
        .header-title {
            margin-top: 40px;
            background: var(--glass);
            backdrop-filter: blur(15px);
            padding: 20px 80px;
            border-radius: 50px;
            border: 1px solid var(--glass-border);
            color: var(--text);
            font-size: 1.5rem;
            font-weight: 800;
            letter-spacing: 4px;
            text-transform: uppercase;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
            animation: slideDown 0.8s ease-out;
        }

        @keyframes slideDown { from { transform: translateY(-100px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }

        /* 2. Main Login Canvas */
        .login-canvas {
            margin-top: 80px;
            background: var(--glass);
            backdrop-filter: blur(30px);
            width: 420px;
            padding: 50px;
            border-radius: 35px;
            border: 1px solid var(--glass-border);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.4);
            text-align: center;
            position: relative;
        }

        .input-group { position: relative; margin-bottom: 25px; }

        .input-box {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--glass-border);
            padding: 16px 20px;
            width: 100%;
            color: white;
            font-size: 1rem;
            border-radius: 15px;
            outline: none;
            transition: 0.3s;
            box-sizing: border-box;
        }

        .input-box:focus { border-color: var(--accent); background: rgba(255, 255, 255, 0.1); }

        .toggle-password {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--accent);
            cursor: pointer;
            opacity: 0.7;
        }

        /* Interactive Button */
        .btn-authorize {
            width: 100%;
            background: linear-gradient(135deg, #60a5fa, #3b82f6);
            color: #0f172a;
            padding: 18px;
            border-radius: 15px;
            font-weight: 800;
            cursor: pointer;
            border: none;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: 0.3s;
            box-shadow: 0 8px 20px rgba(59, 130, 246, 0.3);
        }

        .btn-authorize:hover { transform: translateY(-3px); box-shadow: 0 12px 25px rgba(59, 130, 246, 0.5); }

        /* Full Screen Loading Overlay */
        #authOverlay {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(2, 6, 23, 0.95);
            backdrop-filter: blur(20px);
            display: none;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            color: white;
        }

        .loader {
            width: 80px; height: 80px;
            border: 4px solid var(--glass);
            border-left-color: var(--accent);
            border-radius: 50%;
            animation: load 1s linear infinite;
        }

        @keyframes load { to { transform: rotate(360deg); } }

        .status-msg { margin-top: 30px; font-weight: bold; letter-spacing: 1px; color: var(--accent); }
    </style>
</head>
<body>

    <div class="header-title">Event Management System</div>

    <div id="authOverlay">
        <div class="loader"></div>
        <div class="status-msg" id="statusText">INITIALIZING SECURITY...</div>
    </div>

    <div class="login-canvas">
        <h3 style="color:white; margin-bottom:40px; opacity:0.8;">ADMIN AUTHORIZATION</h3>
        
        <?php if($error) echo "<p style='color:#f87171; font-weight:bold;'>$error</p>"; ?>

        <form method="POST" id="loginForm">
            <div class="input-group">
                <input type="text" name="username" class="input-box" placeholder="User ID" required>
            </div>

            <div class="input-group">
                <input type="password" name="password" id="passInput" class="input-box" placeholder="Access Key" required>
                <i class="fas fa-eye toggle-password" id="eyeToggle"></i>
            </div>

            <button type="submit" name="login" class="btn-authorize">Authorize Access</button>
        </form>
    </div>

    <script>
        // 1. Password Visibility Toggle
        const passInput = document.getElementById('passInput');
        const eyeToggle = document.getElementById('eyeToggle');

        eyeToggle.addEventListener('click', () => {
            const isPass = passInput.type === 'password';
            passInput.type = isPass ? 'text' : 'password';
            eyeToggle.classList.toggle('fa-eye-slash');
        });

        // 2. Multi-Stage Loading Interaction
        <?php if(isset($_SESSION['admin_logged_in'])): ?>
            const overlay = document.getElementById('authOverlay');
            const status = document.getElementById('statusText');
            overlay.style.display = 'flex';

            const stages = ["VERIFYING CREDENTIALS...", "ESTABLISHING SECURE TUNNEL...", "ACCESS GRANTED"];
            let i = 0;
            const timer = setInterval(() => {
                status.innerText = stages[i];
                i++;
                if (i >= stages.length) {
                    clearInterval(timer);
                    window.location.href = 'dashboard.php'; // Final Redirect
                }
            }, 600); // 1.8 seconds total authorization
        <?php endif; ?>
    </script>
</body>
</html>