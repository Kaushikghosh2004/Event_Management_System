<?php
session_start();
require_once '../includes/config.php';

// Security Protocol: Restrict access to authenticated Vendors
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'vendor') {
    header("Location: login.php");
    exit;
}

$v_id = $_SESSION['vendor_id'];

// --- 🛠️ DEPLOYMENT PROTOCOL ---
if (isset($_POST['add_product'])) {
    $p_name = mysqli_real_escape_string($conn, $_POST['p_name']);
    $p_price = (float)$_POST['p_price'];
    
    // 🔍 Capturing Status from the Dropdown
    $p_status = mysqli_real_escape_string($conn, $_POST['p_status']);
    
    $image_name = $_FILES['p_image']['name'];
    $tmp_name = $_FILES['p_image']['tmp_name'];
    
    // Filename Sanitization
    $clean_name = time() . "_" . preg_replace("/[^a-zA-Z0-9.]/", "_", $image_name);
    $target_dir = "../assets/images/";
    
    if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);

    if (move_uploaded_file($tmp_name, $target_dir . $clean_name)) {
        // 🚀 INSERT logic now explicitly includes the chosen status
        $sql = "INSERT INTO products (vendor_id, product_name, price, image, status) 
                VALUES ('$v_id', '$p_name', '$p_price', '$clean_name', '$p_status')";
        
        if(mysqli_query($conn, $sql)) {
            $success_msg = "Protocol Deployed Successfully!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Service Hub | Vendor Command</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --glass: rgba(255, 255, 255, 0.08);
            --glass-border: rgba(255, 255, 255, 0.15);
            --emerald: #10b981;
            --ruby: #f87171;
            --text: #ffffff;
        }

        body {
            margin: 0; min-height: 100vh;
            background: radial-gradient(at 0% 100%, #064e3b 0, transparent 50%),
                        radial-gradient(at 100% 0%, #065f46 0, transparent 50%), #020617;
            font-family: 'Segoe UI', sans-serif; color: var(--text); padding: 30px;
        }

        .top-banner {
            background: var(--glass); backdrop-filter: blur(15px);
            padding: 15px 40px; display: flex; justify-content: space-between; align-items: center;
            border-radius: 50px; border: 1px solid var(--glass-border); margin-bottom: 40px;
        }

        .nav-btn {
            background: rgba(255,255,255,0.05); color: white; padding: 10px 20px;
            text-decoration: none; border-radius: 12px; font-weight: 600;
            border: 1px solid var(--glass-border); transition: 0.3s; font-size: 0.85rem; margin-left: 10px;
        }
        .nav-btn:hover, .nav-btn.active { background: var(--emerald); color: #064e3b; }

        .dashboard-grid { display: grid; grid-template-columns: 420px 1fr; gap: 30px; }

        .glass-card {
            background: var(--glass); backdrop-filter: blur(25px);
            padding: 40px; border-radius: 35px; border: 1px solid var(--glass-border);
        }

        .glass-input, .glass-select {
            width: 100%; background: rgba(255, 255, 255, 0.05); border: 1px solid var(--glass-border);
            padding: 15px 20px; border-radius: 15px; color: white; margin-bottom: 25px; outline: none; box-sizing: border-box;
        }
        .glass-select option { background: #020617; color: white; }

        .btn-deploy {
            width: 100%; background: var(--emerald); color: #064e3b; padding: 18px;
            border-radius: 15px; border: none; font-weight: 800; text-transform: uppercase;
            cursor: pointer; transition: 0.4s;
        }
    </style>
</head>
<body>

    <div class="top-banner">
        <h3 style="margin:0;"><i class="fas fa-microchip" style="color:var(--emerald);"></i> Service Hub</h3>
        <div class="nav-btns">
            <a href="vendor_dashboard.php" class="nav-btn">Dashboard</a>
            <a href="add_item.php" class="nav-btn active">Add Item</a>
            <a href="product_status.php" class="nav-btn">Status Monitor</a>
            <a href="../logout.php" class="nav-btn" style="border-color:var(--ruby); color:var(--ruby);">Logout</a>
        </div>
    </div>

    <div class="dashboard-grid">
        <div class="glass-card">
            <h3 style="margin-top:0; opacity:0.8;"><i class="fas fa-cloud-upload-alt" style="color:var(--emerald);"></i> New Service Entry</h3>
            <form method="POST" enctype="multipart/form-data">
                <input type="text" name="p_name" class="glass-input" placeholder="Service Name" required>
                <input type="number" name="p_price" class="glass-input" placeholder="Unit Price (Rs.)" required>
                
                <label style="display:block; font-size:0.75rem; margin-bottom:10px; opacity:0.6; padding-left:10px;">AVAILABILITY STATUS</label>
                <select name="p_status" class="glass-select" required>
                    <option value="In Stock">✅ Available (In Stock)</option>
                    <option value="Out of Stock">❌ Unavailable (Out of Stock)</option>
                </select>
                <input type="file" name="p_image" class="glass-input" required>
                <button type="submit" name="add_product" class="btn-deploy">Deploy to Market</button>
            </form>
        </div>

        <div class="glass-card">
            <h3 style="margin-top:0; opacity:0.8;"><i class="fas fa-boxes" style="color:var(--emerald);"></i> Inventory Preview</h3>
            <table style="width:100%; border-collapse: collapse;">
                <thead>
                    <tr style="text-align:left; color:var(--emerald); font-size:0.8rem; text-transform:uppercase;">
                        <th>Visual</th>
                        <th>Product</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $res = mysqli_query($conn, "SELECT * FROM products WHERE vendor_id = '$v_id' ORDER BY id DESC LIMIT 5");
                    while ($row = mysqli_fetch_assoc($res)) : ?>
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <td style="padding:15px;"><img src="../assets/images/<?php echo $row['image']; ?>" style="width:45px; height:45px; border-radius:10px; object-fit:cover;"></td>
                        <td style="padding:15px;">
                            <strong><?php echo htmlspecialchars($row['product_name']); ?></strong><br>
                            <small style="opacity:0.6;">₹<?php echo number_format($row['price']); ?></small>
                        </td>
                        <td style="padding:15px;">
                            <span style="color:<?php echo ($row['status'] == 'In Stock') ? 'var(--emerald)' : 'var(--ruby)'; ?>; font-weight:bold; font-size:0.8rem;">
                                <?php echo $row['status'] ?: 'Pending'; ?>
                            </span>
                        </td>
                        <td style="padding:15px;">
                            <a href="edit_product.php?id=<?php echo $row['id']; ?>" class="nav-btn" style="font-size:0.7rem; margin:0; padding:8px 12px;">Sync Status</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>