<?php
session_start();
// Connectivity Protocol
require_once '../includes/config.php';

// Security Protocol: Restrict access to authenticated Users
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit;
}

$u_id = $_SESSION['user_id'];

// --- 🔍 UPDATED REGISTRY QUERY ---
// Logic: Corrected 'full_name' to 'name' to match your database structure
$status_query = mysqli_query($conn, "SELECT c.status, u.name, u.email, u.txn_id as address 
                                     FROM cart c 
                                     JOIN registrations u ON c.user_id = u.id 
                                     WHERE c.user_id = $u_id AND c.status != 'In Cart'
                                     ORDER BY c.id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Status | Event Management System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --glass: rgba(255, 255, 255, 0.08);
            --glass-border: rgba(255, 255, 255, 0.15);
            --accent: #60a5fa; 
            --emerald: #10b981;
            --ruby: #f87171;
            --text: #ffffff;
        }

        body {
            margin: 0; min-height: 100vh; display: flex; flex-direction: column; align-items: center;
            background: radial-gradient(circle at top right, #1e3a8a, #020617);
            font-family: 'Segoe UI', sans-serif; color: var(--text); padding-bottom: 50px;
        }

        /* 1. Upper Floating Title */
        .header-title {
            margin: 40px 0; background: var(--glass); backdrop-filter: blur(15px);
            padding: 15px 60px; border-radius: 50px; border: 1px solid var(--glass-border);
            font-size: 1.2rem; font-weight: 800; letter-spacing: 3px; text-transform: uppercase;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        }

        /* 2. 🔮 Main Glass Canvas */
        .status-canvas {
            background: var(--glass); backdrop-filter: blur(30px); width: 1000px;
            padding: 45px; border-radius: 35px; border: 1px solid var(--glass-border);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.4); animation: fadeIn 0.8s ease-out;
        }

        @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

        .status-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; text-align: center; }

        .header-tile {
            background: rgba(255, 255, 255, 0.05); padding: 15px; border-radius: 15px;
            border: 1px solid var(--glass-border); font-weight: 800; text-transform: uppercase;
            letter-spacing: 1px; font-size: 0.8rem; color: var(--accent);
        }

        .data-tile {
            background: rgba(255, 255, 255, 0.03); border: 1px solid var(--glass-border);
            padding: 20px 15px; border-radius: 15px; font-size: 0.95rem; min-height: 60px;
            display: flex; align-items: center; justify-content: center; transition: 0.3s;
        }

        /* 🟢 Dynamic Status Node Styling */
        .status-tile { font-weight: 900; text-transform: uppercase; font-size: 0.8rem; }
        .status-ordered { color: var(--emerald); text-shadow: 0 0 10px rgba(16, 185, 129, 0.3); }
        .status-pending { color: var(--accent); }

        .data-tile:hover { background: rgba(255, 255, 255, 0.08); transform: scale(1.02); border-color: var(--accent); }
    </style>
</head>
<body>

    <div class="header-title">Event Management System</div>

    <div class="status-canvas">
        <div style="display: flex; justify-content: space-between; margin-bottom: 40px; align-items: center;">
            <h2 style="margin:0; opacity:0.8;"><i class="fas fa-radar" style="color:var(--accent); margin-right:10px;"></i> Order Registry</h2>
            <div style="display:flex; gap:15px;">
                <a href="dashboard.php" style="text-decoration:none; color:white; background:var(--glass); padding:10px 25px; border-radius:12px; border:1px solid var(--glass-border); font-weight:bold;">
                    <i class="fas fa-th-large"></i> Dashboard
                </a>
                <a href="../logout.php" style="text-decoration:none; color:var(--ruby); background:var(--glass); padding:10px 25px; border-radius:12px; border:1px solid var(--glass-border); font-weight:bold;">
                    <i class="fas fa-power-off"></i> Logout
                </a>
            </div>
        </div>

        <div class="status-grid">
            <div class="header-tile">Identity Node</div>
            <div class="header-tile">Email Hub</div>
            <div class="header-tile">Registry Txn</div>
            <div class="header-tile">Clearance Status</div>

            <?php if(mysqli_num_rows($status_query) > 0): ?>
                <?php while($row = mysqli_fetch_assoc($status_query)): ?>
                    <div class="data-tile"><?php echo htmlspecialchars($row['name']); ?></div>
                    <div class="data-tile" style="opacity:0.6; font-size:0.8rem;"><?php echo htmlspecialchars($row['email']); ?></div>
                    <div class="data-tile" style="opacity:0.6; font-family: monospace;"><?php echo htmlspecialchars($row['address']); ?></div>
                    <div class="data-tile status-tile <?php echo ($row['status'] == 'Ordered') ? 'status-ordered' : 'status-pending'; ?>">
                        <?php echo htmlspecialchars($row['status']); ?>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div style="grid-column: 1 / -1; padding: 40px; opacity: 0.4;">
                    <i class="fas fa-box-open" style="font-size: 3rem; margin-bottom: 15px;"></i>
                    <p>No active service requests detected in the registry.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>