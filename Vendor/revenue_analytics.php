<?php
session_start();
require_once '../includes/config.php';

// Security: Access restricted to authorized Vendors
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'vendor') {
    header("Location: login.php");
    exit;
}

$v_id = $_SESSION['vendor_id'];

// 1. Data Logic: Monthly Revenue Stream
$flow_query = mysqli_query($conn, "
    SELECT MONTHNAME(c.created_at) as month, SUM(p.price * c.quantity) as total 
    FROM cart c JOIN products p ON c.product_id = p.id 
    WHERE p.vendor_id = $v_id AND c.status = 'Ordered'
    GROUP BY MONTH(c.created_at) ORDER BY MONTH(c.created_at) ASC
");
$months = []; $totals = [];
while($row = mysqli_fetch_assoc($flow_query)) { $months[] = $row['month']; $totals[] = $row['total']; }

// 2. Data Logic: Top Client Registry (Using 'name' column)
$client_query = mysqli_query($conn, "
    SELECT u.name, SUM(p.price * c.quantity) as spent 
    FROM cart c 
    JOIN registrations u ON c.user_id = u.id 
    JOIN products p ON c.product_id = p.id 
    WHERE p.vendor_id = $v_id AND c.status = 'Ordered' 
    GROUP BY u.id ORDER BY spent DESC LIMIT 5
");

// 3. Data Logic: Service Popularity Matrix
$service_query = mysqli_query($conn, "
    SELECT p.product_name, COUNT(c.id) as orders 
    FROM cart c JOIN products p ON c.product_id = p.id 
    WHERE p.vendor_id = $v_id GROUP BY p.id
");
$p_labels = []; $p_data = [];
while($row = mysqli_fetch_assoc($service_query)) { $p_labels[] = $row['product_name']; $p_data[] = $row['orders']; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Revenue Intelligence | Vendor Terminal</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --glass: rgba(255, 255, 255, 0.05); --emerald: #10b981; --text: #ffffff; }
        body { 
            margin: 0; 
            background: radial-gradient(at 0% 100%, #064e3b 0, transparent 50%), #020617;
            font-family: 'Segoe UI', sans-serif; 
            color: var(--text); 
            padding: 40px; 
        }
        
        .analytics-grid { display: grid; grid-template-columns: 2.5fr 1fr; gap: 30px; }
        .glass-canvas { 
            background: var(--glass); 
            backdrop-filter: blur(40px); 
            padding: 35px; 
            border-radius: 40px; 
            border: 1px solid rgba(255,255,255,0.1);
            box-shadow: 0 25px 50px rgba(0,0,0,0.5);
        }
        
        .stat-node { text-align: center; padding: 20px; border-radius: 20px; background: rgba(255,255,255,0.02); }
        .btn-export { background: var(--emerald); color: #064e3b; padding: 12px 30px; border-radius: 50px; border: none; font-weight: 900; cursor: pointer; transition: 0.3s; }
        .btn-export:hover { transform: scale(1.05); box-shadow: 0 0 20px var(--emerald); }
    </style>
</head>
<body>

    <header style="display:flex; justify-content:space-between; align-items:center; margin-bottom:50px;">
        <h2 style="letter-spacing:4px; font-weight:900;"><i class="fas fa-chart-line" style="color:var(--emerald);"></i> FINANCIAL COMMAND</h2>
        <button class="btn-export" onclick="exportReport()"><i class="fas fa-file-pdf"></i> GENERATE PROTOCOL</button>
    </header>

    <div id="capture-zone">
        <div class="analytics-grid">
            <div class="glass-canvas">
                <h3 style="margin-top:0;">Revenue Stream Flow</h3>
                <div style="height:350px;"><canvas id="revenueFlow"></canvas></div>
            </div>

            <div class="glass-canvas">
                <h3 style="margin-top:0;">Service Matrix</h3>
                <div style="height:300px;"><canvas id="serviceMatrix"></canvas></div>
            </div>
        </div>

        <div class="analytics-grid" style="margin-top:30px;">
            <div class="glass-canvas">
                <h3 style="margin-top:0;">Top Client Registry</h3>
                <table style="width:100%; border-collapse:collapse; margin-top:20px;">
                    <?php while($client = mysqli_fetch_assoc($client_query)): ?>
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <td style="padding:15px; font-weight:bold;"><?php echo htmlspecialchars($client['name']); ?></td>
                        <td style="text-align:right; color:var(--emerald); font-weight:900;">₹<?php echo number_format($client['spent']); ?></td>
                    </tr>
                    <?php endwhile; ?>
                </table>
            </div>

            <div class="glass-canvas" style="display:flex; flex-direction:column; justify-content:space-around;">
                <div class="stat-node">
                    <p style="opacity:0.5; font-size:0.7rem; text-transform:uppercase;">Monthly Velocity</p>
                    <div style="font-size:2rem; font-weight:900; color:var(--emerald);">+<?php echo count($p_data); ?> Nodes</div>
                </div>
                <div class="stat-node">
                    <p style="opacity:0.5; font-size:0.7rem; text-transform:uppercase;">Protocol Status</p>
                    <div style="font-size:1.2rem; font-weight:900;">ACTIVE SYNC</div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function exportReport() {
            const element = document.getElementById('capture-zone');
            html2pdf().from(element).save('Vendor_Intelligence_Report.pdf');
        }

        // Revenue Flow (Line Chart)
        const flowCtx = document.getElementById('revenueFlow').getContext('2d');
        const grad = flowCtx.createLinearGradient(0, 0, 0, 400);
        grad.addColorStop(0, 'rgba(16, 185, 129, 0.4)');
        grad.addColorStop(1, 'rgba(16, 185, 129, 0)');

        new Chart(flowCtx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($months); ?>,
                datasets: [{
                    label: 'Revenue (₹)',
                    data: <?php echo json_encode($totals); ?>,
                    borderColor: '#10b981',
                    backgroundColor: grad,
                    fill: true,
                    tension: 0.4,
                    borderWidth: 4,
                    pointRadius: 6,
                    pointBackgroundColor: '#ffffff'
                }]
            },
            options: { maintainAspectRatio: false, plugins: { legend: { display: false } } }
        });

        // Service Matrix (Doughnut)
        new Chart(document.getElementById('serviceMatrix'), {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode($p_labels); ?>,
                datasets: [{
                    data: <?php echo json_encode($p_data); ?>,
                    backgroundColor: ['#10b981', '#34d399', '#059669', '#115e59'],
                    borderWidth: 0
                }]
            },
            options: { maintainAspectRatio: false, cutout: '70%' }
        });
    </script>
</body>
</html>