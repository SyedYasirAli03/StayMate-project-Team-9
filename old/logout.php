<?php
// Start the session to access session variables.
session_start();

// Destroy the session and unset session variables.
session_destroy();
$_SESSION = array();

// Redirect the user to the login page after logout.
header("Location: login.php");
exit;
?>
