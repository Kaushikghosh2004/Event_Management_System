<?php
require_once 'includes/config.php';

// SQL to clear transactional tables but keep structure
$tables = ['cart', 'guest_list', 'products', 'memberships'];

echo "<h2>🔧 System Reset Utility</h2>";

foreach ($tables as $table) {
    $sql = "TRUNCATE TABLE $table";
    if (mysqli_query($conn, $sql)) {
        echo "✅ Table '$table' has been cleared.<br>";
    } else {
        echo "❌ Error clearing '$table': " . mysqli_error($conn) . "<br>";
    }
}

echo "<br><p><strong>System is now ready for a fresh demo.</strong></p>";
echo "<a href='index.php'>Go to Index</a>";
?>