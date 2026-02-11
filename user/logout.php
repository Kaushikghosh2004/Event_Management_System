<?php
session_start();
session_unset();
session_destroy();
// Redirect back to the index portal choice
header("Location: index.php");
exit;
?>