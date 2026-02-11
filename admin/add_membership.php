<?php
session_start();
require_once '../includes/config.php';
// if($_SESSION['role'] !== 'admin') { header("Location: login.php"); exit; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Premium Membership | Admin Control</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --primary: #3b82f6; --secondary: #1e40af; --accent: #4472c4; --bg: #f4f4f5; --canvas: #cbd5e1; }
        body { background: var(--bg); font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; display: flex; align-items: center; justify-content: center; min-height: 100vh; }

        .canvas { 
            background: var(--canvas); 
            width: 700px; 
            padding: 50px; 
            border-radius: 20px; 
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
            border: 1px solid #71717a;
            position: relative;
            overflow: hidden;
        }

        /* Decorative Background Element */
        .canvas::before {
            content: ''; position: absolute; top: -50px; right: -50px; width: 150px; height: 150px;
            background: var(--primary); opacity: 0.1; border-radius: 50%;
        }

        h2 { text-align: center; color: var(--secondary); margin-bottom: 40px; font-size: 2rem; text-transform: uppercase; letter-spacing: 1px; }

        /* Powerful Input Styling */
        .input-wrapper { margin-bottom: 35px; position: relative; }
        .label { 
            background: var(--primary); color: white; padding: 8px 20px; border-radius: 8px 8px 0 0; 
            font-size: 0.9rem; font-weight: bold; display: inline-block;
        }
        input[type="text"] {
            width: 100%; padding: 15px; border: 2px solid var(--primary); border-radius: 0 12px 12px 12px;
            font-size: 1.1rem; outline: none; box-sizing: border-box; transition: 0.3s;
        }
        input[type="text"]:focus { box-shadow: 0 0 15px rgba(59, 130, 246, 0.3); }

        /* Interactive Radio Cards */
        .duration-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin-top: 15px; }
        .radio-card {
            background: white; border: 2px solid transparent; border-radius: 12px; padding: 20px;
            text-align: center; cursor: pointer; transition: 0.3s; position: relative;
        }
        .radio-card:hover { transform: translateY(-5px); background: #f8fafc; }
        
        input[type="radio"] { display: none; }
        input[type="radio"]:checked + .radio-card { border-color: var(--primary); background: #eff6ff; }
        input[type="radio"]:checked + .radio-card::after {
            content: '\f058'; font-family: 'Font Awesome 6 Free'; font-weight: 900;
            position: absolute; top: 5px; right: 5px; color: var(--primary);
        }

        .card-title { display: block; font-weight: bold; margin-bottom: 5px; color: var(--secondary); }
        .card-price { font-size: 0.85rem; color: #64748b; }

        /* Live Total Amount Display */
        .total-banner {
            background: var(--secondary); color: white; margin-top: 40px; padding: 20px;
            border-radius: 12px; display: flex; justify-content: space-between; align-items: center;
            font-weight: bold; font-size: 1.2rem;
        }

        .btn-confirm {
            width: 100%; background: var(--accent); color: white; padding: 18px; border: none;
            border-radius: 12px; font-size: 1.2rem; font-weight: bold; cursor: pointer;
            margin-top: 25px; transition: 0.3s; box-shadow: 0 4px 15px rgba(68, 114, 196, 0.4);
        }
        .btn-confirm:hover { background: var(--secondary); transform: scale(1.02); }
    </style>
</head>
<body>

    <div class="canvas">
        <h2>Add Membership</h2>
        <form method="POST" action="process_membership.php" id="membershipForm">
            
            <div class="input-wrapper">
                <span class="label"><i class="fas fa-id-badge"></i> User / Vendor ID</span>
                <input type="text" name="member_id" placeholder="Ex: V-1024" required>
            </div>

            <p style="font-weight: bold; color: var(--secondary); margin-bottom: 10px;">Select Duration (Mandatory):</p>
            
            <div class="duration-grid">
                <label>
                    <input type="radio" name="duration" value="6" data-price="500" checked>
                    <div class="radio-card">
                        <span class="card-title">6 Months</span>
                        <span class="card-price">Rs. 500/-</span>
                    </div>
                </label>

                <label>
                    <input type="radio" name="duration" value="12" data-price="900">
                    <div class="radio-card">
                        <span class="card-title">1 Year</span>
                        <span class="card-price">Rs. 900/-</span>
                    </div>
                </label>

                <label>
                    <input type="radio" name="duration" value="24" data-price="1600">
                    <div class="radio-card">
                        <span class="card-title">2 Years</span>
                        <span class="card-price">Rs. 1600/-</span>
                    </div>
                </label>
            </div>

            <div class="total-banner">
                <span>Total Amount</span>
                <span id="displayTotal">Rs. 500/-</span>
            </div>

            <button type="submit" class="btn-confirm">
                <i class="fas fa-check-circle"></i> Confirm Membership
            </button>
        </form>
    </div>

    <script>
        // Real-time Total Amount calculation
        const radios = document.querySelectorAll('input[name="duration"]');
        const displayTotal = document.getElementById('displayTotal');

        radios.forEach(radio => {
            radio.addEventListener('change', (e) => {
                const price = e.target.getAttribute('data-price');
                displayTotal.innerText = `Rs. ${price}/-`;
                
                // Add a little pop animation
                displayTotal.style.transform = "scale(1.2)";
                setTimeout(() => { displayTotal.style.transform = "scale(1)"; }, 200);
            });
        });
    </script>
</body>
</html>