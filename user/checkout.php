<?php
session_start();
require_once '../includes/config.php';

// Security: User Session Verification
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit;
}

$u_id = $_SESSION['user_id'];

// Fetch Total Amount for display
$total_query = mysqli_query($conn, "SELECT SUM(p.price * c.quantity) as grand_total 
                                    FROM cart c 
                                    JOIN products p ON c.product_id = p.id 
                                    WHERE c.user_id = $u_id AND c.status = 'In Cart'");
$total_data = mysqli_fetch_assoc($total_query);
$grand_total = $total_data['grand_total'] ?? 0;

// Handle Order Now Action
if (isset($_POST['order_now'])) {
    // Sanitize using your established 'secure' function
    $name = secure($_POST['name']);
    $email = secure($_POST['email']);
    $address = secure($_POST['address']);
    $pay_method = secure($_POST['payment_method']);

    // Logic: Transition cart to 'Ordered' state
    $update_cart = mysqli_query($conn, "UPDATE cart SET status = 'Ordered' WHERE user_id = $u_id AND status = 'In Cart'");

    if ($update_cart) {
        header("Location: success.php"); 
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout | Event Management System</title>
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
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            background: radial-gradient(circle at top left, #1e3a8a, #020617);
            font-family: 'Segoe UI', sans-serif;
            color: var(--text);
            padding: 40px 0;
        }

        /* Upper Floating Title */
        .header-title {
            margin-bottom: 40px;
            background: var(--glass);
            backdrop-filter: blur(15px);
            padding: 15px 60px;
            border-radius: 50px;
            border: 1px solid var(--glass-border);
            font-size: 1.2rem;
            font-weight: 800;
            letter-spacing: 3px;
            text-transform: uppercase;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        }

        /* 🔮 The Main Glass Canvas */
        .checkout-canvas {
            background: var(--glass);
            backdrop-filter: blur(30px);
            -webkit-backdrop-filter: blur(30px);
            width: 1000px;
            padding: 50px;
            border-radius: 35px;
            border: 1px solid var(--glass-border);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.4);
            animation: slideUp 0.8s ease-out;
        }

        @keyframes slideUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }

        /* Total Amount Banner matching mockup structure */
        .total-banner {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--glass-border);
            padding: 20px;
            border-radius: 20px;
            text-align: center;
            margin-bottom: 40px;
            font-size: 1.5rem;
            font-weight: 800;
        }
        .total-banner span { color: var(--accent); }

        /* Two-Column Form Grid */
        .checkout-form { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; }

        .input-group { position: relative; margin-bottom: 20px; }
        .label-glow {
            display: block;
            margin-bottom: 10px;
            font-size: 0.85rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--accent);
            opacity: 0.9;
        }

        .glass-input {
            width: 100%;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--glass-border);
            padding: 15px 20px;
            border-radius: 12px;
            color: white;
            font-size: 1rem;
            outline: none;
            transition: 0.3s;
            box-sizing: border-box;
        }
        .glass-input:focus { border-color: var(--accent); background: rgba(255,255,255,0.1); box-shadow: 0 0 20px rgba(96, 165, 250, 0.3); }

        /* Payment Dropdown Selection */
        select.glass-input { cursor: pointer; }
        select.glass-input option { background: #0f172a; color: white; }

        /* powerful Decision Button */
        .btn-order-row { grid-column: span 2; text-align: center; margin-top: 30px; }
        .btn-order {
            background: linear-gradient(135deg, var(--emerald), #059669);
            color: #064e3b;
            padding: 20px 100px;
            border-radius: 15px;
            border: none;
            font-weight: 900;
            font-size: 1.2rem;
            text-transform: uppercase;
            cursor: pointer;
            transition: 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 10px 30px rgba(16, 185, 129, 0.4);
        }
        .btn-order:hover { transform: translateY(-5px); box-shadow: 0 15px 40px rgba(16, 185, 129, 0.6); }

        /* Loading Spinner Overlay */
        #paymentOverlay {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(2, 6, 23, 0.95);
            backdrop-filter: blur(20px);
            display: none;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }
        .spinner { width: 70px; height: 70px; border: 5px solid var(--glass); border-left-color: var(--accent); border-radius: 50%; animation: spin 1s linear infinite; margin-bottom: 20px; }
        @keyframes spin { to { transform: rotate(360deg); } }
    </style>
</head>
<body>

    <div class="header-title">Event Management System</div>

    <div id="paymentOverlay">
        <div class="spinner"></div>
        <h3 style="color:var(--accent);">SECURE PAYMENT AUTHORIZATION...</h3>
        <p style="opacity:0.6;">Verifying Event Service Allocation</p>
    </div>

    <div class="checkout-canvas">
        <div class="total-banner">
            Order Summary Total: <span>Rs. <?php echo $grand_total; ?>/-</span>
        </div>

        <form method="POST" id="checkoutForm" class="checkout-form">
            <div class="form-side">
                <div class="input-group">
                    <label class="label-glow">Customer Identity</label>
                    <input type="text" name="name" class="glass-input" placeholder="Full Name" required>
                </div>
                <div class="input-group">
                    <label class="label-glow">Access Email</label>
                    <input type="email" name="email" class="glass-input" placeholder="Email Address" required>
                </div>
                <div class="input-group">
                    <label class="label-glow">Service Address</label>
                    <input type="text" name="address" class="glass-input" placeholder="Street Address" required>
                </div>
                <div class="input-group">
                    <label class="label-glow">City Node</label>
                    <input type="text" name="city" class="glass-input" placeholder="City" required>
                </div>
            </div>

            <div class="form-side">
                <div class="input-group">
                    <label class="label-glow">Contact Protocol</label>
                    <input type="text" name="phone" class="glass-input" placeholder="Phone Number" required>
                </div>
                <div class="input-group">
                    <label class="label-glow">Payment Channel</label>
                    <select name="payment_method" class="glass-input" required>
                        <option value="" disabled selected>Select Channel</option>
                        <option value="Cash">Cash on Service</option>
                        <option value="UPI">UPI Digital Payment</option>
                    </select>
                </div>
                <div class="input-group">
                    <label class="label-glow">Region State</label>
                    <input type="text" name="state" class="glass-input" placeholder="State" required>
                </div>
                <div class="input-group">
                    <label class="label-glow">Postal Zone</label>
                    <input type="text" name="pincode" class="glass-input" placeholder="Pin Code" required>
                </div>
            </div>

            <div class="btn-order-row">
                <button type="submit" name="order_now" class="btn-order">
                    Confirm & Order Now <i class="fas fa-check-double" style="margin-left:10px;"></i>
                </button>
                <p style="margin-top:20px;"><a href="cart.php" style="color:rgba(255,255,255,0.4); text-decoration:none;">Return to Cart</a></p>
            </div>
        </form>
    </div>

    <script>
        // Trigger Secure Payment Overlay on submit
        document.getElementById('checkoutForm').onsubmit = function() {
            document.getElementById('paymentOverlay').style.display = 'flex';
        };
    </script>
</body>
</html>