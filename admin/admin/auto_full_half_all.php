<?php
// Database connection
$servername = "localhost";
$username = "root"; // Replace with your DB username
$password = ""; // Replace with your DB password
$dbname = "scholarship_portal";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Start the session at the top of your script
session_start();

// Get the admin's username from the session, with a fallback to 'unknown' if not set
$admin_username = isset($_SESSION['username']) ? $_SESSION['username'] : 'unknown';

// Initialize status for JavaScript
$status = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Fetch all eligible applicants
    $sql_fetch_all = "SELECT * FROM eligible_applicants_tbl";
    $result = $conn->query($sql_fetch_all);

    // Check if there are any eligible applicants
    if ($result->num_rows > 0) {
        // Process each applicant
        while ($applicant_data = $result->fetch_assoc()) {
            // Fetch the applicant's score
            $applicant_id = $applicant_data['applicant_id'];
            $sql_score = "SELECT total_score FROM applicant_scores WHERE applicant_id=?";
            $stmt_score = $conn->prepare($sql_score);
            $stmt_score->bind_param("i", $applicant_id);
            $stmt_score->execute();
            $score_data = $stmt_score->get_result()->fetch_assoc();
            $stmt_score->close();

            if ($score_data) {
                // Determine the scholarship type based on the score
                $total_score = $score_data['total_score'];
                $status = $total_score > 2.5 ? 'full' : 'half';

                // Decide target table based on status
                $table_name = $status === 'full' ? 'full_scholar_applicant' : 'half_scholar_applicant';
                $column_name = $status === 'full' ? 'accept_full_by' : 'accept_half_by';
                $time = $status === 'full' ? 'accept_full_time' : 'accept_half_time';

                // Insert into the selected table
                $sql_insert = "INSERT INTO $table_name (
                    applicant_id, firstname, middlename, lastname, gender, birthdate, email, contact_number, street,
                    mother_firstname, mother_middlename, mother_lastname, mother_contact_number, mother_birthdate,
                    father_firstname, father_middlename, father_lastname, father_contact_number, father_birthdate,
                    school_level, year_level, school_name, certificate_registration, school_identification, $column_name, $time
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

                $stmt_insert = $conn->prepare($sql_insert);
                $stmt_insert->bind_param(
                    "issssssssssssssssssssssss",
                    $applicant_data['applicant_id'],
                    $applicant_data['firstname'],
                    $applicant_data['middlename'],
                    $applicant_data['lastname'],
                    $applicant_data['gender'],
                    $applicant_data['birthdate'],
                    $applicant_data['email'],
                    $applicant_data['contact_number'],
                    $applicant_data['street'],
                    $applicant_data['mother_firstname'],
                    $applicant_data['mother_middlename'],
                    $applicant_data['mother_lastname'],
                    $applicant_data['mother_contact_number'],
                    $applicant_data['mother_birthdate'],
                    $applicant_data['father_firstname'],
                    $applicant_data['father_middlename'],
                    $applicant_data['father_lastname'],
                    $applicant_data['father_contact_number'],
                    $applicant_data['father_birthdate'],
                    $applicant_data['school_level'],
                    $applicant_data['year_level'],
                    $applicant_data['school_name'],
                    $applicant_data['certificate_registration'],
                    $applicant_data['school_identification'],
                    $admin_username
                );

                if ($stmt_insert->execute()) {
                    // Delete the applicant from eligible_applicants_tbl
                    $sql_delete_eligible = "DELETE FROM eligible_applicants_tbl WHERE applicant_id=?";
                    $stmt_delete_eligible = $conn->prepare($sql_delete_eligible);
                    $stmt_delete_eligible->bind_param("i", $applicant_id);
                    $stmt_delete_eligible->execute();
                    $stmt_delete_eligible->close();
                }
            }
        }

        // After processing all applicants, show a success alert
        echo "<script>
        window.onload = function() {
            Swal.fire({
                icon: 'success',
                title: 'All Eligible Applicants Processed',
                text: 'All applicants have been awarded their scholarships.',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = 'eligibleapplicants.php'; // Redirect to eligible applicants page
            });
        }
        </script>";
    } else {
        echo "No eligible applicants found.";
    }
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
