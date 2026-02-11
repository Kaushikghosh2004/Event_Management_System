<?php
session_start();
require_once '../includes/config.php';

// Security: Access restricted to authorized Vendors
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'vendor') {
    header("Location: login.php");
    exit;
}

$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Handle the status change logic
if (isset($_POST['update_status'])) {
    $new_status = mysqli_real_escape_string($conn, $_POST['status']);
    
    // SQL: Updating the clearance status in the registry
    $update_query = "UPDATE cart SET status = '$new_status' WHERE id = $order_id";
    if (mysqli_query($conn, $update_query)) {
        header("Location: product_status.php?msg=Protocol Updated");
        exit;
    }
}

// Fetch current status for pre-selection
$current_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT status FROM cart WHERE id = $order_id"));
$current_status = $current_data['status'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Protocol | Vendor Command</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --glass: rgba(255, 255, 255, 0.05);
            --glass-border: rgba(255, 255, 255, 0.12);
            --emerald: #10b981;
            --text: #ffffff;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            background: radial-gradient(at 0% 100%, #064e3b 0, transparent 50%),
                        radial-gradient(at 100% 0%, #065f46 0, transparent 50%),
                        #020617;
            font-family: 'Segoe UI', sans-serif;
            color: var(--text);
            padding: 40px;
        }

        /* 📋 Main Canvas */
        .glass-canvas {
            background: var(--glass);
            backdrop-filter: blur(40px);
            width: 600px;
            padding: 50px;
            border-radius: 40px;
            border: 1px solid var(--glass-border);
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.6);
            text-align: center;
            position: relative;
        }

        .header-main { margin-bottom: 40px; }
        .header-main i { font-size: 2.5rem; color: var(--emerald); filter: drop-shadow(0 0 10px var(--emerald)); }

        /* Tactical Radio Group */
        .status-options {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-bottom: 40px;
        }

        .option-node {
            display: flex;
            align-items: center;
            background: rgba(255,255,255,0.03);
            border: 1px solid var(--glass-border);
            padding: 15px 25px;
            border-radius: 15px;
            cursor: pointer;
            transition: 0.3s;
        }

        .option-node:hover { background: rgba(16, 185, 129, 0.05); border-color: var(--emerald); }

        .radio-custom {
            appearance: none;
            width: 20px;
            height: 20px;
            border: 2px solid var(--glass-border);
            border-radius: 50%;
            margin-right: 20px;
            position: relative;
            cursor: pointer;
        }

        .radio-custom:checked { border-color: var(--emerald); background: var(--emerald); }

        .label-text { font-weight: 700; letter-spacing: 1px; font-size: 0.9rem; text-transform: uppercase; }

        /* State Change Indicator */
        .change-indicator {
            position: absolute;
            right: -120px;
            top: 50%;
            transform: translateY(-50%) rotate(90deg);
            opacity: 0.5;
            font-size: 0.7rem;
            letter-spacing: 2px;
            font-weight: 900;
        }

        .btn-update-node {
            width: 100%;
            background: var(--emerald);
            color: #064e3b;
            padding: 18px;
            border-radius: 15px;
            border: none;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 2px;
            cursor: pointer;
            transition: 0.4s;
            box-shadow: 0 10px 20px rgba(16, 185, 129, 0.3);
        }
        .btn-update-node:hover { transform: scale(1.02); box-shadow: 0 15px 30px rgba(16, 185, 129, 0.5); }
    </style>
</head>
<body>

    <div style="margin-bottom:50px; text-transform:uppercase; letter-spacing:5px; font-weight:900; opacity:0.8;">Financial Governance Hub</div>

    <form method="POST" class="glass-canvas">
        <div class="header-main">
            <i class="fas fa-sync-alt"></i>
            <h2 style="margin-top:15px; letter-spacing:2px;">UPDATE PROTOCOL</h2>
            <p style="font-size:0.7rem; opacity:0.5; text-transform:uppercase;">Modify Operational Clearance for ID #<?php echo $order_id; ?></p>
        </div>

        <div class="status-options">
            <label class="option-node">
                <input type="radio" name="status" value="Received" class="radio-custom" <?php if($current_status == 'Received') echo 'checked'; ?> required>
                <span class="label-text">Protocol Received</span>
            </label>

            <label class="option-node">
                <input type="radio" name="status" value="Ready for Shipping" class="radio-custom" <?php if($current_status == 'Ready for Shipping') echo 'checked'; ?>>
                <span class="label-text">Ready for Shipping</span>
            </label>

            <label class="option-node">
                <input type="radio" name="status" value="Out For Delivery" class="radio-custom" <?php if($current_status == 'Out For Delivery') echo 'checked'; ?>>
                <span class="label-text">Out For Delivery</span>
            </label>
        </div>

        <div class="change-indicator">STATE WILL CHANGE <i class="fas fa-long-arrow-alt-right"></i></div>

        <button type="submit" name="update_status" class="btn-update-node">Apply Protocol Change</button>

        <div style="margin-top:30px;">
            <a href="product_status.php" style="color:white; opacity:0.4; text-decoration:none; font-size:0.8rem; font-weight:bold;">Cancel Governance Action</a>
        </div>
    </form>

</body>
</html>