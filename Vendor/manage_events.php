<?php
session_start();
require_once '../includes/config.php';

// Security: Access restricted to authorized Vendors
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'vendor') {
    header("Location: login.php");
    exit;
}

$v_id = $_SESSION['vendor_id']; // Current Vendor Identity

// Handle Deletion Protocol
if (isset($_GET['delete'])) {
    $e_id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM events WHERE id = $e_id AND vendor_id = $v_id");
    header("Location: manage_events.php?msg=Event_Purged");
}

// Fetch only events belonging to this vendor
$query = "SELECT * FROM events WHERE vendor_id = '$v_id' ORDER BY event_date ASC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Events | Vendor Command</title>
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
            /* Animated Mesh Gradient */
            background: linear-gradient(135deg, #020617 0%, #064e3b 50%, #020617 100%);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
            font-family: 'Segoe UI', sans-serif;
            color: var(--text);
            padding: 40px;
        }

        @keyframes gradientBG { 0% { background-position: 0% 50%; } 50% { background-position: 100% 50%; } 100% { background-position: 0% 50%; } }

        /* Floating Title */
        .header-title {
            background: var(--glass);
            backdrop-filter: blur(20px);
            padding: 15px 50px;
            border-radius: 50px;
            border: 1px solid var(--glass-border);
            font-size: 1.1rem;
            font-weight: 800;
            letter-spacing: 4px;
            text-transform: uppercase;
            box-shadow: 0 10px 40px rgba(0,0,0,0.5);
            margin-bottom: 50px;
        }

        /* 🔮 The Glass Canvas */
        .glass-canvas {
            background: var(--glass);
            backdrop-filter: blur(40px);
            width: 1000px;
            padding: 40px;
            border-radius: 35px;
            border: 1px solid var(--glass-border);
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.4);
            animation: fadeIn 0.8s ease-out;
        }

        @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

        .nav-hub { display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px; }

        /* Tactical Table */
        .glass-table { width: 100%; border-collapse: collapse; }
        .glass-table th { text-align: left; padding: 20px; color: var(--emerald); text-transform: uppercase; letter-spacing: 2px; font-size: 0.8rem; border-bottom: 1px solid var(--glass-border); }
        .glass-table td { padding: 20px; border-bottom: 1px solid rgba(255,255,255,0.05); }
        
        .glass-table tr:hover td { background: rgba(16, 185, 129, 0.05); color: var(--emerald); }

        /* Governance Buttons */
        .btn-gov {
            padding: 10px 20px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 800;
            font-size: 0.75rem;
            transition: 0.3s;
            border: 1px solid var(--glass-border);
            display: inline-block;
        }
        .btn-edit { color: var(--emerald); border-color: var(--emerald); }
        .btn-edit:hover { background: var(--emerald); color: #064e3b; }
        
        .btn-purge { color: #f87171; border-color: #f87171; }
        .btn-purge:hover { background: #f87171; color: white; }

        .btn-launch {
            background: var(--emerald);
            color: #064e3b;
            padding: 12px 25px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 900;
            box-shadow: 0 10px 20px rgba(16, 185, 129, 0.3);
        }
    </style>
</head>
<body>

    <div class="header-title">Event Management System</div>

    <div class="glass-canvas">
        <div class="nav-hub">
            <h2 style="margin:0;"><i class="fas fa-tasks" style="color:var(--emerald);"></i> Event Governance</h2>
            <div style="display:flex; gap:15px;">
                <a href="add_event.php" class="btn-launch"><i class="fas fa-plus"></i> Launch New</a>
                <a href="vendor_dashboard.php" class="btn-gov" style="color:white;"><i class="fas fa-home"></i> Home</a>
            </div>
        </div>

        <table class="glass-table">
            <thead>
                <tr>
                    <th>Event Identity</th>
                    <th>Scheduling Node</th>
                    <th>Status</th>
                    <th>Governance</th>
                </tr>
            </thead>
            <tbody>
                <?php if(mysqli_num_rows($result) > 0): ?>
                    <?php while($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($row['event_name']); ?></strong></td>
                        <td><i class="far fa-calendar-alt"></i> <?php echo date('M d, Y', strtotime($row['event_date'])); ?></td>
                        <td><span style="color:var(--emerald); font-weight:bold;">● Active</span></td>
                        <td>
                            <a href="edit_event.php?id=<?php echo $row['id']; ?>" class="btn-gov btn-edit">Modify</a>
                            <a href="manage_events.php?delete=<?php echo $row['id']; ?>" class="btn-gov btn-purge" onclick="return confirm('Purge this event protocol?')">Purge</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="4" style="text-align:center; padding:50px; opacity:0.5;">No event protocols detected in your domain.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script src="../assets/main.js"></script>
</body>
</html>