<?php
// Start session
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "scholarship_portal";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if any user is logged in and retrieve their username and role
$loggedInUsername = null;
if (isset($_SESSION['user_Applicant'])) {
    $loggedInUsername = $_SESSION['user_Applicant']['username'];
} elseif (isset($_SESSION['user_Organizer'])) {
    $loggedInUsername = $_SESSION['user_Organizer']['username'];
} elseif (isset($_SESSION['user_Admin'])) {
    $loggedInUsername = $_SESSION['user_Admin']['username'];
}

// Update `is_logged_in` status and `last_logout_time` in the database
if ($loggedInUsername) {
    $updateLogoutStatus = "
        UPDATE accounts_tbl 
        SET is_logged_in = 0, last_logout_time = CURRENT_TIMESTAMP 
        WHERE username = '$loggedInUsername'";
    $conn->query($updateLogoutStatus);
}

// Destroy session data to log the user out
session_unset();
session_destroy();

// Redirect to the login page
header("Location: login/login.html");
exit();

$conn->close();
?>
