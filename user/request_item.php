<?php
session_start();
require_once '../includes/config.php';

// Security: Restrict to authenticated Users
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit;
}

if (isset($_GET['id'])) {
    $product_id = mysqli_real_escape_string($conn, $_GET['id']);
    $user_id = $_SESSION['user_id'];

    // Insert request into the 'cart' table with 'In Cart' status
    $sql = "INSERT INTO cart (user_id, product_id, quantity, status) 
            VALUES ('$user_id', '$product_id', 1, 'In Cart')";

    if (mysqli_query($conn, $sql)) {
        header("Location: view_cart.php?msg=Service_Requested");
    } else {
        echo "Error updating registry: " . mysqli_error($conn);
    }
} else {
    header("Location: browse_events.php");
}
?>

