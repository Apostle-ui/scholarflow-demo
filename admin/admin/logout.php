<?php
// Start the session
session_start();

// Unset all session variables
session_unset();

// Destroy the session
session_destroy();

// Redirect the user to the login page or the home page
header("Location: ../../login/login.html");
exit;
?>