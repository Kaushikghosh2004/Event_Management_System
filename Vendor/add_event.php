<?php
session_start();
require_once '../includes/config.php';

// Security: Access restricted to authorized Vendors
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'vendor') {
    header("Location: login.php");
    exit;
}

$v_id = $_SESSION['vendor_id'];

// Handle Event Deployment Logic
if (isset($_POST['deploy_event'])) {
    $e_name = mysqli_real_escape_string($conn, $_POST['event_name']);
    $e_date = mysqli_real_escape_string($conn, $_POST['event_date']);
    $e_desc = mysqli_real_escape_string($conn, $_POST['event_description']);

    $sql = "INSERT INTO events (vendor_id, event_name, event_date, description) 
            VALUES ('$v_id', '$e_name', '$e_date', '$e_desc')";
    
    if (mysqli_query($conn, $sql)) {
        $success_trigger = true; // Used to trigger the confetti and toast
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>New Event Protocol | Vendor Command</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --glass: rgba(255, 255, 255, 0.05);
            --glass-border: rgba(255, 255, 255, 0.12);
            --emerald: #10b981;
            --emerald-glow: rgba(16, 185, 129, 0.4);
            --text: #ffffff;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            /* Animated Mesh Gradient for deep immersion */
            background: linear-gradient(135deg, #020617 0%, #064e3b 50%, #020617 100%);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
            font-family: 'Segoe UI', system-ui, sans-serif;
            color: var(--text);
            padding: 40px;
            overflow-x: hidden;
        }

        @keyframes gradientBG { 0% { background-position: 0% 50%; } 50% { background-position: 100% 50%; } 100% { background-position: 0% 50%; } }

        /* Floating System Title */
        .header-title {
            background: var(--glass);
            backdrop-filter: blur(20px);
            padding: 15px 50px;
            border-radius: 50px;
            border: 1px solid var(--glass-border);
            font-size: 1.1rem;
            font-weight: 800;
            letter-spacing: 4px;
            text-transform: uppercase;
            box-shadow: 0 10px 40px rgba(0,0,0,0.5);
            margin-bottom: 50px;
            animation: slideDown 1s ease-out;
        }

        @keyframes slideDown { from { transform: translateY(-50px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }

        /* 🔮 The Hyper-Glass Canvas */
        .glass-canvas {
            background: var(--glass);
            backdrop-filter: blur(40px);
            -webkit-backdrop-filter: blur(40px);
            width: 550px;
            padding: 50px;
            border-radius: 40px;
            border: 1px solid var(--glass-border);
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.6), inset 0 0 20px rgba(255,255,255,0.05);
            text-align: center;
            position: relative;
            animation: scaleIn 0.8s cubic-bezier(0.2, 0.8, 0.2, 1);
        }

        @keyframes scaleIn { from { transform: scale(0.9); opacity: 0; } to { transform: scale(1); opacity: 1; } }

        .form-header { margin-bottom: 40px; }
        .form-header i { font-size: 3rem; color: var(--emerald); margin-bottom: 15px; filter: drop-shadow(0 0 15px var(--emerald-glow)); }

        /* Interactive Inputs */
        .input-group { position: relative; margin-bottom: 30px; text-align: left; }
        
        .label-glow {
            display: block;
            margin-bottom: 10px;
            font-size: 0.8rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: var(--emerald);
            opacity: 0.8;
            transition: 0.3s;
        }

        .glass-input {
            width: 100%;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--glass-border);
            padding: 18px 25px;
            border-radius: 18px;
            color: white;
            font-size: 1rem;
            outline: none;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            box-sizing: border-box;
        }

        .glass-input:focus {
            background: rgba(255, 255, 255, 0.08);
            border-color: var(--emerald);
            box-shadow: 0 0 25px var(--emerald-glow);
            transform: translateY(-2px);
        }

        /* Tactical Button */
        .btn-authorize {
            width: 100%;
            background: linear-gradient(135deg, var(--emerald), #059669);
            color: #064e3b;
            padding: 20px;
            border-radius: 20px;
            border: none;
            font-weight: 900;
            font-size: 1.1rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            cursor: pointer;
            transition: 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 15px 35px rgba(16, 185, 129, 0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
        }

        .btn-authorize:hover {
            transform: translateY(-5px) scale(1.02);
            box-shadow: 0 20px 50px rgba(16, 185, 129, 0.6);
            background: #ffffff;
        }

        .btn-authorize:active { transform: scale(0.98); }

        .return-link {
            display: inline-block;
            margin-top: 30px;
            color: rgba(255,255,255,0.4);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 600;
            transition: 0.3s;
        }
        .return-link:hover { color: var(--emerald); }
    </style>
</head>
<body>

    <div class="header-title">Event Management System</div>

    <div class="glass-canvas">
        <div class="form-header">
            <i class="fas fa-shield-heart"></i>
            <h2 style="margin:0; letter-spacing:2px; font-weight:900;">NEW PROTOCOL</h2>
            <p style="opacity:0.5; font-size:0.7rem; text-transform:uppercase; margin-top:5px;">Deploying Event to Global Command</p>
        </div>

        <form method="POST" id="eventForm">
            <div class="input-group">
                <label class="label-glow">Event Identity</label>
                <input type="text" name="event_name" class="glass-input" placeholder="Enter Event Name" required>
            </div>

            <div class="input-group">
                <label class="label-glow">Scheduling Node</label>
                <input type="date" name="event_date" class="glass-input" required>
            </div>

            <div class="input-group">
                <label class="label-glow">Operational Details</label>
                <textarea name="event_description" class="glass-input" style="height:120px; resize:none;" placeholder="Define event objectives..."></textarea>
            </div>

            <button type="submit" name="deploy_event" class="btn-authorize">
                <span>Deploy Event</span>
                <i class="fas fa-paper-plane"></i>
            </button>
        </form>
        
        <a href="vendor_dashboard.php" class="return-link">
            <i class="fas fa-chevron-left"></i> Return to Command Center
        </a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.5.1/dist/confetti.browser.min.js"></script>
    <script>
        // Interactive UI Feedback
        <?php if(isset($success_trigger)): ?>
            confetti({
                particleCount: 150,
                spread: 70,
                origin: { y: 0.6 },
                colors: ['#10b981', '#ffffff', '#064e3b']
            });
            alert("Protocol Verified: Event has been successfully deployed.");
        <?php endif; ?>

        // Dynamic Glow Effect on Input
        const inputs = document.querySelectorAll('.glass-input');
        inputs.forEach(input => {
            input.addEventListener('focus', () => {
                input.previousElementSibling.style.opacity = '1';
                input.previousElementSibling.style.transform = 'translateX(5px)';
            });
            input.addEventListener('blur', () => {
                input.previousElementSibling.style.opacity = '0.8';
                input.previousElementSibling.style.transform = 'translateX(0)';
            });
        });
    </script>
</body>
</html>