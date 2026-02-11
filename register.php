<?php
require_once 'includes/config.php';

if (isset($_POST['submit'])) {
    // Using mysqli_real_escape_string for security
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $event_id = (int)$_POST['event_id']; // Use ID, not Name
    $txn_id = mysqli_real_escape_string($conn, $_POST['txn_id']);

    // Relational Insert
    $sql = "INSERT INTO registrations (name, email, event_id, txn_id, status) 
            VALUES ('$name', '$email', '$event_id', '$txn_id', 'pending')";

    if (mysqli_query($conn, $sql)) {
        header("Location: index.php?msg=Registration_Protocol_Initiated");
    }
}
?>