<?php
require_once 'config.php';
require_once 'includes/functions.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = sanitize($_POST['name'], $conn);
    $email = sanitize($_POST['email'], $conn);
    $txn = sanitize($_POST['txn_id'], $conn);
    $e_id = (int)$_POST['event_id'];

    $sql = "INSERT INTO registrations (event_id, full_name, email, txn_id) VALUES ($e_id, '$name', '$email', '$txn')";
    
    if (mysqli_query($conn, $sql)) {
        echo "Success! Your registration is pending verification.";
    } else {
        echo "Error: Duplicate Transaction ID or System Error.";
    }
}
?>