<?php
session_start();
require_once '../includes/config.php';

$member_data = null;
$search_performed = false;

// Logic: Membership Number is mandatory to fetch data
if (isset($_POST['search_membership'])) {
    $search_performed = true;
    $m_no = (int)$_POST['m_no'];
    $res = mysqli_query($conn, "SELECT * FROM memberships WHERE membership_no = $m_no");
    $member_data = mysqli_fetch_assoc($res);
}

// Logic: Handle extension or cancellation
if (isset($_POST['apply_update'])) {
    $m_no = (int)$_POST['m_no'];
    $action = $_POST['update_action'];
    
    if ($action === 'extend') {
        $months = (int)$_POST['extension_period']; // Default 6 months
        mysqli_query($conn, "UPDATE memberships SET expiry_date = DATE_ADD(expiry_date, INTERVAL $months MONTH), status = 'Active' WHERE membership_no = $m_no");
    } else {
        mysqli_query($conn, "UPDATE memberships SET status = 'Cancelled' WHERE membership_no = $m_no");
    }
    header("Location: maintain_users.php?msg=Updated");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Membership | Command Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --glass: rgba(255, 255, 255, 0.1);
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
            background: radial-gradient(circle at top left, #1e3a8a, #020617);
            font-family: 'Segoe UI', sans-serif;
            color: var(--text);
            padding: 40px;
        }

        /* Upper Floating Header */
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
        .canvas {
            background: var(--glass);
            backdrop-filter: blur(30px);
            -webkit-backdrop-filter: blur(30px);
            width: 750px;
            padding: 50px;
            border-radius: 35px;
            border: 1px solid var(--glass-border);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.4);
            animation: fadeIn 0.8s ease-out;
        }

        @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

        /* Search Section */
        .search-container {
            display: flex;
            gap: 15px;
            margin-bottom: 40px;
        }

        .search-input {
            flex: 1;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--glass-border);
            padding: 18px;
            border-radius: 15px;
            color: white;
            font-size: 1.1rem;
            outline: none;
            transition: 0.3s;
        }

        .search-input:focus { border-color: var(--accent); box-shadow: 0 0 15px rgba(96, 165, 250, 0.3); }

        .btn-search {
            background: var(--accent);
            color: #0f172a;
            border: none;
            padding: 0 40px;
            border-radius: 15px;
            font-weight: 800;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn-search:hover { transform: scale(1.05); background: #93c5fd; }

        /* Data Panel: Revealed after Search */
        .data-panel {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--glass-border);
            border-radius: 25px;
            padding: 30px;
            animation: slideIn 0.5s ease-out;
        }

        @keyframes slideIn { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }

        .info-row { display: flex; justify-content: space-between; margin-bottom: 20px; font-size: 1.1rem; }
        .status-pill { background: #34d399; color: #064e3b; padding: 4px 15px; border-radius: 20px; font-size: 0.9rem; font-weight: 800; }

        /* Action Selectors */
        .action-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin: 30px 0; }
        .action-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--glass-border);
            padding: 20px;
            border-radius: 15px;
            text-align: center;
            cursor: pointer;
            transition: 0.3s;
        }

        input[type="radio"] { display: none; }
        input[type="radio"]:checked + .action-card { background: var(--accent); color: #0f172a; border-color: transparent; font-weight: bold; }

        /* Update Button */
        .btn-update {
            width: 100%;
            background: var(--text);
            color: #0f172a;
            padding: 18px;
            border: none;
            border-radius: 15px;
            font-size: 1.1rem;
            font-weight: 800;
            cursor: pointer;
            margin-top: 20px;
            transition: 0.3s;
        }

        .btn-update:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(0,0,0,0.3); }

        .no-result { text-align: center; color: #f87171; font-weight: bold; }
    </style>
</head>
<body>

    <div class="header-title">Event Management System</div>

    <div class="canvas">
        <h2 style="text-align:center; margin-bottom:40px; opacity:0.8;">Update Membership</h2>
        
        <form method="POST" class="search-container">
            <input type="text" name="m_no" class="search-input" placeholder="Enter Membership ID (Mandatory)" required>
            <button type="submit" name="search_membership" class="btn-search">
                <i class="fas fa-search"></i>
            </button>
        </form>

        <?php if ($member_data): ?>
            <form method="POST" class="data-panel">
                <input type="hidden" name="m_no" value="<?php echo $member_data['membership_no']; ?>">
                
                <div class="info-row">
                    <span>Current Status</span>
                    <span class="status-pill"><?php echo $member_data['status']; ?></span>
                </div>
                <div class="info-row">
                    <span>Valid Until</span>
                    <span><?php echo date('M d, Y', strtotime($member_data['expiry_date'])); ?></span>
                </div>

                <hr style="border: 0; border-top: 1px solid var(--glass-border); margin: 30px 0;">

                <p style="font-weight:bold; opacity:0.7;">Select System Action:</p>
                <div class="action-grid">
                    <label>
                        <input type="radio" name="update_action" value="extend" checked>
                        <div class="action-card"><i class="fas fa-calendar-plus"></i><br>Extend</div>
                    </label>
                    <label>
                        <input type="radio" name="update_action" value="cancel">
                        <div class="action-card"><i class="fas fa-calendar-times"></i><br>Cancel</div>
                    </label>
                </div>

                <div id="extensionOptions">
                    <p style="font-weight:bold; opacity:0.7;">Extension Period (Mandatory):</p>
                    <select name="extension_period" style="width:100%; padding:15px; border-radius:12px; background:rgba(255,255,255,0.05); color:white; border:1px solid var(--glass-border); outline:none;">
                        <option value="6" selected>6 Months (Default Extension)</option>
                        <option value="12">1 Year (Premium)</option>
                        <option value="24">2 Years (Elite)</option>
                    </select>
                </div>

                <button type="submit" name="apply_update" class="btn-update">Commit Changes</button>
            </form>
        <?php elseif ($search_performed): ?>
            <p class="no-result"><i class="fas fa-exclamation-circle"></i> No membership found with that ID.</p>
        <?php endif; ?>
    </div>

    <script>
        // Interactive Toggle: Hide extension options if 'Cancel' is selected
        const radios = document.querySelectorAll('input[name="update_action"]');
        const extOptions = document.getElementById('extensionOptions');

        radios.forEach(r => {
            r.addEventListener('change', () => {
                extOptions.style.display = (r.value === 'cancel') ? 'none' : 'block';
            });
        });
    </script>
</body>
</html>