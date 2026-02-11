<?php
require_once '../includes/config.php';

$message = "";
$status = "";

if (isset($_POST['signup'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password']; 
    $category = mysqli_real_escape_string($conn, $_POST['category']);

    // Registration Logic
    $check = mysqli_query($conn, "SELECT * FROM vendors WHERE email = '$email'");
    
    if (mysqli_num_rows($check) > 0) {
        $message = "Email already registered!";
        $status = "error";
    } else {
        $sql = "INSERT INTO vendors (username, email, password, category) 
                VALUES ('$name', '$email', '$password', '$category')";
        
        if (mysqli_query($conn, $sql)) {
            $message = "Registration Successful!";
            $status = "success";
        } else {
            $message = "Database Error. Try again.";
            $status = "error";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register | Event Management System</title>
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
            /* Deep Mesh Gradient for Glass Contrast */
            background: radial-gradient(at top left, #1e3a8a, transparent),
                        radial-gradient(at bottom right, #1e40af, transparent),
                        #020617;
            font-family: 'Segoe UI', sans-serif;
            overflow-x: hidden;
        }

        /* Upper Floating Header */
        .header-title {
            margin: 40px 0;
            background: var(--glass);
            backdrop-filter: blur(15px);
            padding: 15px 60px;
            border-radius: 50px;
            border: 1px solid var(--glass-border);
            color: var(--text);
            font-size: 1.2rem;
            font-weight: 800;
            letter-spacing: 3px;
            text-transform: uppercase;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        }

        /* 🔮 The Main Glass Canvas */
        .signup-canvas {
            background: var(--glass);
            backdrop-filter: blur(30px);
            -webkit-backdrop-filter: blur(30px);
            width: 1000px;
            display: flex;
            border-radius: 35px;
            border: 1px solid var(--glass-border);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            overflow: hidden;
            animation: slideIn 0.8s ease-out;
        }

        @keyframes slideIn { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }

        /* Left Side: Form Area */
        .form-area { flex: 1.4; padding: 50px; border-right: 1px solid var(--glass-border); }
        
        /* Right Side: Info Panel */
        .info-panel { 
            flex: 1; 
            padding: 50px; 
            background: rgba(255, 255, 255, 0.03);
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .input-group { position: relative; margin-bottom: 25px; }

        .input-field, select {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--glass-border);
            padding: 15px 20px;
            width: 100%;
            color: white;
            font-size: 1rem;
            border-radius: 12px;
            outline: none;
            transition: 0.3s;
            box-sizing: border-box;
        }

        .input-field:focus, select:focus {
            background: rgba(255, 255, 255, 0.1);
            border-color: var(--accent);
            box-shadow: 0 0 15px rgba(96, 165, 250, 0.3);
        }

        .toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--accent);
            cursor: pointer;
        }

        /* Action Tiles for Info Panel */
        .category-tile {
            background: var(--glass);
            margin-bottom: 15px;
            padding: 15px;
            border-radius: 15px;
            border: 1px solid var(--glass-border);
            display: flex;
            align-items: center;
            gap: 15px;
            color: white;
            transition: 0.3s;
        }
        .category-tile i { color: var(--accent); font-size: 1.2rem; }
        .category-tile:hover { transform: translateX(10px); background: rgba(255,255,255,0.15); }

        .btn-signup {
            width: 100%;
            background: linear-gradient(135deg, #60a5fa, #3b82f6);
            color: #0f172a;
            padding: 18px;
            border-radius: 15px;
            font-weight: 800;
            cursor: pointer;
            border: none;
            text-transform: uppercase;
            margin-top: 20px;
            transition: 0.3s;
            box-shadow: 0 10px 20px rgba(59, 130, 246, 0.3);
        }

        .btn-signup:hover { transform: translateY(-3px); box-shadow: 0 15px 30px rgba(59, 130, 246, 0.5); }

        .status-msg {
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 25px;
            text-align: center;
            font-weight: bold;
        }
        .error { background: rgba(239, 68, 68, 0.2); color: #f87171; }
        .success { background: rgba(16, 185, 129, 0.2); color: #34d399; }
    </style>
</head>
<body>

    <div class="header-title">Event Management System</div>

    <div class="signup-canvas">
        <div class="form-area">
            <h2 style="color:white; margin-bottom:40px; text-align:center; opacity:0.8;">Admin Registration</h2>
            
            <?php if($message): ?>
                <div class="status-msg <?php echo $status; ?>">
                    <i class="fas <?php echo $status == 'error' ? 'fa-times-circle' : 'fa-check-circle'; ?>"></i>
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="input-group">
                    <input type="text" name="name" class="input-field" placeholder="Full Name" required>
                </div>

                <div class="input-group">
                    <input type="email" name="email" class="input-field" placeholder="Admin Email" required>
                </div>

                <div class="input-group">
                    <input type="password" name="password" id="pass" class="input-field" placeholder="Access Password" required>
                    <i class="fas fa-eye toggle-password" id="eye"></i>
                </div>

                <div class="input-group">
                    <select name="category" required>
                        <option value="" disabled selected>Select Business Category</option>
                        <option value="Catering">Catering Services</option>
                        <option value="Florist">Floral Design</option>
                        <option value="Decoration">Event Decoration</option>
                        <option value="Lighting">Audio & Lighting</option>
                    </select>
                </div>

                <button type="submit" name="signup" class="btn-signup">Initialize Account</button>
            </form>
            
            <p style="text-align:center; color:rgba(255,255,255,0.5); margin-top:20px;">
                Already have an account? <a href="login.php" style="color:var(--accent); text-decoration:none;">Login Here</a>
            </p>
        </div>

        <div class="info-panel">
            <h3 style="color:white; margin-bottom:30px; opacity:0.7;">Supported Categories</h3>
            
            <div class="category-tile"><i class="fas fa-utensils"></i> <span>Catering Services</span></div>
            <div class="category-tile"><i class="fas fa-seedling"></i> <span>Floral Design</span></div>
            <div class="category-tile"><i class="fas fa-holly-berry"></i> <span>Event Decoration</span></div>
            <div class="category-tile"><i class="fas fa-lightbulb"></i> <span>Audio & Lighting</span></div>
        </div>
    </div>

    <script>
        // Password Visibility Interaction
        const pass = document.getElementById('pass');
        const eye = document.getElementById('eye');

        eye.addEventListener('click', () => {
            const type = pass.getAttribute('type') === 'password' ? 'text' : 'password';
            pass.setAttribute('type', type);
            eye.classList.toggle('fa-eye-slash');
        });
    </script>
</body>
</html>