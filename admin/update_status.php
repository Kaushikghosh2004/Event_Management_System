<?php
session_start();
// Security: Point to established includes directory
require_once '../includes/config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$user_data = null;

// Fetch current user details for the interactive panel
if ($id > 0) {
    $res = mysqli_query($conn, "SELECT * FROM registrations WHERE id = $id");
    $user_data = mysqli_fetch_assoc($res);
}

// Logic: Process the update via POST for better security and interactivity
if (isset($_POST['commit_status'])) {
    $new_status = mysqli_real_escape_string($conn, $_POST['status']);
    $sql = "UPDATE registrations SET status = '$new_status' WHERE id = $id";
    
    if (mysqli_query($conn, $sql)) {
        // We will handle the redirect via JavaScript to show the loading animation
        $success = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Authorize Transition | Event Management System</title>
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
            flex-direction: column;
            align-items: center;
            background: radial-gradient(circle at bottom left, #1e3a8a, #020617);
            font-family: 'Segoe UI', sans-serif;
            overflow: hidden;
        }

        /* Upper Part: Floating Glass Header */
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

        /* 🔮 Main Glass Canvas */
        .maintain-canvas {
            background: var(--glass);
            backdrop-filter: blur(30px);
            -webkit-backdrop-filter: blur(30px);
            width: 650px;
            padding: 50px;
            border-radius: 35px;
            border: 1px solid var(--glass-border);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.4);
            text-align: center;
            animation: slideIn 0.8s ease-out;
        }

        @keyframes slideIn { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }

        .user-tag { color: var(--accent); font-weight: bold; font-size: 1.1rem; margin-bottom: 30px; }

        /* Status Selection Grid */
        .status-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin: 40px 0; }
        
        .status-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--glass-border);
            padding: 25px;
            border-radius: 20px;
            cursor: pointer;
            transition: 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
        }

        input[type="radio"] { display: none; }
        input[type="radio"]:checked + .status-card {
            background: var(--accent);
            color: #0f172a;
            transform: scale(1.05);
            box-shadow: 0 10px 30px rgba(96, 165, 250, 0.4);
        }

        .status-card i { font-size: 2rem; margin-bottom: 15px; }
        .status-card span { display: block; font-weight: bold; font-size: 1rem; }

        /* powerful Authorize Button */
        .btn-authorize {
            width: 100%;
            background: var(--text);
            color: #0f172a;
            padding: 18px;
            border-radius: 15px;
            font-weight: 800;
            cursor: pointer;
            border: none;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: 0.3s;
        }

        .btn-authorize:hover { background: var(--accent); transform: translateY(-3px); }

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

        .spinner { width: 60px; height: 60px; border: 4px solid var(--glass); border-left-color: var(--accent); border-radius: 50%; animation: spin 1s linear infinite; margin-bottom: 20px; }
        @keyframes spin { to { transform: rotate(360deg); } }
    </style>
</head>
<body>

    <div class="header-title">Event Management System</div>

    <div id="authOverlay">
        <div class="spinner"></div>
        <h3 id="statusMsg">UPDATING PERMISSIONS...</h3>
    </div>

    <div class="maintain-canvas">
        <h2 style="color:white; opacity:0.8; margin-bottom:10px;">Status Governance</h2>
        <div class="user-tag">Target: <?php echo $user_data['full_name'] ?? 'Undefined User'; ?></div>

        <form method="POST" id="statusForm">
            <div class="status-grid">
                <label>
                    <input type="radio" name="status" value="Approved" checked>
                    <div class="status-card">
                        <i class="fas fa-check-circle"></i>
                        <span>Approve</span>
                    </div>
                </label>

                <label>
                    <input type="radio" name="status" value="Suspended">
                    <div class="status-card">
                        <i class="fas fa-user-slash"></i>
                        <span>Suspend</span>
                    </div>
                </label>
            </div>

            <button type="submit" name="commit_status" class="btn-authorize">Authorize Transition</button>
            <br><br>
            <a href="dashboard.php" style="color:rgba(255,255,255,0.5); text-decoration:none;">Cancel Decision</a>
        </form>
    </div>

    <script>
        <?php if(isset($success)): ?>
            const overlay = document.getElementById('authOverlay');
            const msg = document.getElementById('statusMsg');
            overlay.style.display = 'flex';

            const stages = ["REWRITING REGISTRY...", "SYNCHRONIZING DATABASE...", "UPDATE SUCCESSFUL"];
            let i = 0;
            const timer = setInterval(() => {
                msg.innerText = stages[i];
                i++;
                if (i >= stages.length) {
                    clearInterval(timer);
                    window.location.href = 'dashboard.php?msg=Status Updated';
                }
            }, 600);
        <?php endif; ?>
    </script>
</body>
</html>