<?php
session_start();
require_once '../includes/config.php';

// Security Protocol
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'vendor') {
    header("Location: login.php");
    exit;
}

$v_id = $_SESSION['vendor_id'];

// --- GOVERNANCE LOGIC ---
// 1. Purge Protocol (Quick Delete)
if (isset($_GET['delete'])) {
    $p_id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM products WHERE id = $p_id AND vendor_id = $v_id");
    header("Location: View_Product.php?msg=Node_Purged");
    exit;
}

// 2. Stock Toggle Logic (Bonus feature for "Stock Status")
if (isset($_GET['toggle_stock'])) {
    $p_id = (int)$_GET['toggle_stock'];
    mysqli_query($conn, "UPDATE products SET status = IF(status='In Stock', 'Out of Stock', 'In Stock') WHERE id = $p_id AND vendor_id = $v_id");
    header("Location: View_Product.php");
    exit;
}

$res = mysqli_query($conn, "SELECT * FROM products WHERE vendor_id = $v_id ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Visual Registry | Advanced Governance</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --glass: rgba(255, 255, 255, 0.05); --emerald: #10b981; --text: #ffffff; }
        body { margin: 0; background: radial-gradient(at 0% 100%, #064e3b 0, transparent 50%), #020617; font-family: 'Segoe UI', sans-serif; color: var(--text); padding: 40px; }

        .inventory-canvas { background: var(--glass); backdrop-filter: blur(40px); max-width: 1200px; margin: 0 auto; border-radius: 40px; padding: 50px; border: 1px solid rgba(255,255,255,0.1); }
        .product-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 30px; }

        /* 📦 Interactive Node */
        .product-node {
            background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255,255,255,0.1); padding: 35px 20px; border-radius: 35px; text-align: center;
            transition: 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); position: relative;
        }
        .product-node:hover { transform: translateY(-10px); border-color: var(--emerald); background: rgba(16, 185, 129, 0.05); }

        /* 🛡️ Governance Overlays */
        .purge-trigger { position: absolute; top: 20px; right: 20px; color: #f87171; cursor: pointer; opacity: 0.4; transition: 0.3s; font-size: 1.2rem; }
        .product-node:hover .purge-trigger { opacity: 1; }

        /* 🏷️ Stock Badges */
        .stock-badge {
            position: absolute; top: 20px; left: 20px; font-size: 0.65rem; font-weight: 900; padding: 5px 12px; border-radius: 50px; text-transform: uppercase;
            border: 1px solid currentColor; cursor: pointer;
        }
        .in-stock { color: var(--emerald); background: rgba(16, 185, 129, 0.1); }
        .out-of-stock { color: #94a3b8; background: rgba(148, 163, 184, 0.1); }

        .img-box { width: 120px; height: 120px; background: white; border-radius: 50%; margin: 10px auto 20px; overflow: hidden; border: 3px solid var(--emerald); }
        .img-box img { width: 100%; height: 100%; object-fit: cover; }

        /* 🛠️ Edit Button */
        .btn-edit {
            margin-top: 20px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.2); color: white;
            padding: 8px 20px; border-radius: 50px; font-size: 0.8rem; font-weight: bold; cursor: pointer; transition: 0.3s;
        }
        .btn-edit:hover { background: white; color: #020617; }
    </style>
</head>
<body>

    <div class="inventory-canvas">
        <header style="display:flex; justify-content:space-between; align-items:center; margin-bottom:50px;">
            <h2 style="letter-spacing:2px;"><i class="fas fa-microchip" style="color:var(--emerald);"></i> VISUAL REGISTRY</h2>
            <div style="display:flex; gap:15px;">
                <a href="vendor_dashboard.php" style="color:white; text-decoration:none; opacity:0.6; font-weight:bold;">Dashboard</a>
                <a href="add_item.php" style="color:var(--emerald); text-decoration:none; font-weight:bold;">+ Add Protocol</a>
            </div>
        </header>

        <div class="product-grid">
            <?php while($p = mysqli_fetch_assoc($res)): ?>
            <div class="product-node">
                <a href="View_Product.php?delete=<?php echo $p['id']; ?>" class="purge-trigger" onclick="return confirm('Confirm Purge Protocol?')">
                    <i class="fas fa-times-circle"></i>
                </a>

                <a href="View_Product.php?toggle_stock=<?php echo $p['id']; ?>" style="text-decoration:none;">
                    <div class="stock-badge <?php echo ($p['status'] == 'Out of Stock') ? 'out-of-stock' : 'in-stock'; ?>">
                        <?php echo $p['status'] ?? 'In Stock'; ?>
                    </div>
                </a>

                <div class="img-box"><img src="../assets/images/<?php echo $p['image']; ?>"></div>
                <strong style="display:block; font-size:1.1rem;"><?php echo htmlspecialchars($p['product_name']); ?></strong>
                <span style="color:var(--emerald); font-weight:900; font-size:0.9rem;">Rs/- <?php echo number_format($p['price']); ?></span>

                <div style="margin-top:10px;">
                    <button class="btn-edit" onclick="location.href='edit_product.php?id=<?php echo $p['id']; ?>'">
                        <i class="fas fa-pen" style="font-size:0.7rem;"></i> Edit Node
                    </button>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>

</body>
</html>