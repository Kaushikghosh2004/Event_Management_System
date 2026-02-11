<?php
session_start();
require_once '../includes/config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit;
}

$u_id = $_SESSION['user_id'];
// Join cart with products to display requested services
$cart_items = mysqli_query($conn, "SELECT c.id as cart_id, p.product_name, p.price, c.status 
                                   FROM cart c 
                                   JOIN products p ON c.product_id = p.id 
                                   WHERE c.user_id = $u_id AND c.status = 'In Cart'");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Service Cart | EMS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --glass: rgba(255,255,255,0.05); --ocean: #3b82f6; }
        body { margin:0; background:#020617; color:white; font-family:'Segoe UI'; padding:50px; }
        .cart-container { background:var(--glass); backdrop-filter:blur(25px); border-radius:30px; border:1px solid rgba(255,255,255,0.1); padding:40px; }
        table { width:100%; border-collapse:collapse; margin-top:20px; }
        th { text-align:left; opacity:0.5; font-size:0.75rem; text-transform:uppercase; padding:15px; border-bottom:1px solid rgba(255,255,255,0.1); }
        td { padding:15px; border-bottom:1px solid rgba(255,255,255,0.05); }
        .btn-checkout { background:var(--ocean); color:#0f172a; padding:10px 20px; border-radius:10px; text-decoration:none; font-weight:800; font-size:0.75rem; }
    </style>
</head>
<body>
    <div class="cart-container">
        <h2><i class="fas fa-shopping-basket"></i> PENDING REQUESTS</h2>
        <table>
            <thead><tr><th>Service Node</th><th>Cost</th><th>Action</th></tr></thead>
            <tbody>
                <?php while($item = mysqli_fetch_assoc($cart_items)): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($item['product_name']); ?></strong></td>
                        <td>$<?php echo number_format($item['price'], 2); ?></td>
                        <td><a href="checkout_logic.php?id=<?php echo $item['cart_id']; ?>" class="btn-checkout">FINALIZE REQUEST</a></td>
                    </tr>
                <?php endwhile; ?>
                <?php if(mysqli_num_rows($cart_items) == 0): ?>
                    <tr><td colspan="3" style="text-align:center; padding:40px; opacity:0.4;">Registry is currently empty.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>