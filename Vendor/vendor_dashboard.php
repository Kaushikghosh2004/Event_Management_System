<?php
session_start();
require_once '../includes/config.php';

// Security: Access restricted to authorized Vendors
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'vendor') {
    header("Location: login.php");
    exit;
}

$v_id = $_SESSION['vendor_id'];

// --- 📊 ANALYTICS LOGIC ---
// 1. Revenue Flow (Monthly)
$flow_query = mysqli_query($conn, "SELECT MONTHNAME(c.created_at) as month, SUM(p.price * c.quantity) as total 
    FROM cart c JOIN products p ON c.product_id = p.id 
    WHERE p.vendor_id = $v_id AND c.status = 'Ordered' 
    GROUP BY MONTH(c.created_at) ORDER BY MONTH(c.created_at) ASC");
$months = []; $totals = [];
while($f = mysqli_fetch_assoc($flow_query)) { $months[] = $f['month']; $totals[] = $f['total']; }

// 2. Annual Target Node (₹1L Goal)
$yearly_total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(p.price * c.quantity) as total 
    FROM cart c JOIN products p ON c.product_id = p.id 
    WHERE p.vendor_id = $v_id AND YEAR(c.created_at) = YEAR(CURRENT_DATE) AND c.status = 'Ordered'"))['total'] ?? 0;
$goal_progress = min(($yearly_total / 100000) * 100, 100);

// --- 📋 DATA TABLES LOGIC ---
// 3. Attendee Registry
$attendees = mysqli_query($conn, "SELECT r.*, e.event_name FROM registrations r 
    JOIN events e ON r.event_id = e.id WHERE e.vendor_id = '$v_id' ORDER BY r.id DESC LIMIT 5");

// 4. Product Order Status
$orders = mysqli_query($conn, "SELECT c.id, c.status, p.product_name, u.name 
    FROM cart c JOIN products p ON c.product_id = p.id JOIN registrations u ON c.user_id = u.id 
    WHERE p.vendor_id = $v_id AND c.status != 'In Cart' ORDER BY c.id DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Vendor Intelligence | Command Center</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root { --glass: rgba(255, 255, 255, 0.05); --emerald: #10b981; --text: #ffffff; }
        body { margin: 0; background: #020617; font-family: 'Segoe UI', sans-serif; color: var(--text); display: flex; min-height: 100vh;
            background: radial-gradient(at 0% 100%, #064e3b 0, transparent 50%), #020617; }

        /* 🟢 Sidebar Registry */
        .sidebar { width: 280px; background: var(--glass); backdrop-filter: blur(20px); border-right: 1px solid rgba(255,255,255,0.1); padding: 40px 20px; display: flex; flex-direction: column; gap: 10px; }
        .nav-link { padding: 15px 20px; border-radius: 15px; text-decoration: none; color: white; display: flex; align-items: center; gap: 15px; transition: 0.3s; }
        .nav-link.active { background: var(--emerald); color: #064e3b; font-weight: 800; box-shadow: 0 10px 20px rgba(16,185,129,0.3); }

        .dashboard-main { flex: 1; padding: 40px; overflow-y: auto; }
        .glass-card { background: var(--glass); backdrop-filter: blur(30px); border-radius: 30px; border: 1px solid rgba(255,255,255,0.1); padding: 25px; margin-bottom: 25px; }
        
        /* Tactical Grid Layout */
        .stat-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-bottom: 30px; }
        .table-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }

        .progress-ring { position: relative; width: 80px; height: 80px; margin: 0 auto; }
        .ring-fill { fill: none; stroke: var(--emerald); stroke-width: 8; stroke-dasharray: <?php echo ($goal_progress * 2.5); ?>, 250; transform: rotate(-90deg); transform-origin: center; transition: 1s; }

        table { width: 100%; border-collapse: collapse; font-size: 0.85rem; }
        th { text-align: left; opacity: 0.5; text-transform: uppercase; padding: 10px; }
        td { padding: 12px 10px; border-bottom: 1px solid rgba(255,255,255,0.05); }
        .status-pill { padding: 4px 10px; border-radius: 20px; font-size: 0.7rem; font-weight: bold; }
        .status-pill.ordered { background: rgba(16, 185, 129, 0.2); color: var(--emerald); }
    </style>
</head>
<body>

    <nav class="sidebar">
        <h3 style="letter-spacing: 2px; color: var(--emerald);">VENDOR COMMAND</h3>
        <a href="vendor_dashboard.php" class="nav-link active"><i class="fas fa-th-large"></i> Dashboard</a>
        <a href="Manage_events.php" class="nav-link"><i class="fas fa-calendar-alt"></i> Manage Events</a>
        <a href="product_status.php" class="nav-link"><i class="fas fa-truck-loading"></i> Order Updates</a>
        <a href="revenue_analytics.php" class="nav-link"><i class="fas fa-chart-bar"></i> Revenue Analytics</a>
        <a href="add_item.php" class="nav-link"><i class="fas fa-truck-loading"></i> Add Items</a>
        <a href="request_item.php" class="nav-link"><i class="fas fa-inbox"></i> Incoming Requests</a>
        <a href="../logout.php" class="nav-link" style="margin-top:auto; color:#f87171;"><i class="fas fa-power-off"></i> Logout</a>
    </nav>

    <main class="dashboard-main">
        <header style="margin-bottom:40px;">
            <h1 style="margin:0; letter-spacing:2px;">OPERATIONAL INTELLIGENCE</h1>
            <p style="opacity:0.5;">Live Protocol: <?php echo date('F d, Y'); ?></p>
        </header>

        <div class="stat-grid">
            <div class="glass-card">
                <h3 style="margin-top:0;"><i class="fas fa-chart-line" style="color:var(--emerald);"></i> Revenue Flow Protocol</h3>
                <div style="height:250px;"><canvas id="revenueFlow"></canvas></div>
            </div>

            <div class="glass-card" style="text-align:center;">
                <h3>Annual Target</h3>
                <div class="progress-ring">
                    <svg width="80" height="80"><circle class="ring-fill" cx="40" cy="40" r="35"/></svg>
                    <span style="position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); font-weight:900;"><?php echo round($goal_progress); ?>%</span>
                </div>
                <p style="font-weight:bold; margin-top:15px;">₹<?php echo number_format($yearly_total); ?> / ₹1L</p>
                <p style="opacity:0.5; font-size:0.75rem;">Verified Financial Path</p>
            </div>
        </div>

        <div class="table-grid">
            <div class="glass-card">
                <h3><i class="fas fa-users"></i> Recent Attendees</h3>
                <table>
                    <thead><tr><th>Name</th><th>Event</th></tr></thead>
                    <tbody>
                        <?php while($a = mysqli_fetch_assoc($attendees)): ?>
                        <tr><td><?php echo htmlspecialchars($a['name']); ?></td><td><?php echo htmlspecialchars($a['event_name']); ?></td></tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <div class="glass-card">
                <h3><i class="fas fa-shipping-fast"></i> Order Status Updates</h3>
                <table>
                    <thead><tr><th>Product</th><th>Status</th></tr></thead>
                    <tbody>
                        <?php while($o = mysqli_fetch_assoc($orders)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($o['product_name']); ?></td>
                            <td><span class="status-pill <?php echo strtolower($o['status']); ?>"><?php echo $o['status']; ?></span></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <script>
        const ctx = document.getElementById('revenueFlow').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($months); ?>,
                datasets: [{
                    label: 'Revenue (₹)',
                    data: <?php echo json_encode($totals); ?>,
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.2)',
                    fill: true, tension: 0.4, borderWidth: 4
                }]
            },
            options: { maintainAspectRatio: false, plugins: { legend: { display: false } }, 
            scales: { y: { grid: { color: 'rgba(255,255,255,0.05)' } }, x: { grid: { display: false } } } }
        });
    </script>
</body>
</html>