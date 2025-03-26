<?php
// Create connection
$servername = "localhost";
$username = "root"; // Replace with your DB username
$password = ""; // Replace with your DB password
$dbname = "scholarship_portal";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Start transaction to ensure all deletions occur or none at all
$conn->begin_transaction();

try {
    // Delete all records from applicant_school_file, applicant_parent, and applicant_demographic
    $conn->query("DELETE FROM applicant_school_file");
    $conn->query("DELETE FROM applicant_parent");
    $conn->query("DELETE FROM applicant_demographic");

    // Reset AUTO_INCREMENT for each table
    $conn->query("ALTER TABLE applicant_school_file AUTO_INCREMENT = 1");
    $conn->query("ALTER TABLE applicant_parent AUTO_INCREMENT = 1");
    $conn->query("ALTER TABLE applicant_demographic AUTO_INCREMENT = 1");

    // Commit transaction
    $conn->commit();

    // Display SweetAlert success message
    echo "<script>
        window.onload = function() {
            Swal.fire({
                icon: 'success',
                title: 'All Applicant Data Removed',
                text: 'All applicant records have been successfully deleted.',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = 'applicants.php';
            });
        }
    </script>";
} catch (Exception $e) {
    // Rollback transaction if an error occurs
    $conn->rollback();
    
    // Display SweetAlert error message
    echo "<script>
        window.onload = function() {
            Swal.fire({
                title: 'Error!',
                text: 'Failed to delete applicant data.',
                icon: 'error',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = 'applicants.php';
            });
        }
    </script>";
}

// Close the connection
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