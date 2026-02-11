<?php
session_start();
require_once '../includes/config.php';

$error = "";
if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    // Professional Auth Logic for Vendors
    $query = "SELECT * FROM vendors WHERE email = '$email' AND password = '$password' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {
        $vendor_data = mysqli_fetch_assoc($result);
        $_SESSION['vendor_id'] = $vendor_data['id'];
        $_SESSION['user_name'] = $vendor_data['username'];
        $_SESSION['role'] = 'vendor';
        
        // Success trigger for JavaScript Authorization Sequence
        $success = true;
    } else {
        $error = "Access Denied: Invalid Vendor Credentials";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Vendor Access | Event Management System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --glass: rgba(255, 255, 255, 0.08);
            --glass-border: rgba(255, 255, 255, 0.15);
            --emerald: #10b981;
            --text: #ffffff;
        }

        body {
            margin: 0;
            height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            background: radial-gradient(at 0% 100%, #064e3b 0, transparent 50%),
                        radial-gradient(at 100% 0%, #065f46 0, transparent 50%),
                        #020617;
            font-family: 'Segoe UI', sans-serif;
            overflow: hidden;
        }

        /* Upper Floating Title */
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

        .login-canvas {
            background: var(--glass);
            backdrop-filter: blur(30px);
            -webkit-backdrop-filter: blur(30px);
            width: 420px;
            padding: 50px;
            border-radius: 35px;
            border: 1px solid var(--glass-border);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.4);
            text-align: center;
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

        .input-box:focus { border-color: var(--emerald); box-shadow: 0 0 15px rgba(16, 185, 129, 0.4); }

        .btn-authorize {
            width: 100%;
            background: linear-gradient(135deg, var(--emerald), #059669);
            color: #064e3b;
            padding: 18px;
            border-radius: 15px;
            font-weight: 800;
            cursor: pointer;
            border: none;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: 0.3s;
            box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3);
        }

        .btn-authorize:hover { transform: translateY(-3px); box-shadow: 0 12px 25px rgba(16, 185, 129, 0.5); }

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

        .loader { width: 70px; height: 70px; border: 4px solid var(--glass); border-left-color: var(--emerald); border-radius: 50%; animation: spin 1s linear infinite; margin-bottom: 20px; }
        @keyframes spin { to { transform: rotate(360deg); } }

        .error-glass { background: rgba(239, 68, 68, 0.15); border: 1px solid #ef4444; padding: 12px; border-radius: 12px; color: #fca5a5; margin-bottom: 20px; font-weight: bold; }
    </style>
</head>
<body>

    <div class="header-title">Event Management System</div>

    <div id="authOverlay">
        <div class="loader"></div>
        <h3 id="statusMsg" style="color:var(--emerald);">VALIDATING VENDOR CREDENTIALS...</h3>
    </div>

    <div class="login-canvas">
        <h3 style="color:white; margin-bottom:40px; opacity:0.8;">VENDOR AUTHORIZATION</h3>
        
        <?php if($error) echo "<div class='error-glass'>$error</div>"; ?>

        <form method="POST">
            <div class="input-group">
                <input type="email" name="email" class="input-box" placeholder="Vendor Email" required>
            </div>

            <div class="input-group">
                <input type="password" name="password" id="passInput" class="input-box" placeholder="Secure Password" required>
            </div>

            <button type="submit" name="login" class="btn-authorize">Enter Command Center</button>
            
            <p style="margin-top:25px; color:rgba(255,255,255,0.4); font-size:0.9rem;">
                Not registered? <a href="signup.php" style="color:var(--emerald); text-decoration:none;">Apply for Partnership</a>
            </p>
        </form>
    </div>

    <script>
        <?php if(isset($success)): ?>
            const overlay = document.getElementById('authOverlay');
            const msg = document.getElementById('statusMsg');
            overlay.style.display = 'flex';

            const stages = ["SYNCING VENDOR DATA...", "ESTABLISHING SECURE NODE...", "ACCESS GRANTED"];
            let i = 0;
            const timer = setInterval(() => {
                msg.innerText = stages[i];
                i++;
                if (i >= stages.length) {
                    clearInterval(timer);
                    window.location.href = 'vendor_dashboard.php';
                }
            }, 600);
        <?php endif; ?>
    </script>
</body>
</html>