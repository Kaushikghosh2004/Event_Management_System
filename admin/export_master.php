<?php
require_once '../includes/config.php';

// Set headers for Excel download
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Event_Master_Report_".date('Y-m-d').".xls");

// SQL Join to get comprehensive data
$sql = "SELECT r.full_name, r.email, p.product_name, v.username as vendor, c.status, c.quantity 
        FROM cart c 
        JOIN registrations r ON c.user_id = r.id 
        JOIN products p ON c.product_id = p.id 
        JOIN vendors v ON p.vendor_id = v.id 
        WHERE c.status != 'In Cart'";

$res = mysqli_query($conn, $sql);

echo "Customer Name\tEmail\tItem Ordered\tVendor\tQuantity\tStatus\n";

while($row = mysqli_fetch_assoc($res)) {
    echo "{$row['full_name']}\t{$row['email']}\t{$row['product_name']}\t{$row['vendor']}\t{$row['quantity']}\t{$row['status']}\n";
}
exit;
?>