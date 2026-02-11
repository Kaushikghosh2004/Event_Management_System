<?php
session_start();
require_once '../includes/config.php';

// Security: User Session Verification
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit;
}

$u_id = $_SESSION['user_id'];

// --- Logic Operations (Remain Powerful & Secure) ---
if (isset($_GET['remove'])) {
    $cart_id = (int)$_GET['remove'];
    mysqli_query($conn, "DELETE FROM cart WHERE id = $cart_id AND user_id = $u_id");
    header("Location: cart.php");
}

if (isset($_POST['delete_all'])) {
    mysqli_query($conn, "DELETE FROM cart WHERE user_id = $u_id AND status = 'In Cart'");
    header("Location: cart.php");
}

if (isset($_POST['update_qty'])) {
    $cart_id = (int)$_POST['cart_id'];
    $new_qty = (int)$_POST['quantity'];
    mysqli_query($conn, "UPDATE cart SET quantity = $new_qty WHERE id = $cart_id");
}

$cart_query = mysqli_query($conn, "SELECT c.*, p.product_name, p.price, p.image 
                                   FROM cart c 
                                   JOIN products p ON c.product_id = p.id 
                                   WHERE c.user_id = $u_id AND c.status = 'In Cart'");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Cart | Event Management System</title>
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
            padding-bottom: 50px;
        }

        /* Upper Floating Title */
        .header-title {
            margin: 40px 0;
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

        /* 🔮 Main Glass Canvas */
        .cart-canvas {
            background: var(--glass);
            backdrop-filter: blur(30px);
            -webkit-backdrop-filter: blur(30px);
            width: 1100px;
            padding: 40px;
            border-radius: 35px;
            border: 1px solid var(--glass-border);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.4);
            animation: fadeIn 0.8s ease-out;
        }

        @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

        /* Glass Nav Header */
        .nav-header { display: flex; justify-content: space-between; margin-bottom: 40px; }
        
        .btn-glass-nav {
            background: var(--glass);
            border: 1px solid var(--glass-border);
            padding: 10px 20px;
            border-radius: 12px;
            text-decoration: none;
            color: var(--text);
            font-weight: 600;
            font-size: 0.85rem;
            transition: 0.3s;
        }
        .btn-glass-nav:hover { background: rgba(255,255,255,0.15); transform: translateY(-3px); border-color: var(--accent); }

        /* Cart Table: Interactive Glass Grid */
        .cart-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .cart-table th { padding: 20px; text-align: left; background: rgba(255,255,255,0.05); border-radius: 10px; font-weight: 800; opacity: 0.7; }
        .cart-table td { padding: 20px; border-bottom: 1px solid var(--glass-border); }

        .product-img { width: 60px; height: 60px; border-radius: 12px; border: 1px solid var(--glass-border); object-fit: cover; }

        /* Grand Total Glass Bar */
        .total-bar {
            margin-top: 30px;
            background: rgba(255,255,255,0.05);
            padding: 25px 40px;
            border-radius: 20px;
            border: 1px solid var(--glass-border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 1.2rem;
            font-weight: 800;
        }

        /* Action Buttons */
        .btn-remove { color: #f87171; text-decoration: none; font-weight: bold; transition: 0.3s; }
        .btn-remove:hover { text-shadow: 0 0 10px #ef4444; transform: scale(1.1); }

        .btn-delete-all {
            background: transparent; border: 1px solid #f87171; color: #f87171;
            padding: 10px 25px; border-radius: 10px; cursor: pointer; transition: 0.3s;
        }
        .btn-delete-all:hover { background: #ef4444; color: white; }

        .checkout-row { text-align: center; margin-top: 50px; }
        .btn-checkout {
            background: linear-gradient(135deg, var(--emerald), #059669);
            color: #064e3b;
            padding: 20px 100px;
            border-radius: 15px;
            text-decoration: none;
            font-weight: 900;
            font-size: 1.2rem;
            text-transform: uppercase;
            box-shadow: 0 10px 30px rgba(16, 185, 129, 0.4);
            display: inline-block;
            transition: 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        .btn-checkout:hover { transform: translateY(-5px); box-shadow: 0 15px 40px rgba(16, 185, 129, 0.6); }
    </style>
</head>
<body>

    <div class="header-title">Event Management System</div>

    <div class="cart-canvas">
        <div class="nav-header">
            <a href="dashboard.php" class="btn-glass-nav"><i class="fas fa-home"></i> Home</a>
            <a href="products.php" class="btn-glass-nav"><i class="fas fa-search"></i> View Products</a>
            <a href="request_item.php" class="btn-glass-nav"><i class="fas fa-plus"></i> Request Item</a>
            <a href="order_status.php" class="btn-glass-nav"><i class="fas fa-truck"></i> Product Status</a>
            <a href="../logout.php" class="btn-glass-nav" style="color:#f87171;"><i class="fas fa-power-off"></i> Logout</a>
        </div>

        <h2 style="margin-bottom:30px; opacity:0.8; letter-spacing:1px;"><i class="fas fa-shopping-cart" style="color:var(--accent);"></i> Shopping Cart</h2>

        <table class="cart-table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $grand_total = 0;
                if(mysqli_num_rows($cart_query) > 0):
                    while($item = mysqli_fetch_assoc($cart_query)): 
                        $total_item_price = $item['price'] * $item['quantity'];
                        $grand_total += $total_item_price;
                ?>
                    <tr>
                        <td><img src="../assets/images/<?php echo $item['image']; ?>" class="product-img"></td>
                        <td><strong><?php echo htmlspecialchars($item['product_name']); ?></strong></td>
                        <td style="opacity:0.8;">Rs. <?php echo $item['price']; ?>/-</td>
                        <td>
                            <form method="POST" id="qtyForm_<?php echo $item['id']; ?>">
                                <input type="hidden" name="cart_id" value="<?php echo $item['id']; ?>">
                                <select name="quantity" onchange="this.form.submit()" style="background:rgba(255,255,255,0.1); color:white; border:1px solid var(--glass-border); padding:8px; border-radius:8px; cursor:pointer;">
                                    <?php for($i=1; $i<=10; $i++): ?>
                                        <option value="<?php echo $i; ?>" <?php if($i == $item['quantity']) echo 'selected'; ?> style="color:black;"><?php echo $i; ?></option>
                                    <?php endfor; ?>
                                </select>
                                <input type="hidden" name="update_qty">
                            </form>
                        </td>
                        <td style="color:var(--accent); font-weight:bold;">Rs. <?php echo $total_item_price; ?>/-</td>
                        <td>
                            <a href="cart.php?remove=<?php echo $item['id']; ?>" class="btn-remove">
                                <i class="fas fa-trash-alt"></i> Remove
                            </a>
                        </td>
                    </tr>
                <?php endwhile; else: ?>
                    <tr><td colspan="6" style="text-align:center; padding:50px; opacity:0.5;">Your service cart is currently empty.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="total-bar">
            <div>
                <span style="opacity:0.6; font-size:1rem; text-transform:uppercase;">Grand Total</span>
                <div style="color:var(--accent); font-size:1.8rem;">Rs. <?php echo $grand_total; ?>/-</div>
            </div>
            <form method="POST">
                <button type="submit" name="delete_all" class="btn-delete-all">
                    <i class="fas fa-dumpster"></i> Clear All Items
                </button>
            </form>
        </div>

        <div class="checkout-row">
            <?php if($grand_total > 0): ?>
                <a href="checkout.php" class="btn-checkout">
                    Confirm & Proceed <i class="fas fa-arrow-right" style="margin-left:15px;"></i>
                </a>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>