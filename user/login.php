<?php
session_start();
require_once '../includes/config.php';

$error = "";

// 🛡️ ONLY process if the 'login' button was actually clicked
if (isset($_POST['login'])) {
    
    // Security: Escaping email to prevent SQL Injection
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    // 🔍 Query verified against your current DB structure ('email' and 'password')
    $query = "SELECT * FROM registrations WHERE email = '$email' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if ($user_data = mysqli_fetch_assoc($result)) {
        // 🔐 Verify the input against the secure hash in the database
        if (password_verify($password, $user_data['password'])) {
            $_SESSION['user_id'] = $user_data['id'];
            $_SESSION['user_name'] = $user_data['name']; // Using renamed 'name' column
            $_SESSION['role'] = 'user';
            
            // Success state triggers the multi-stage JS animation below
            $success = true;
        } else {
            $error = "🔴 SECURITY ALERT: Invalid Access Credentials.";
        }
    } else {
        $error = "🔴 SYSTEM ERROR: Identity not found in registry.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Authorize Access | User Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --glass: rgba(255, 255, 255, 0.08);
            --glass-border: rgba(255, 255, 255, 0.15);
            --accent: #60a5fa; 
            --text: #ffffff;
        }

        body {
            margin: 0; height: 100vh; display: flex; flex-direction: column; align-items: center;
            background: radial-gradient(at top right, #1e3a8a, #020617);
            font-family: 'Segoe UI', sans-serif; overflow: hidden;
        }

        .header-title {
            margin-top: 40px; background: var(--glass); backdrop-filter: blur(15px);
            padding: 15px 60px; border-radius: 50px; border: 1px solid var(--glass-border);
            color: var(--text); font-size: 1.2rem; font-weight: 800; letter-spacing: 3px;
            text-transform: uppercase; box-shadow: 0 10px 30px rgba(0,0,0,0.5);
            animation: slideDown 0.8s ease-out;
        }

        @keyframes slideDown { from { transform: translateY(-100px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }

        .login-canvas {
            margin-top: 80px; background: var(--glass); backdrop-filter: blur(30px);
            width: 420px; padding: 50px; border-radius: 35px; border: 1px solid var(--glass-border);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.4); text-align: center;
        }

        .input-group { position: relative; margin-bottom: 25px; }

        .input-box {
            background: rgba(255, 255, 255, 0.05); border: 1px solid var(--glass-border);
            padding: 16px 20px; width: 100%; color: white; border-radius: 15px;
            outline: none; transition: 0.3s; box-sizing: border-box;
        }

        .input-box:focus { border-color: var(--accent); background: rgba(255, 255, 255, 0.1); box-shadow: 0 0 15px rgba(96, 165, 250, 0.4); }

        .toggle-password {
            position: absolute; right: 20px; top: 50%; transform: translateY(-50%);
            color: var(--accent); cursor: pointer; opacity: 0.7; transition: 0.2s;
        }

        .btn-authorize {
            width: 100%; background: linear-gradient(135deg, var(--accent), #3b82f6);
            color: #0f172a; padding: 18px; border-radius: 15px; font-weight: 800;
            cursor: pointer; border: none; text-transform: uppercase; letter-spacing: 1px;
            transition: 0.3s;
        }

        #authOverlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(2, 6, 23, 0.95); backdrop-filter: blur(20px);
            display: none; flex-direction: column; align-items: center; justify-content: center;
            z-index: 1000; color: white;
        }

        .loader {
            width: 70px; height: 70px; border: 4px solid var(--glass);
            border-left-color: var(--accent); border-radius: 50%;
            animation: load 1s linear infinite; margin-bottom: 30px;
        }

        @keyframes load { to { transform: rotate(360deg); } }

        .error-glass { 
            background: rgba(239, 68, 68, 0.15); border: 1px solid #ef4444; 
            padding: 12px; border-radius: 12px; color: #fca5a5; 
            margin-bottom: 20px; font-size: 0.9rem; font-weight: bold;
        }
    </style>
</head>
<body>

    <div class="header-title">Event Management System</div>

    <div id="authOverlay">
        <div class="loader"></div>
        <h3 id="statusMsg" style="color:var(--accent); letter-spacing:2px;">VERIFYING CREDENTIALS...</h3>
    </div>

    <div class="login-canvas">
        <h3 style="color:white; margin-bottom:40px; opacity:0.8; letter-spacing:1px;">USER AUTHORIZATION</h3>
        
        <?php if($error) echo "<div class='error-glass'><i class='fas fa-shield-virus'></i> $error</div>"; ?>

        <form method="POST" id="loginForm">
            <div class="input-group">
                <input type="email" name="email" class="input-box" placeholder="Registry Email" required>
            </div>

            <div class="input-group">
                <input type="password" name="password" id="passInput" class="input-box" placeholder="Security Password" required>
                <i class="fas fa-eye toggle-password" id="eyeToggle"></i>
            </div>

            <button type="submit" name="login" class="btn-authorize">Unlock Portal</button>
            
            <div style="margin-top:25px;">
                <a href="../index.php" style="color:rgba(255,255,255,0.4); text-decoration:none; font-size:0.9rem;">Return to Gateway</a>
            </div>
        </form>
    </div>

    <script>
        // 1. Password Visibility Logic
        const passInput = document.getElementById('passInput');
        const eyeToggle = document.getElementById('eyeToggle');

        eyeToggle.addEventListener('click', () => {
            const isPass = passInput.type === 'password';
            passInput.type = isPass ? 'text' : 'password';
            eyeToggle.classList.toggle('fa-eye-slash');
        });

        // 2. Multi-Stage Authorization Sequence
        <?php if(isset($success)): ?>
            const overlay = document.getElementById('authOverlay');
            const msg = document.getElementById('statusMsg');
            overlay.style.display = 'flex';

            const stages = ["AUTHENTICATING...", "SYNCING USER DATA...", "ACCESS GRANTED"];
            let i = 0;
            const timer = setInterval(() => {
                msg.innerText = stages[i];
                i++;
                if (i >= stages.length) {
                    clearInterval(timer);
                    window.location.href = 'dashboard.php'; 
                }
            }, 600);
        <?php endif; ?>
    </script>
</body>
</html>