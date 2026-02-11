<?php
session_start();
require_once '../includes/config.php';

// Security Protocol: Vendor Gatekeeper
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'vendor') {
    header("Location: login.php");
    exit;
}

if (isset($_GET['id'])) {
    $p_id = (int)$_GET['id'];
    $v_id = $_SESSION['vendor_id'];

    // --- 🛠️ BINARY UPDATE PROTOCOL ---
    if (isset($_POST['sync_now'])) {
        // Casting to integer to match your 1/0 requirement
        $new_status = (int)$_POST['status']; 
        
        // Target specifically by ID and Vendor ID for security
        $sql = "UPDATE products SET status = '$new_status' WHERE id = '$p_id' AND vendor_id = '$v_id'";
        
        if (mysqli_query($conn, $sql)) {
            // Redirect with binary-specific success message
            header("Location: add_item.php?msg=Binary_Sync_Complete");
            exit;
        }
    }

    // Fetch product details for the interface
    $query = mysqli_query($conn, "SELECT * FROM products WHERE id = '$p_id' AND vendor_id = '$v_id'");
    $product = mysqli_fetch_assoc($query);
    
    // Safety check if product doesn't exist
    if (!$product) {
        header("Location: add_item.php");
        exit;
    }
} else {
    header("Location: add_item.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Binary Sync | Vendor Hub</title>
    <style>
        :root { --glass: rgba(255, 255, 255, 0.05); --emerald: #10b981; --ruby: #f87171; }
        body { background: #020617; color: white; font-family: 'Segoe UI', sans-serif; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; }
        .edit-box { background: var(--glass); backdrop-filter: blur(25px); padding: 40px; border-radius: 30px; border: 1px solid rgba(255,255,255,0.1); text-align: center; width: 380px; box-shadow: 0 25px 50px rgba(0,0,0,0.3); }
        select, button { width: 100%; padding: 15px; margin: 15px 0; border-radius: 12px; border: none; font-weight: bold; font-family: inherit; }
        select { background: #1e293b; color: white; border: 1px solid rgba(255,255,255,0.1); outline: none; }
        button { background: var(--emerald); color: #064e3b; cursor: pointer; text-transform: uppercase; transition: 0.3s; }
        button:hover { filter: brightness(1.1); transform: translateY(-2px); }
        .cancel-link { color: #64748b; text-decoration: none; font-size: 0.85rem; transition: 0.3s; }
        .cancel-link:hover { color: var(--ruby); }
    </style>
</head>
<body>
    <div class="edit-box">
        <h3 style="margin-top:0;">Update Registry</h3>
        <p style="opacity:0.6; font-size:0.9rem;">Protocol: <?php echo htmlspecialchars($product['product_name']); ?></p>
        
        <form method="POST">
            <select name="status">
                <option value="1" <?php if($product['status'] == '1') echo 'selected'; ?>>✅ Available (1)</option>
                <option value="0" <?php if($product['status'] == '0') echo 'selected'; ?>>❌ Not Available (0)</option>
            </select>
            
            <button type="submit" name="sync_now">Update Registry</button>
        </form>
        
        <a href="add_item.php" class="cancel-link">Cancel and Return</a>
    </div>
</body>
</html>