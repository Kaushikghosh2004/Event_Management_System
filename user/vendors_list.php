<?php
session_start();
require_once '../includes/config.php';

// Security: User Session Verification
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit;
}

// Fetch approved vendors from the registry
$vendors_query = mysqli_query($conn, "SELECT id, user_name, store_name, email FROM vendors WHERE status = 'Approved'");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Service Providers | Event Management System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --glass: rgba(255, 255, 255, 0.05);
            --ocean: #3b82f6;
            --text: #ffffff;
        }

        body {
            margin: 0; min-height: 100vh;
            background: radial-gradient(at 0% 0%, #0f172a 0, transparent 50%), #020617;
            font-family: 'Segoe UI', sans-serif; color: var(--text); padding: 40px;
        }

        .header-area { display: flex; justify-content: space-between; align-items: center; margin-bottom: 50px; }

        .vendor-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
        }

        /* 📋 Vendor Node Styling */
        .vendor-card {
            background: var(--glass);
            backdrop-filter: blur(20px);
            padding: 35px;
            border-radius: 30px;
            border: 1px solid rgba(255,255,255,0.1);
            text-align: center;
            transition: 0.3s ease;
        }

        .vendor-card:hover {
            transform: translateY(-10px);
            border-color: var(--ocean);
            box-shadow: 0 15px 30px rgba(59, 130, 246, 0.2);
        }

        .vendor-icon {
            font-size: 3rem;
            color: var(--ocean);
            margin-bottom: 20px;
        }

        .btn-view {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 25px;
            background: var(--ocean);
            color: #0f172a;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 800;
            text-transform: uppercase;
            font-size: 0.75rem;
        }
    </style>
</head>
<body>

    <div class="header-area">
        <h2><i class="fas fa-handshake" style="color:var(--ocean);"></i> SERVICE PROVIDERS</h2>
        <a href="user_dashboard.php" style="color:white; text-decoration:none; opacity:0.6; font-weight:bold;">
            <i class="fas fa-arrow-left"></i> Dashboard
        </a>
    </div>

    <div class="vendor-grid">
        <?php if(mysqli_num_rows($vendors_query) > 0): ?>
            <?php while($vendor = mysqli_fetch_assoc($vendors_query)): ?>
                <div class="vendor-card">
                    <i class="fas fa-store vendor-icon"></i>
                    <h3 style="margin:0;"><?php echo htmlspecialchars($vendor['store_name'] ?: $vendor['user_name']); ?></h3>
                    <p style="opacity:0.5; font-size:0.85rem; margin:10px 0;"><?php echo htmlspecialchars($vendor['email']); ?></p>
                    <a href="vendor_profile.php?id=<?php echo $vendor['id']; ?>" class="btn-view">View Services</a>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div style="grid-column: 1 / -1; text-align:center; padding:100px; opacity:0.3;">
                <i class="fas fa-user-slash" style="font-size:4rem; margin-bottom:20px;"></i>
                <p>No authorized vendors found in the registry.</p>
            </div>
        <?php endif; ?>
    </div>

</body>
</html>