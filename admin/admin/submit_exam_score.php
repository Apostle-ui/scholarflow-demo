<?php
// Connect to the database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "scholarship_portal";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $applicant_id = $_POST['applicant_id'];
    $deserve_scholarship = $_POST['deserve_scholarship'];
    $financial_need = $_POST['financial_need'];
    $academic_goals = $_POST['academic_goals'];
    $future_impact = $_POST['future_impact'];
    $community_impact = $_POST['community_impact'];

    // Check if the applicant already exists in the table
    $check_query = "SELECT 1 FROM applicant_scores WHERE applicant_id = ?";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param("i", $applicant_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Update exam scores only
        $update_query = "UPDATE applicant_scores 
                         SET deserve_scholarship = ?, financial_need = ?, academic_goals = ?, future_impact = ?, community_impact = ?
                         WHERE applicant_id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("sssssi", $deserve_scholarship, $financial_need, $academic_goals, $future_impact, $community_impact, $applicant_id);

        if ($stmt->execute()) {
            $status = 'updated';
        } else {
            $status = 'error';
        }
    } else {
        // Insert a new record with exam scores
        $insert_query = "INSERT INTO applicant_scores (applicant_id, deserve_scholarship, financial_need, academic_goals, future_impact, community_impact)
                         VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_query);
        $stmt->bind_param("isssss", $applicant_id, $deserve_scholarship, $financial_need, $academic_goals, $future_impact, $community_impact);

        if ($stmt->execute()) {
            $status = 'inserted';
        } else {
            $status = 'error';
        }
    }

    $stmt->close();
    $conn->close();
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
    <script>
        // Ensure the PHP variable is safely passed into JavaScript
        var status = "<?php echo $status; ?>"; // Get the status from PHP

        // Display appropriate SweetAlert based on status
        if (status === 'inserted' || status === 'updated') {
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: 'Interview Scores submitted successfully.',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = 'eligibleapplicants.php'; // Redirect after clicking OK
            });
        } else if (status === 'error') {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'An error occurred while submitting the interview scores. Please try again.',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = 'eligibleapplicants.php'; // Redirect after clicking OK
            });
        }
    </script>
</body>
</html>
