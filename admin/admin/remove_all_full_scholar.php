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

// Get all applicants from full_scholar_applicant
$sql = "SELECT * FROM full_scholar_applicant";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Loop through all rows
    while ($applicant = $result->fetch_assoc()) {
        $applicant_id = $applicant['applicant_id'];

        // Insert the applicant into eligible_applicants_tbl
        $sql_eligible_applicants = "
        INSERT INTO eligible_applicants_tbl (
            applicant_id, firstname, middlename, lastname, gender, birthdate, email, contact_number, street,
            mother_firstname, mother_middlename, mother_lastname, mother_contact_number, mother_birthdate,
            father_firstname, father_middlename, father_lastname, father_contact_number, father_birthdate,
            school_level, year_level, school_name, certificate_registration, school_identification
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt_eligible_applicants = $conn->prepare($sql_eligible_applicants);
        $stmt_eligible_applicants->bind_param(
            "isssssssssssssssssssssss",
            $applicant_id,
            $applicant['firstname'],
            $applicant['middlename'],
            $applicant['lastname'],
            $applicant['gender'],
            $applicant['birthdate'],
            $applicant['email'],
            $applicant['contact_number'],
            $applicant['street'],
            $applicant['mother_firstname'],
            $applicant['mother_middlename'],
            $applicant['mother_lastname'],
            $applicant['mother_contact_number'],
            $applicant['mother_birthdate'],
            $applicant['father_firstname'],
            $applicant['father_middlename'],
            $applicant['father_lastname'],
            $applicant['father_contact_number'],
            $applicant['father_birthdate'],
            $applicant['school_level'],
            $applicant['year_level'],
            $applicant['school_name'],
            $applicant['certificate_registration'],
            $applicant['school_identification']
        );
        $stmt_eligible_applicants->execute();

        // Delete the applicant from full_scholar_applicant
        $delete_sql = "DELETE FROM full_scholar_applicant WHERE applicant_id = ?";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->bind_param("i", $applicant_id);
        $delete_stmt->execute();
    }

    // Reset auto-increment for full_scholar_applicant table
    $maxSql = "SELECT MAX(applicant_id) FROM full_scholar_applicant";
    $result = $conn->query($maxSql);
    if ($result) {
        $row = $result->fetch_row();
        $newAutoIncrement = $row[0] + 1;
        $resetSql = "ALTER TABLE full_scholar_applicant AUTO_INCREMENT = $newAutoIncrement";
        $conn->query($resetSql);
    }

    // Close the connection
    $stmt_eligible_applicants->close();
    $conn->close();

    // Success message with SweetAlert
    echo "<script>
        window.onload = function() {
            Swal.fire({
                icon: 'success',
                title: 'All Full Scholar Applicants Removed',
                text: 'All full scholarship applicants have been removed.',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = 'fullscholar.php';
            });
        };
    </script>";
} else {
    // Error message with SweetAlert
    echo "<script>
        window.onload = function() {
            Swal.fire({
                title: 'Error!',
                text: 'No full scholarship applicants found.',
                icon: 'error',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = 'fullscholar.php';
            });
        };
    </script>";
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
