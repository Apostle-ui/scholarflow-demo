<?php
// Include database connection
$servername = "localhost";
$username = "root"; // Replace with your DB username
$password = ""; // Replace with your DB password
$dbname = "scholarship_portal";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the notification_id is set and is a valid number
if (isset($_POST['notification_id']) && is_numeric($_POST['notification_id'])) {
    $notification_id = $_POST['notification_id'];

    // Prepare delete statement
    $deleteStmt = $conn->prepare("DELETE FROM notifications_tbl WHERE notification_id = ?");
    $deleteStmt->bind_param("i", $notification_id);

    if ($deleteStmt->execute()) {
        // Reset auto-increment for notifications_tbl
        $maxSql = "SELECT MAX(notification_id) FROM notifications_tbl";
        $result = $conn->query($maxSql);

        if ($result) {
            $row = $result->fetch_row();
            $newAutoIncrement = $row[0] + 1;
            $resetSql = "ALTER TABLE notifications_tbl AUTO_INCREMENT = $newAutoIncrement";
            if (!$conn->query($resetSql)) {
                echo "Error resetting auto increment for notifications_tbl: " . $conn->error;
            }
        } else {
            echo "Error retrieving max notification ID: " . $conn->error;
        }
        
        // Redirect back to the notification page
        header("Location: notification.php");
        exit();
    } else {
        echo "Error: " . $deleteStmt->error;
    }

    $deleteStmt->close();
} else {
    echo "Invalid notification ID.";
}

$conn->close();
?>
