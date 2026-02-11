<?php
session_start();
require_once '../includes/config.php';

// Security: User Session Verification
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit;
}

// Logic: Categorized filtering for targeted discovery
$category = isset($_GET['cat']) ? mysqli_real_escape_string($conn, $_GET['cat']) : 'Florist';

// Fetch vendors belonging to this specific node
$vendor_query = "SELECT * FROM vendors WHERE category = '$category'";
$vendor_result = mysqli_query($conn, $vendor_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Discover | <?php echo $category; ?> Registry</title>
    <link rel="stylesheet" href="../assets/style.css">
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
            margin: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            background: radial-gradient(circle at top left, #1e3a8a, #020617);
            font-family: 'Segoe UI', sans-serif;
            color: var(--text);
            padding-bottom: 50px;
        }

        /* Upper Floating Title Bar */
        .header-title {
            margin: 40px 0;
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
        }

        /* 🔮 The Main Discovery Canvas */
        .vendor-canvas {
            background: var(--glass);
            backdrop-filter: blur(30px);
            -webkit-backdrop-filter: blur(30px);
            width: 1100px;
            padding: 45px;
            border-radius: 35px;
            border: 1px solid var(--glass-border);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.4);
            animation: fadeIn 0.8s ease-out;
        }

        @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

        /* Discovery Nav Header */
        .nav-header { display: flex; justify-content: space-between; margin-bottom: 50px; }

        /* Category Indicator Banner */
        .category-node {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--glass-border);
            width: fit-content;
            margin: 0 auto 50px auto;
            display: flex;
            align-items: center;
            gap: 20px;
            padding: 15px 40px;
            border-radius: 20px;
        }
        .category-node span:first-child { opacity: 0.5; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 2px; }
        .category-node span:last-child { font-weight: 800; font-size: 1.5rem; color: var(--accent); }

        /* Interactive Vendor Pods */
        .vendor-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
        }

        .vendor-pod {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--glass-border);
            padding: 40px 20px;
            border-radius: 30px;
            text-align: center;
            transition: 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 20px;
        }

        .vendor-pod:hover {
            background: var(--glass);
            transform: translateY(-12px);
            border-color: var(--accent);
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
        }

        .pod-icon {
            width: 70px; height: 70px;
            background: var(--accent);
            color: #1e3a8a;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            margin-bottom: 10px;
        }

        .vendor-pod h3 { margin: 0; font-size: 1.5rem; letter-spacing: 1px; }

        .btn-discover {
            background: var(--text);
            color: #1e3a8a;
            padding: 12px 35px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 800;
            text-transform: uppercase;
            font-size: 0.85rem;
            transition: 0.3s;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn-discover:hover {
            background: var(--accent);
            color: white;
            box-shadow: 0 8px 20px rgba(96, 165, 250, 0.4);
        }
    </style>
</head>
<body>

    <div class="header-title">Event Management System</div>

    <div class="vendor-canvas">
        <div class="nav-header">
            <h2 style="margin:0; opacity:0.8;"><i class="fas fa-search-location" style="color:var(--accent); margin-right:10px;"></i> Discovery Hub</h2>
            <div style="display:flex; gap:15px;">
                <a href="dashboard.php" class="btn-glass-nav" style="text-decoration:none; color:white; background:var(--glass); padding:10px 25px; border-radius:12px; border:1px solid var(--glass-border); font-weight:bold;">
                    <i class="fas fa-home"></i> Home
                </a>
                <a href="../logout.php" class="btn-glass-nav" style="text-decoration:none; color:#f87171; background:var(--glass); padding:10px 25px; border-radius:12px; border:1px solid var(--glass-border); font-weight:bold;">
                    <i class="fas fa-power-off"></i> Logout
                </a>
            </div>
        </div>

        <div class="category-node">
            <span>Filtering By Category</span>
            <span><?php echo htmlspecialchars($category); ?></span>
        </div>

        <div class="vendor-grid">
            <?php if(mysqli_num_rows($vendor_result) > 0): ?>
                <?php while($vendor = mysqli_fetch_assoc($vendor_result)): ?>
                    <div class="vendor-pod">
                        <div class="pod-icon">
                            <i class="fas fa-id-badge"></i>
                        </div>
                        <h3><?php echo htmlspecialchars($vendor['username']); ?></h3>
                        <p style="opacity:0.6; font-size:0.9rem;">Verified Service Partner</p>
                        
                        <a href="products.php?vendor_id=<?php echo $vendor['id']; ?>" class="btn-discover">
                            Explore Catalog <i class="fas fa-chevron-right"></i>
                        </a>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div style="grid-column: 1/-1; text-align:center; padding:100px; opacity:0.4;">
                    <i class="fas fa-box-open" style="font-size:3rem; margin-bottom:20px;"></i>
                    <p>No verified vendors currently registered in this domain.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>