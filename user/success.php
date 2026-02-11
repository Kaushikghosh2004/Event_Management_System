<?php
session_start();
require_once '../includes/config.php';

// Security: User Session Verification
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit;
}

$u_id = $_SESSION['user_id'];

// Fetch the most recent order details for the receipt node
$total_query = mysqli_query($conn, "SELECT SUM(p.price * c.quantity) as grand_total 
                                    FROM cart c 
                                    JOIN products p ON c.product_id = p.id 
                                    WHERE c.user_id = $u_id AND c.status = 'Ordered'");
$total_data = mysqli_fetch_assoc($total_query);
$final_amount = $total_data['grand_total'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Successful | Event Management System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --glass: rgba(255, 255, 255, 0.08);
            --glass-border: rgba(255, 255, 255, 0.15);
            --accent: #60a5fa;
            --emerald: #10b981;
            --text: #ffffff;
        }

        body {
            margin: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: radial-gradient(circle at center, #1e3a8a, #020617);
            font-family: 'Segoe UI', sans-serif;
            overflow: hidden;
        }

        /* 🔮 The Glass Success Canvas */
        .success-popup {
            background: var(--glass);
            backdrop-filter: blur(30px);
            -webkit-backdrop-filter: blur(30px);
            width: 550px;
            padding: 50px;
            border-radius: 40px;
            border: 1px solid var(--glass-border);
            text-align: center;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.5);
            animation: popIn 0.8s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
        }

        @keyframes popIn { from { opacity: 0; transform: scale(0.8); } to { opacity: 1; transform: scale(1); } }

        .check-icon {
            font-size: 4rem;
            color: var(--emerald);
            margin-bottom: 20px;
            text-shadow: 0 0 20px rgba(16, 185, 129, 0.5);
            animation: bounce 2s infinite;
        }

        @keyframes bounce { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-10px); } }

        .thank-you {
            font-size: 2rem;
            font-weight: 900;
            letter-spacing: 5px;
            margin-bottom: 30px;
            color: var(--text);
            text-transform: uppercase;
        }

        /* Floating Amount Banner */
        .amount-banner {
            background: rgba(16, 185, 129, 0.15);
            color: var(--emerald);
            padding: 15px;
            border-radius: 20px;
            font-weight: 800;
            font-size: 1.3rem;
            margin-bottom: 40px;
            border: 1px solid var(--emerald);
            box-shadow: 0 0 15px rgba(16, 185, 129, 0.2);
        }

        /* Detail Registry Grid */
        .details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 40px;
        }

        .detail-tile {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--glass-border);
            padding: 12px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--accent);
            opacity: 0.8;
        }

        /* Navigation Node */
        .btn-continue {
            background: var(--text);
            color: #020617;
            padding: 18px 60px;
            border-radius: 15px;
            text-decoration: none;
            font-weight: 900;
            text-transform: uppercase;
            display: inline-block;
            transition: 0.4s;
            box-shadow: 0 10px 20px rgba(0,0,0,0.3);
        }

        .btn-continue:hover {
            background: var(--accent);
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(96, 165, 250, 0.4);
        }

        /* Background Particle Effect Overlay */
        #confetti-canvas { position: absolute; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; z-index: -1; }
    </style>
</head>
<body>

<canvas id="confetti-canvas"></canvas>

<div class="success-popup">
    <div class="check-icon"><i class="fas fa-certificate"></i></div>
    <div class="thank-you">Thank You</div>

    <div class="amount-banner">
        TOTAL SECURED: Rs. <?php echo $final_amount; ?>/-
    </div>

    <div class="details-grid">
        <div class="detail-tile">Verified Name</div>
        <div class="detail-tile">Contact Node</div>
        
        <div class="detail-tile">Auth E-mail</div>
        <div class="detail-tile">Pay Channel</div>
        
        <div class="detail-tile">Service Area</div>
        <div class="detail-tile">Region State</div>
        
        <div class="detail-tile">City Node</div>
        <div class="detail-tile">Postal Zone</div>
    </div>

    <a href="dashboard.php" class="btn-continue">Continue Shopping</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.5.1/dist/confetti.browser.min.js"></script>
<script>
    // Launch Confetti on Load
    var end = Date.now() + (2 * 1000);
    var colors = ['#60a5fa', '#10b981', '#ffffff'];

    (function frame() {
      confetti({
        particleCount: 3,
        angle: 60,
        spread: 55,
        origin: { x: 0 },
        colors: colors
      });
      confetti({
        particleCount: 3,
        angle: 120,
        spread: 55,
        origin: { x: 1 },
        colors: colors
      });

      if (Date.now() < end) {
        requestAnimationFrame(frame);
      }
    }());
</script>

</body>
</html>