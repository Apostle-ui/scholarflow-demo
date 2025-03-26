<?php

// Connect to the database
$servername = "localhost";
$username = "root"; // Replace with your DB username
$password = ""; // Replace with your DB password
$dbname = "scholarship_portal";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and retrieve form data
    $notification_id = $_POST['notification_id'];
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);
    $recipient_email = mysqli_real_escape_string($conn, $_POST['recipient_email']);
    $recipients_group = mysqli_real_escape_string($conn, $_POST['recipients_group']);

    // Prepare SQL query to update the notification
    $query = "UPDATE notifications_tbl SET 
                title = '$title',
                message = '$message',
                recipient_email = '$recipient_email',
                recipients_group = '$recipients_group'
              WHERE notification_id = '$notification_id'";

    // Execute query and check for success
    if (mysqli_query($conn, $query)) {
        echo "<script>
            window.onload = function() {
                Swal.fire({
                    title: 'Success',
                    text: 'Notification updated successfully!',
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.href = 'notification.php'; // Redirect to notification page
                });
            }
        </script>";
    } else {
        // In case of an error
        echo "<script>
            window.onload = function() {
                Swal.fire({
                    title: 'Error',
                    text: 'Error updating notification: " . mysqli_error($conn) . "',
                    icon: 'error',
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.href = 'notification.php'; // Redirect to notification page
                });
            }
        </script>";
    }

    // Close the database connection
    mysqli_close($conn);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scholarship Portal</title>
    <!-- SweetAlert2 Script -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
</body>
</html>
