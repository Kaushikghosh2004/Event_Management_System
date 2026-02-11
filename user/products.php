<?php
session_start();
require_once '../includes/config.php';

// Security Protocol
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit;
}

// 1. Get Vendor context ensuring 'id' matches URL param
$v_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// 2. 🔍 FIXED: Changed 'user_name' to 'username' to match your DB
$vendor_query = mysqli_query($conn, "SELECT username FROM vendors WHERE id = $v_id");
$vendor_data = mysqli_fetch_assoc($vendor_query);
$v_name = $vendor_data['username'] ?? "Event Vendor";

// 3. Handle Interactive Cart Addition
if (isset($_POST['add_to_cart'])) {
    $p_id = (int)$_POST['product_id'];
    $u_id = $_SESSION['user_id'];
    
    // Logic: Check if service node already exists in user's active cart
    $check = mysqli_query($conn, "SELECT * FROM cart WHERE user_id = $u_id AND product_id = $p_id AND status = 'In Cart'");
    
    if (mysqli_num_rows($check) > 0) {
        mysqli_query($conn, "UPDATE cart SET quantity = quantity + 1 WHERE user_id = $u_id AND product_id = $p_id");
    } else {
        // Standard insert for new service request
        mysqli_query($conn, "INSERT INTO cart (user_id, product_id, quantity, status) VALUES ($u_id, $p_id, 1, 'In Cart')");
    }
    $success_msg = "Service added to your event registry!";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $v_name; ?> | Service Catalog</title>
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
            margin: 0; min-height: 100vh; display: flex; flex-direction: column; align-items: center;
            background: radial-gradient(circle at top right, #1e3a8a, #020617);
            font-family: 'Segoe UI', sans-serif; color: var(--text); padding-bottom: 50px;
        }

        .header-title {
            margin: 40px 0; background: var(--glass); backdrop-filter: blur(15px);
            padding: 15px 60px; border-radius: 50px; border: 1px solid var(--glass-border);
            font-size: 1.2rem; font-weight: 800; letter-spacing: 3px; text-transform: uppercase;
        }

        .products-canvas {
            background: var(--glass); backdrop-filter: blur(30px); width: 1100px;
            padding: 45px; border-radius: 35px; border: 1px solid var(--glass-border);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.4); animation: fadeIn 0.8s ease-out;
        }

        @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

        .products-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 30px; }

        .product-pill {
            background: rgba(255, 255, 255, 0.05); border: 1px solid var(--glass-border);
            padding: 30px 20px; border-radius: 30px; text-align: center; transition: 0.4s;
            display: flex; flex-direction: column; gap: 15px;
        }

        .product-pill:hover { transform: translateY(-10px); border-color: var(--accent); }

        .btn-add-cart {
            background: var(--text); color: #1e3a8a; padding: 12px; border: none;
            border-radius: 12px; font-weight: 800; text-transform: uppercase; cursor: pointer;
        }
        .btn-add-cart:hover { background: var(--emerald); color: #064e3b; }
    </style>
</head>
<body>

    <div class="header-title"><?php echo htmlspecialchars($v_name); ?></div>

    <div class="products-canvas">
        <div style="display: flex; justify-content: space-between; margin-bottom: 40px;">
            <h2 style="margin:0; opacity:0.8;"><i class="fas fa-layer-group" style="color:var(--accent); margin-right:10px;"></i> Service Catalog</h2>
            <div style="display:flex; gap:15px;">
                <a href="dashboard.php" style="text-decoration:none; color:white; background:var(--glass); padding:10px 25px; border-radius:12px; border:1px solid var(--glass-border); font-weight:bold;">Home</a>
                <a href="view_cart.php" style="text-decoration:none; color:var(--accent); background:var(--glass); padding:10px 25px; border-radius:12px; border:1px solid var(--glass-border); font-weight:bold;">My Cart</a>
            </div>
        </div>

        <div class="products-grid">
            <?php
            // 4. 🔍 Querying matching your ENUM ('In Stock')
            $p_query = mysqli_query($conn, "SELECT * FROM products WHERE vendor_id = $v_id AND status = 'In Stock'");
            if(mysqli_num_rows($p_query) > 0):
                while($p = mysqli_fetch_assoc($p_query)): ?>
                    <form method="POST" class="product-pill">
                        <input type="hidden" name="product_id" value="<?php echo $p['id']; ?>">
                        <div style="background:rgba(255,255,255,0.05); width:60px; height:60px; border-radius:50%; margin:0 auto; display:flex; align-items:center; justify-content:center;">
                            <i class="fas fa-box-open" style="color:var(--accent); font-size:1.5rem;"></i>
                        </div>
                        <h3 style="margin:0; font-size:1.1rem; color:var(--accent);"><?php echo htmlspecialchars($p['product_name']); ?></h3>
                        <div style="font-size:1.2rem; font-weight:900;">Rs. <?php echo number_format($p['price'], 2); ?></div>
                        <button type="submit" name="add_to_cart" class="btn-add-cart">
                            <i class="fas fa-cart-plus"></i> Add to Cart
                        </button>
                    </form>
                <?php endwhile;
            else: ?>
                <div style="grid-column: 1/-1; text-align:center; padding:100px; opacity:0.4;">
                    <i class="fas fa-box-open" style="font-size:3rem; margin-bottom:20px;"></i>
                    <p>No services are currently listed for this vendor node.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php if(isset($success_msg)): ?>
        <div style="position:fixed; bottom:30px; right:30px; background:var(--emerald); color:white; padding:15px 30px; border-radius:15px; font-weight:bold; box-shadow:0 10px 30px rgba(0,0,0,0.3);">
            <?php echo $success_msg; ?>
        </div>
    <?php endif; ?>
</body>
</html>