<?php
// includes/functions.php

function sanitize($data, $conn) {
    return mysqli_real_escape_string($conn, htmlspecialchars(strip_tags(trim($data))));
}

function format_status($status) {
    return ($status == 'Confirmed') ? "✅ Confirmed" : "⏳ Pending";
}
?>