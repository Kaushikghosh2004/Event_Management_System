<?php
session_start();
require_once '../includes/config.php';

// Security: Admin Only
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

if (isset($_GET['approve_id'])) {
    $c_id = mysqli_real_escape_string($conn, $_GET['approve_id']);
    
    // Update the 'status' column in the cart table
    $update = mysqli_query($conn, "UPDATE cart SET status = 'Ordered' WHERE id = '$c_id'");
    
    if ($update) {
        header("Location: admin_dashboard.php?msg=Order_Cleared");
    } else {
        echo "Registry Error: " . mysqli_error($conn);
    }
}
?>