<?php
$conn = mysqli_connect("localhost", "root", "", "event_management_db");
if (!$conn) die("Connection Failed");

// Super Power: Global Sanitization Function
function secure($data) {
    global $conn;
    return mysqli_real_escape_string($conn, htmlspecialchars(strip_tags(trim($data))));
}
?>