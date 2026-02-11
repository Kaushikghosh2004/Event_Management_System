<?php
session_start();
require_once '../includes/config.php';

// Security: Access restricted to authorized Vendors
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'vendor') {
    header("Location: login.php");
    exit;
}

$v_id = $_SESSION['vendor_id'];

// THE VERIFIED REQUEST QUERY: Using column 'name' from registrations table
$request_query = mysqli_query($conn, "SELECT c.*, p.product_name, u.name as user_display 
                                      FROM cart c 
                                      JOIN products p ON c.product_id = p.id 
                                      JOIN registrations u ON c.user_id = u.id 
                                      WHERE p.vendor_id = $v_id AND c.status = 'Ordered'");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Request Center | Vendor Command</title>
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
            /* Emerald-to-Midnight Mesh Gradient */
            background: radial-gradient(at 0% 100%, #064e3b 0, transparent 50%),
                        radial-gradient(at 100% 0%, #065f46 0, transparent 50%),
                        #020617;
            font-family: 'Segoe UI', sans-serif;
            color: var(--text);
            padding: 40px;
        }

        /* Floating Command Header */
        .header-title {
            background: var(--glass);
            backdrop-filter: blur(20px);
            padding: 15px 60px;
            border-radius: 50px;
            border: 1px solid var(--glass-border);
            font-size: 1.1rem;
            font-weight: 900;
            letter-spacing: 3px;
            text-transform: uppercase;
            box-shadow: 0 10px 40px rgba(0,0,0,0.5);
            margin-bottom: 50px;
        }

        /* 🔮 The Glass Request Canvas */
        .request-canvas {
            background: var(--glass);
            backdrop-filter: blur(40px);
            -webkit-backdrop-filter: blur(40px);
            width: 1100px;
            padding: 45px;
            border-radius: 35px;
            border: 1px solid var(--glass-border);
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.4);
            animation: fadeIn 0.8s ease-out;
        }

        @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

        .nav-hub { display: flex; justify-content: space-between; align-items: center; margin-bottom: 50px; }

        /* Request Pod Grid */
        .items-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 25px;
        }

        .request-pod {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--glass-border);
            padding: 35px 20px;
            border-radius: 30px;
            text-align: center;
            transition: 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .request-pod:hover {
            background: var(--glass);
            transform: translateY(-10px);
            border-color: var(--emerald);
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
        }

        .pod-icon { font-size: 2.5rem; color: var(--emerald); margin-bottom: 10px; }

        .product-name { font-size: 1.2rem; font-weight: 800; letter-spacing: 1px; }

        .user-tag {
            font-size: 0.75rem;
            background: rgba(16, 185, 129, 0.1);
            color: var(--emerald);
            padding: 8px 15px;
            border-radius: 12px;
            display: inline-block;
            font-weight: bold;
            text-transform: uppercase;
        }

        .btn-glass-nav {
            background: var(--glass);
            border: 1px solid var(--glass-border);
            padding: 12px 30px;
            border-radius: 50px;
            color: white;
            text-decoration: none;
            font-weight: bold;
            transition: 0.3s;
        }
        .btn-glass-nav:hover { background: rgba(255,255,255,0.1); border-color: var(--emerald); }
    </style>
</head>
<body>

    <div class="header-title">Event Management System</div>

    <div class="request-canvas">
        <div class="nav-hub">
            <h2 style="margin:0;"><i class="fas fa-inbox" style="color:var(--emerald); margin-right:15px;"></i> Active Requests</h2>
            <div style="display:flex; gap:15px;">
                <a href="vendor_dashboard.php" class="btn-glass-nav"><i class="fas fa-home"></i> Home</a>
                <a href="request_item.php" class="btn-glass-nav" style="background:var(--emerald); color:#064e3b; border-color:var(--emerald);"><i class="fas fa-sync"></i> Refresh Hub</a>
                <a href="../logout.php" class="btn-glass-nav" style="color:#f87171;"><i class="fas fa-power-off"></i> Logout</a>
            </div>
        </div>

        <div class="items-grid">
            <?php if(mysqli_num_rows($request_query) > 0): ?>
                <?php while($req = mysqli_fetch_assoc($request_query)): ?>
                    <div class="request-pod">
                        <div class="pod-icon"><i class="fas fa-shopping-basket"></i></div>
                        <div class="product-name"><?php echo htmlspecialchars($req['product_name']); ?></div>
                        <div class="user-tag">From: <?php echo htmlspecialchars($req['user_display']); ?></div>
                        <div style="font-size: 0.85rem; opacity: 0.6;">
                            <i class="fas fa-layer-group"></i> Allocation: <?php echo $req['quantity']; ?> Units
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div style="grid-column: 1/-1; text-align:center; padding:80px; opacity:0.3;">
                    <i class="fas fa-wind" style="font-size:4rem; margin-bottom:20px;"></i>
                    <p style="font-size: 1.2rem; font-weight: bold;">No active service requests detected in the registry.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>