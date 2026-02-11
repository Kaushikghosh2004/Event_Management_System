<?php
session_start();
require_once '../includes/config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit;
}

// Fetch all available protocols from your events table
$events_query = mysqli_query($conn, "SELECT * FROM events ORDER BY event_date ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Browse Events | EMS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --glass: rgba(255, 255, 255, 0.05); --ocean: #3b82f6; --text: #ffffff; }
        body { margin: 0; background: radial-gradient(at 0% 0%, #0f172a 0, transparent 50%), #020617; font-family: 'Segoe UI', sans-serif; color: var(--text); padding: 40px; }
        .event-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 25px; }
        .event-card { background: var(--glass); backdrop-filter: blur(20px); padding: 35px; border-radius: 30px; border: 1px solid rgba(255,255,255,0.1); transition: 0.3s; }
        .event-card:hover { transform: translateY(-5px); border-color: var(--ocean); }
        .btn-request { display: inline-block; margin-top: 20px; padding: 12px 25px; background: var(--ocean); color: #0f172a; text-decoration: none; border-radius: 12px; font-weight: 800; font-size: 0.8rem; text-transform: uppercase; }
    </style>
</head>
<body>
    <h2 style="letter-spacing:2px; margin-bottom:50px; opacity:0.9;"><i class="fas fa-calendar-check" style="color:var(--ocean);"></i> AVAILABLE PROTOCOLS</h2>
    <div class="event-grid">
        <?php while($event = mysqli_fetch_assoc($events_query)): ?>
            <div class="event-card">
                <h3 style="margin:0;"><?php echo htmlspecialchars($event['event_name']); ?></h3>
                <p style="opacity:0.5; font-size:0.85rem; margin: 10px 0;"><i class="fas fa-clock"></i> <?php echo $event['event_date']; ?></p>
                
                <p style="font-size:0.9rem; line-height:1.6; min-height:60px;">
                    <?php echo isset($event['description']) ? htmlspecialchars($event['description']) : "Protocol details are currently classified."; ?>
                </p>
                
                <a href="request_item.php?id=<?php echo $event['id']; ?>" class="btn-request">Request Access</a>
            </div>
        <?php endwhile; ?>
    </div>
</body>
</html>