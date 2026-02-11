<?php
session_start();
// Security: Access restricted to Admin only
if (!isset($_SESSION['admin_logged_in'])) { header("Location: login.php"); exit; }
require_once '../includes/config.php';

// Fetch all vendors from the system
$vendor_query = "SELECT * FROM vendors ORDER BY id DESC";
$vendors = mysqli_query($conn, $vendor_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Vendor Manage | Admin Command</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --glass: rgba(255, 255, 255, 0.08);
            --glass-border: rgba(255, 255, 255, 0.15);
            --emerald: #10b981;
            --accent: #60a5fa;
            --text: #ffffff;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            /* Emerald-to-Midnight Mesh Gradient for Vendor Domain */
            background: radial-gradient(at 0% 100%, #064e3b 0, transparent 50%),
                        radial-gradient(at 100% 0%, #065f46 0, transparent 50%),
                        #020617;
            font-family: 'Segoe UI', system-ui, sans-serif;
            color: var(--text);
            padding: 40px;
        }

        /* Upper Part: System Title */
        .header-title {
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
            margin-bottom: 50px;
        }

        /* 🔮 The Main Glass Canvas */
        .glass-canvas {
            background: var(--glass);
            backdrop-filter: blur(30px);
            -webkit-backdrop-filter: blur(30px);
            width: 1100px;
            padding: 40px;
            border-radius: 35px;
            border: 1px solid var(--glass-border);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.5);
            animation: slideUp 0.7s ease-out;
        }

        @keyframes slideUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }

        .nav-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px; }
        
        .btn-action {
            background: var(--glass);
            border: 1px solid var(--glass-border);
            padding: 10px 25px;
            border-radius: 12px;
            color: white;
            text-decoration: none;
            font-weight: bold;
            transition: 0.3s;
        }
        .btn-action:hover { background: rgba(255,255,255,0.15); transform: scale(1.05); }

        /* Powerful Interactive Table */
        .table-container {
            background: rgba(255, 255, 255, 0.03);
            border-radius: 20px;
            padding: 15px;
            border: 1px solid var(--glass-border);
        }

        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 20px; border-bottom: 1px solid var(--glass-border); color: var(--emerald); font-weight: 800; text-transform: uppercase; font-size: 0.85rem; }
        td { padding: 20px; border-bottom: 1px solid rgba(255,255,255,0.05); font-size: 1rem; }
        
        tr:hover td { background: rgba(16, 185, 129, 0.05); color: var(--emerald); }

        /* Status Badges */
        .badge {
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-active { background: rgba(16, 185, 129, 0.2); color: #34d399; }
        .badge-pending { background: rgba(96, 165, 250, 0.2); color: #93c5fd; }

        .btn-manage {
            background: var(--emerald);
            color: #064e3b;
            padding: 8px 18px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 800;
            font-size: 0.85rem;
            transition: 0.3s;
        }
        .btn-manage:hover { background: #34d399; box-shadow: 0 0 15px var(--emerald); }
    </style>
</head>
<body>

    <div class="header-title">Event Management System</div>

    <div class="glass-canvas">
        <div class="nav-header">
            <h2 style="margin:0; opacity:0.8;">Vendor Registry Control</h2>
            <div style="display:flex; gap:15px;">
                <a href="dashboard.php" class="btn-action"><i class="fas fa-home"></i> Dashboard</a>
                <a href="maintain_vendors.php" class="btn-action"><i class="fas fa-tools"></i> Maintenance</a>
            </div>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Vendor Identity</th>
                        <th>Service Domain</th>
                        <th>Access Email</th>
                        <th>Clearance</th>
                        <th>Governance</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($v = mysqli_fetch_assoc($vendors)): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($v['username']); ?></strong></td>
                        <td><i class="fas fa-tags" style="font-size: 0.8rem; opacity: 0.6;"></i> <?php echo htmlspecialchars($v['category']); ?></td>
                        <td style="opacity: 0.7;"><?php echo htmlspecialchars($v['email']); ?></td>
                        <td><span class="badge badge-active">Active</span></td>
                        <td>
                            <a href="update_status.php?id=<?php echo $v['id']; ?>" class="btn-manage">Manage</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>