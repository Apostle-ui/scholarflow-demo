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

// Get all applicants from eligible_applicants_tbl
$sql = "SELECT * FROM eligible_applicants_tbl";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($applicant = $result->fetch_assoc()) {
        $applicant_id = $applicant['applicant_id'];

        // Insert data into applicant_demographic
        $sql_demographic = "INSERT INTO applicant_demographic (applicant_id, firstname, middlename, lastname, gender, birthdate, email, contact_number, street) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt_demographic = $conn->prepare($sql_demographic);
        $stmt_demographic->bind_param(
            "issssssss",
            $applicant_id,
            $applicant['firstname'],
            $applicant['middlename'],
            $applicant['lastname'],
            $applicant['gender'],
            $applicant['birthdate'],
            $applicant['email'],
            $applicant['contact_number'],
            $applicant['street']
        );
        $stmt_demographic->execute();

        // Insert data into applicant_parent
        $sql_parent = "INSERT INTO applicant_parent (applicant_parent_id, mother_firstname, mother_middlename, mother_lastname, mother_contact_number, mother_birthdate, father_firstname, father_middlename, father_lastname, father_contact_number, father_birthdate) 
                       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt_parent = $conn->prepare($sql_parent);
        $stmt_parent->bind_param(
            "issssssssss",
            $applicant_id,
            $applicant['mother_firstname'],
            $applicant['mother_middlename'],
            $applicant['mother_lastname'],
            $applicant['mother_contact_number'],
            $applicant['mother_birthdate'],
            $applicant['father_firstname'],
            $applicant['father_middlename'],
            $applicant['father_lastname'],
            $applicant['father_contact_number'],
            $applicant['father_birthdate']
        );
        $stmt_parent->execute();

        // Insert data into applicant_school_file
        $sql_school = "INSERT INTO applicant_school_file (applicant_school_file_id, school_level, year_level, school_name, certificate_registration, school_identification) 
                       VALUES (?, ?, ?, ?, ?, ?)";
        $stmt_school = $conn->prepare($sql_school);
        $stmt_school->bind_param(
            "isssss",
            $applicant_id,
            $applicant['school_level'],
            $applicant['year_level'],
            $applicant['school_name'],
            $applicant['certificate_registration'],
            $applicant['school_identification']
        );
        $stmt_school->execute();

        // Delete the applicant from eligible_applicants_tbl
        $delete_sql = "DELETE FROM eligible_applicants_tbl WHERE applicant_id = ?";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->bind_param("i", $applicant_id);
        $delete_stmt->execute();

        // Reset auto-increment for eligible_applicants_tbl
        $maxSql = "SELECT MAX(applicant_id) FROM eligible_applicants_tbl";
        $resultMax = $conn->query($maxSql);
        if ($resultMax) {
            $row = $resultMax->fetch_row();
            $newAutoIncrement = $row[0] + 1;
            $resetSql = "ALTER TABLE eligible_applicants_tbl AUTO_INCREMENT = $newAutoIncrement";
            $conn->query($resetSql);
        }

        // Close statements for each applicant
        $stmt_demographic->close();
        $stmt_parent->close();
        $stmt_school->close();
        $delete_stmt->close();
    }

    // Display SweetAlert success message
    echo "<script>
        window.onload = function() {
            Swal.fire({
                icon: 'success',
                title: 'All Eligible Applicants Removed',
                text: 'All eligible applicants have been removed.',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = 'eligibleapplicants.php'; // Redirect to eligible applicants page
            });
        }
        </script>";
} else {
    // Display SweetAlert error message if no applicants are found
    echo "<script>
     window.onload = function() {
            Swal.fire({
                title: 'Error!',
                text: 'No eligible applicants found.',
                icon: 'error',
                confirmButtonText: 'OK'
            }).then(() => {
                // Redirect after SweetAlert is closed
                window.location.href = 'eligibleapplicants.php';
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
