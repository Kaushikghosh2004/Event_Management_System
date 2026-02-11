<?php
session_start();
require_once '../includes/config.php';

// Security: User Session Verification
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit;
}

$id = (int)$_GET['id'];
$u_id = $_SESSION['user_id'];

// Fetch existing data to pre-populate the glass fields
$res = mysqli_query($conn, "SELECT * FROM guest_list WHERE id = $id AND user_id = $u_id");
$guest = mysqli_fetch_assoc($res);

if (isset($_POST['update_guest'])) {
    $name = secure($_POST['g_name']);
    $rel = secure($_POST['g_relation']);
    
    $update_sql = "UPDATE guest_list SET guest_name = '$name', relation = '$rel' WHERE id = $id AND user_id = $u_id";
    
    if (mysqli_query($conn, $update_sql)) {
        // Redirecting to the main list after successful modification
        header("Location: guest_list.php?msg=Updated");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Guest | Event Management System</title>
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
            height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: radial-gradient(circle at bottom right, #1e3a8a, #020617);
            font-family: 'Segoe UI', sans-serif;
            overflow: hidden;
        }

        /* 🔮 The Glass Editor Canvas */
        .edit-canvas {
            background: var(--glass);
            backdrop-filter: blur(30px);
            -webkit-backdrop-filter: blur(30px);
            width: 450px;
            padding: 50px;
            border-radius: 35px;
            border: 1px solid var(--glass-border);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.4);
            text-align: center;
            animation: scaleUp 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        @keyframes scaleUp { from { opacity: 0; transform: scale(0.9); } to { opacity: 1; transform: scale(1); } }

        .header-icon {
            font-size: 3rem;
            color: var(--accent);
            margin-bottom: 20px;
            text-shadow: 0 0 15px rgba(96, 165, 250, 0.4);
        }

        .input-group { position: relative; margin-bottom: 25px; text-align: left; }
        
        .label-tag {
            font-size: 0.75rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--accent);
            margin-left: 10px;
            margin-bottom: 8px;
            display: block;
        }

        /* decision Node Button */
        .btn-update {
            width: 100%;
            background: var(--accent);
            color: #0f172a;
            padding: 18px;
            border-radius: 15px;
            border: none;
            font-weight: 900;
            text-transform: uppercase;
            cursor: pointer;
            transition: 0.3s;
            box-shadow: 0 10px 20px rgba(96, 165, 250, 0.2);
        }

        .btn-update:hover {
            background: #ffffff;
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(255, 255, 255, 0.2);
        }
    </style>
</head>
<body>

    <div class="edit-canvas">
        <div class="header-icon"><i class="fas fa-user-edit"></i></div>
        <h2 style="color:white; margin-bottom:40px; opacity:0.8;">Edit Guest Info</h2>

        <form method="POST">
            <div class="input-group">
                <span class="label-tag">Guest Name</span>
                <input type="text" name="g_name" class="glass-input" value="<?php echo htmlspecialchars($guest['guest_name']); ?>" required>
            </div>

            <div class="input-group">
                <span class="label-tag">Relationship</span>
                <input type="text" name="g_relation" class="glass-input" value="<?php echo htmlspecialchars($guest['relation']); ?>" required>
            </div>

            <button type="submit" name="update_guest" class="btn-update">
                Apply Modifications <i class="fas fa-check-double" style="margin-left:10px;"></i>
            </button>
            
            <div style="margin-top:25px;">
                <a href="guest_list.php" style="color:rgba(255,255,255,0.4); text-decoration:none; font-size:0.9rem;">
                    <i class="fas fa-times"></i> Cancel Edit
                </a>
            </div>
        </form>
    </div>

    <script src="../assets/main.js"></script>
</body>
</html>