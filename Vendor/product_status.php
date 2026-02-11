<?php
session_start();
require_once '../includes/config.php';

// Security: Access restricted to authorized Vendors
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'vendor') {
    header("Location: login.php");
    exit;
}

$v_id = $_SESSION['vendor_id'];

// Handle Deletion Protocol
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM cart WHERE id = $id");
    header("Location: product_status.php?msg=Purged");
    exit;
}

// THE VERIFIED QUERY: Using column 'name' as seen in image_a822d8.jpg
$query = "SELECT c.id, c.status, u.name as display_name, u.email, u.txn_id as address
          FROM cart c
          JOIN registrations u ON c.user_id = u.id
          JOIN products p ON c.product_id = p.id
          WHERE p.vendor_id = $v_id AND c.status != 'In Cart'
          ORDER BY c.id DESC";

$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Operations Monitor | Vendor Command</title>
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

        .status-canvas {
            background: var(--glass);
            backdrop-filter: blur(40px);
            width: 1200px;
            padding: 45px;
            border-radius: 35px;
            border: 1px solid var(--glass-border);
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.4);
        }

        .status-grid {
            display: grid;
            grid-template-columns: 2fr 2fr 2fr 1.5fr 1fr 1fr;
            gap: 12px;
            text-align: center;
        }

        .header-tile {
            background: rgba(255, 255, 255, 0.08);
            padding: 18px;
            border-radius: 12px;
            font-weight: 800;
            color: var(--emerald);
            border: 1px solid var(--glass-border);
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 1px;
        }

        .data-tile {
            background: rgba(255, 255, 255, 0.03);
            padding: 20px 10px;
            border-radius: 12px;
            border: 1px solid var(--glass-border);
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: 0.3s;
        }

        .data-tile:hover { background: rgba(16, 185, 129, 0.05); border-color: var(--emerald); }

        .btn-gov { text-decoration: none; font-weight: 800; font-size: 0.75rem; transition: 0.3s; }
        .btn-update { color: #60a5fa; }
        .btn-purge { color: #f87171; }
    </style>
</head>
<body>

    <div style="margin-bottom: 40px; background: var(--glass); padding: 15px 50px; border-radius: 50px; border: 1px solid var(--glass-border);">
        <h2 style="margin:0; font-size: 1.1rem; letter-spacing: 3px; font-weight: 900;">EVENT MANAGEMENT SYSTEM</h2>
    </div>

    <div class="status-canvas">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:40px;">
            <h3 style="margin:0;"><i class="fas fa-microchip" style="color:var(--emerald);"></i> OPERATIONS MONITOR</h3>
            <div style="display:flex; gap:15px;">
                <a href="vendor_dashboard.php" style="color:white; text-decoration:none; opacity:0.6; font-weight:bold;">Home</a>
                <a href="../logout.php" style="color:#f87171; text-decoration:none; font-weight:bold;">Logout</a>
            </div>
        </div>

        <div class="status-grid">
            <div class="header-tile">Customer Identity</div>
            <div class="header-tile">Access Email</div>
            <div class="header-tile">TXN Registry</div>
            <div class="header-tile">Service Status</div>
            <div class="header-tile">Governance</div>
            <div class="header-tile">Purge</div>

            <?php if(mysqli_num_rows($result) > 0): ?>
                <?php while($row = mysqli_fetch_assoc($result)): ?>
                    <div class="data-tile"><strong><?php echo htmlspecialchars($row['display_name']); ?></strong></div>
                    <div class="data-tile" style="opacity:0.6;"><?php echo htmlspecialchars($row['email']); ?></div>
                    <div class="data-tile" style="opacity:0.6;"><?php echo htmlspecialchars($row['address']); ?></div>
                    <div class="data-tile" style="color:var(--emerald); font-weight:bold;"><?php echo $row['status']; ?></div>
                    <div class="data-tile">
                        <a href="update_order.php?id=<?php echo $row['id']; ?>" class="btn-gov btn-update">Update</a>
                    </div>
                    <div class="data-tile">
                        <a href="product_status.php?delete=<?php echo $row['id']; ?>" class="btn-gov btn-purge" onclick="return confirm('Purge protocol?')">Purge</a>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="data-tile" style="grid-column: span 6; padding:80px; opacity:0.4;">
                    No operational requests detected.
                </div>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>