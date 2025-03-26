<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

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

// Handle "Delete All Notifications"
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Check if delete_all button is pressed
    if (isset($_POST['delete_all'])) {
        // Prepare and execute the query to delete all notifications
        $delete_query = "DELETE FROM notifications_tbl";
        
        // Prepare the delete statement
        $deleteStmt = $conn->prepare($delete_query);
        
        if ($deleteStmt->execute()) {
            // Reset auto-increment for notifications_tbl
            $maxSql = "SELECT MAX(notification_id) FROM notifications_tbl";
            $result = $conn->query($maxSql);

            if ($result) {
                $row = $result->fetch_row();
                $newAutoIncrement = $row[0] + 1;
                $resetSql = "ALTER TABLE notifications_tbl AUTO_INCREMENT = $newAutoIncrement";
                if (!$conn->query($resetSql)) {
                    echo "<script>
                        window.onload = function() {
                            Swal.fire({
                                title: 'Error',
                                text: 'Error resetting auto increment for notifications_tbl: " . $conn->error . "',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                window.location.href = 'notification.php';
                            });
                        }
                    </script>";
                    exit; // Prevent further execution
                }
            } else {
                echo "<script>
                    window.onload = function() {
                        Swal.fire({
                            title: 'Error',
                            text: 'Error retrieving max notification ID: " . $conn->error . "',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            window.location.href = 'notification.php';
                        });
                    }
                </script>";
                exit; // Prevent further execution
            }

            // Success case: Show SweetAlert and redirect
            echo "<script>
                window.onload = function() {
                    Swal.fire({
                        title: 'Success',
                        text: 'All notifications deleted successfully!',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        window.location.href = 'notification.php';
                    });
                }
            </script>";
        } else {
            echo "<script>
                window.onload = function() {
                    Swal.fire({
                        title: 'Error',
                        text: 'Error deleting notifications: " . $conn->error . "',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        window.location.href = 'notification.php';
                    });
                }
            </script>";
        }
    } else {
        echo "<script>
            window.onload = function() {
                Swal.fire({
                    title: 'No Action Taken',
                    text: 'Delete action was not confirmed.',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.href = 'notification.php';
                });
            }
        </script>";
    }
}

$conn->close();
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
