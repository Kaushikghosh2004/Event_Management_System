<?php
session_start();
require_once '../includes/config.php';

// Security: Admin Gatekeeper
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

if (isset($_GET['approve_id'])) {
    $order_id = mysqli_real_escape_string($conn, $_GET['approve_id']);
    
    // Update logic targeting the ENUM status column in your cart table
    $sql = "UPDATE cart SET status = 'Ordered' WHERE id = '$order_id'";
    
    if (mysqli_query($conn, $sql)) {
        header("Location: admin_dashboard.php?msg=Order_Authorized");
    } else {
        echo "Registry Sync Error: " . mysqli_error($conn);
    }
}
?>