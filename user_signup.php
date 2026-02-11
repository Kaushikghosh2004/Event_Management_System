<?php
session_start();
// Connectivity Protocol
require_once 'includes/config.php';

$message = "";
if (isset($_POST['signup'])) {
    // Security: Escaping inputs for the 'registrations' table
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    
    // Hashing: Encryption for the new 'password' column
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); 

    // Integrity Check: Duplicate detection within the registry
    $check = mysqli_query($conn, "SELECT * FROM registrations WHERE email = '$email'");
    
    if (mysqli_num_rows($check) > 0) {
        $message = "🔴 SECURITY ALERT: Identity already exists in the registry.";
    } else {
        // Operational Insert: Matching the updated schema
        $sql = "INSERT INTO registrations (name, email, password) VALUES ('$name', '$email', '$password')";
        
        if (mysqli_query($conn, $sql)) {
            $message = "🟢 PROTOCOL ACTIVATED: You may now access the portal.";
        } else {
            // Error Handling: Utilizing mysqli_error for debugging
            $message = "🔴 SYSTEM ERROR: " . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Signup | Event Management System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --glass: rgba(255, 255, 255, 0.05);
            --ocean: #3b82f6;
            --text: #ffffff;
        }

        body {
            margin: 0;
            min-height: 100vh;
            background: radial-gradient(at 0% 0%, #0f172a 0, transparent 50%), #020617;
            font-family: 'Segoe UI', sans-serif;
            color: var(--text);
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .signup-canvas {
            background: var(--glass);
            backdrop-filter: blur(25px);
            width: 480px;
            padding: 50px;
            border-radius: 40px;
            border: 1px solid rgba(255,255,255,0.1);
            box-shadow: 0 25px 50px rgba(0,0,0,0.5);
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }

        .btn-nav {
            position: absolute; top: 30px; left: 30px;
            color: var(--ocean); text-decoration: none; font-weight: bold; font-size: 0.9rem;
        }

        .input-group { position: relative; margin-bottom: 25px; }

        .glass-input {
            width: 100%;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255,255,255,0.1);
            padding: 15px 20px;
            border-radius: 15px;
            color: white;
            outline: none;
            box-sizing: border-box;
            transition: 0.3s;
        }

        .glass-input:focus { border-color: var(--ocean); box-shadow: 0 0 15px rgba(59, 130, 246, 0.3); }

        .label-node {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--ocean);
            font-weight: 900;
            margin-bottom: 8px;
            display: block;
        }

        .btn-submit {
            width: 100%;
            background: var(--ocean);
            color: #0f172a;
            padding: 18px;
            border-radius: 15px;
            border: none;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 2px;
            cursor: pointer;
            transition: 0.4s;
        }

        .btn-submit:hover { transform: scale(1.02); filter: brightness(1.2); }
        .msg { text-align: center; font-size: 0.85rem; margin-bottom: 25px; font-weight: bold; }
    </style>
</head>
<body>

<a href="index.php" class="btn-nav"><i class="fas fa-arrow-left"></i> BACK TO PORTAL</a>

<div class="signup-canvas">
    <div style="text-align: center; margin-bottom: 40px;">
        <i class="fas fa-user-plus" style="font-size: 3rem; color: var(--ocean);"></i>
        <h2 style="margin-top: 15px; letter-spacing: 2px;">JOIN SYSTEM</h2>
        <p style="opacity: 0.5; font-size: 0.75rem;">Initialize Your Participant Account</p>
    </div>

    <?php if($message) echo "<p class='msg'>$message</p>"; ?>

    <form method="POST">
        <div class="input-group">
            <span class="label-node">Full Identity</span>
            <input type="text" name="name" class="glass-input" placeholder="e.g. John Doe" required>
        </div>

        <div class="input-group">
            <span class="label-node">Registry Email</span>
            <input type="email" name="email" class="glass-input" placeholder="name@protocol.com" required>
        </div>

        <div class="input-group">
            <span class="label-node">Security Password</span>
            <input type="password" name="password" class="glass-input" placeholder="••••••••" required>
        </div>

        <button type="submit" name="signup" class="btn-submit">Register Account</button>
    </form>

    <div style="text-align: center; margin-top: 30px; opacity: 0.6; font-size: 0.8rem;">
        Existing Identity? <a href="user/login.php" style="color: var(--ocean); text-decoration: none; font-weight: bold;">Login Here</a>
    </div>
</div>

</body>
</html>