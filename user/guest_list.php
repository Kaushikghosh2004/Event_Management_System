<?php
session_start();
require_once '../includes/config.php';

// Security: User Session Verification
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit;
}

$u_id = $_SESSION['user_id'];

// --- Handle Interactive Logic ---
if (isset($_GET['delete'])) {
    $guest_id = (int)$_GET['delete'];
    if(mysqli_query($conn, "DELETE FROM guest_list WHERE id = $guest_id AND user_id = $u_id")) {
        // Success state for JS Toast
        $msg = "Guest Removed";
    }
}

if (isset($_POST['add_guest'])) {
    $g_name = secure($_POST['g_name']);
    $g_relation = secure($_POST['g_relation']);
    if(mysqli_query($conn, "INSERT INTO guest_list (user_id, guest_name, relation) VALUES ($u_id, '$g_name', '$g_relation')")) {
        $msg = "Guest Added Successfully";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Guest Registry | Event Management System</title>
    <link rel="stylesheet" href="../assets/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --glass: rgba(255, 255, 255, 0.08);
            --glass-border: rgba(255, 255, 255, 0.15);
            --accent: #60a5fa;
            --text: #ffffff;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            background: radial-gradient(circle at top right, #1e3a8a, #020617);
            font-family: 'Segoe UI', sans-serif;
            color: var(--text);
            padding-bottom: 50px;
        }

        /* 1. Upper Floating Title */
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

        /* 🔮 The Main Glass Canvas */
        .guest-canvas {
            background: var(--glass);
            backdrop-filter: blur(30px);
            -webkit-backdrop-filter: blur(30px);
            width: 950px;
            padding: 45px;
            border-radius: 35px;
            border: 1px solid var(--glass-border);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.4);
            animation: fadeIn 0.8s ease-out;
        }

        @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr auto;
            gap: 20px;
            background: rgba(255,255,255,0.03);
            padding: 25px;
            border-radius: 20px;
            border: 1px solid var(--glass-border);
            margin-bottom: 40px;
        }

        /* Action Column */
        .btn-delete {
            color: #f87171;
            text-decoration: none;
            font-size: 1.1rem;
            transition: 0.3s;
        }
        .btn-delete:hover { color: #ef4444; transform: scale(1.2); text-shadow: 0 0 10px rgba(239, 68, 68, 0.5); }

        .nav-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        
        /* Table Glass Formatting */
        .glass-table { width: 100%; border-collapse: collapse; }
        .glass-table th { text-align: left; padding: 15px; opacity: 0.6; text-transform: uppercase; letter-spacing: 1px; font-size: 0.8rem; }
        .glass-table td { padding: 15px; border-bottom: 1px solid var(--glass-border); }
        .glass-table tr:hover td { background: rgba(255,255,255,0.05); color: var(--accent); }
    </style>
</head>
<body>

    <div class="header-title">Event Management System</div>

    <div class="guest-canvas">
        <div class="nav-row">
            <h2 style="margin:0; opacity:0.8;"><i class="fas fa-users-rectangle" style="color:var(--accent); margin-right:10px;"></i> Guest Registry</h2>
            <a href="dashboard.php" class="btn-glass-nav" style="text-decoration:none; color:white; font-weight:bold; background:var(--glass); padding:10px 25px; border-radius:12px; border:1px solid var(--glass-border);">
                <i class="fas fa-arrow-left"></i> Dashboard
            </a>
        </div>

        <form method="POST" class="form-grid">
            <div class="input-wrap">
                <input type="text" name="g_name" class="glass-input" placeholder="Guest Full Name" required>
            </div>
            <div class="input-wrap">
                <input type="text" name="g_relation" class="glass-input" placeholder="Relationship (e.g. Family)" required>
            </div>
            <button type="submit" name="add_guest" class="btn-powerful" style="padding: 12px 30px;">
                <i class="fas fa-user-plus"></i> Add
            </button>
        </form>

        <table class="glass-table">
            <thead>
                <tr>
                    <th><i class="fas fa-signature"></i> Guest Name</th>
                    <th><i class="fas fa-heart"></i> Relation</th>
                    <th style="text-align:center;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $res = mysqli_query($conn, "SELECT * FROM guest_list WHERE user_id = $u_id");
                if(mysqli_num_rows($res) > 0):
                    while($row = mysqli_fetch_assoc($res)): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($row['guest_name']); ?></strong></td>
                        <td style="opacity:0.8;"><?php echo htmlspecialchars($row['relation']); ?></td>
                        <td style="text-align:center;">
                            <a href="guest_list.php?delete=<?php echo $row['id']; ?>" class="btn-delete" title="Remove Guest">
                                <i class="fas fa-trash-can"></i>
                            </a>
                        </td>
                    </tr>
                <?php endwhile; else: ?>
                    <tr><td colspan="3" style="text-align:center; padding:40px; opacity:0.5;">No guests added to your registry yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script src="../assets/main.js"></script>
    <script>
        // Trigger Toast for PHP message if exists
        <?php if(isset($msg)): ?>
            showToast("<?php echo $msg; ?>", "success");
        <?php endif; ?>
    </script>
</body>
</html>