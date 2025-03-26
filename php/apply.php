<?php
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

session_start(); // Start the session to access session variables

// Check if user is logged in as an Applicant
if (
    !isset($_SESSION['user_Applicant']) || 
    !isset($_SESSION['user_Applicant']['email']) || 
    !isset($_SESSION['user_Applicant']['account_id'])
) {
    showModal('Error', 'You need to be logged in as an Applicant to apply.', 'login.html', false);
    exit();
}

// Retrieve email and account ID from the session
$email = isset($_SESSION['user_Applicant']['email']) ? $_SESSION['user_Applicant']['email'] : null;
$useraccounts_id = isset($_SESSION['user_Applicant']['account_id']) ? $_SESSION['user_Applicant']['account_id'] : null;

// Prepare SQL queries for all relevant tables
$sql_check_demographic = "SELECT * FROM applicant_demographic WHERE email = ? LIMIT 1";
$sql_check_eligible = "SELECT * FROM eligible_applicants_tbl WHERE email = ? LIMIT 1";
$sql_check_full = "SELECT * FROM full_scholar_applicant WHERE email = ? LIMIT 1";
$sql_check_half = "SELECT * FROM half_scholar_applicant WHERE email = ? LIMIT 1";

// Prepare statements for each table
$stmt_check_demographic = $conn->prepare($sql_check_demographic);
$stmt_check_demographic->bind_param("s", $email);
$stmt_check_demographic->execute();
$result_check_demographic = $stmt_check_demographic->get_result();

$stmt_check_eligible = $conn->prepare($sql_check_eligible);
$stmt_check_eligible->bind_param("s", $email);
$stmt_check_eligible->execute();
$result_check_eligible = $stmt_check_eligible->get_result();

$stmt_check_full = $conn->prepare($sql_check_full);
$stmt_check_full->bind_param("s", $email);
$stmt_check_full->execute();
$result_check_full = $stmt_check_full->get_result();

$stmt_check_half = $conn->prepare($sql_check_half);
$stmt_check_half->bind_param("s", $email);
$stmt_check_half->execute();
$result_check_half = $stmt_check_half->get_result();

// Check if the email exists in any table
if ($result_check_demographic->num_rows > 0 || $result_check_eligible->num_rows > 0 || $result_check_full->num_rows > 0 || $result_check_half->num_rows > 0) {
    showModal('Error', 'You have already applied for the scholarship, Please wait for further announcements.', false);
    exit();
}

// Retrieve form data
$firstname = $_POST['firstname'];
$middlename = $_POST['middlename'];
$lastname = $_POST['lastname'];
$gender = $_POST['gender'];
$birthdate = $_POST['birthdate'];
$contact_number = $_POST['number'];
$province = $_POST['province'];
$city = $_POST['city'];
$barangay = $_POST['barangay'];
$street = $_POST['street-number'];

$mother_firstname = $_POST['mother-firstname'];
$mother_middlename = $_POST['mother-middlename'];
$mother_lastname = $_POST['mother-lastname'];
$mother_contact_number = $_POST['mother-contact'];
$mother_birthdate = $_POST['mother-birthdate'];

$father_firstname = $_POST['father-firstname'];
$father_middlename = $_POST['father-middlename'];
$father_lastname = $_POST['father-lastname'];
$father_contact_number = $_POST['father-contact'];
$father_birthdate = $_POST['father-birthdate'];

$school_level = $_POST['current-school-level'];
$year_level = $_POST['grade-level'];
$school_name = $_POST['school-name'];

// File upload handling
$target_dir = "uploads/";
if (!is_dir($target_dir)) {
    mkdir($target_dir, 0777, true);
}

$certificate_registration = $_FILES['certificate']['name'];
$school_identification = $_FILES['school-identification']['name'];

$certificate_target = $target_dir . basename($certificate_registration);
$school_id_target = $target_dir . basename($school_identification);

if (!move_uploaded_file($_FILES['certificate']['tmp_name'], $certificate_target)) {
    showModal('Error', 'Error moving uploaded Certificate of Registration.', false);
    exit();
}

if (!move_uploaded_file($_FILES['school-identification']['tmp_name'], $school_id_target)) {
    showModal('Error', 'Error moving uploaded School Identification.', false);
    exit();
}

// Insert data into `applicant_demographic`
$sql1 = "INSERT INTO applicant_demographic (firstname, middlename, lastname, gender, birthdate, contact_number, province, city, barangay, street, email) 
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt1 = $conn->prepare($sql1);
$stmt1->bind_param(
    "sssssssssss",
    $firstname,
    $middlename,
    $lastname,
    $gender,
    $birthdate,
    $contact_number,
    $province,
    $city,
    $barangay,
    $street,
    $email
);

if ($stmt1->execute()) {
    $applicant_id = $stmt1->insert_id;

    // Insert data into `applicant_parent`
    $sql2 = "INSERT INTO applicant_parent (applicant_parent_id, mother_firstname, mother_middlename, mother_lastname, mother_contact_number, mother_birthdate, father_firstname, father_middlename, father_lastname, father_contact_number, father_birthdate) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt2 = $conn->prepare($sql2);
    $stmt2->bind_param(
        "issssssssss",
        $applicant_id,
        $mother_firstname,
        $mother_middlename,
        $mother_lastname,
        $mother_contact_number,
        $mother_birthdate,
        $father_firstname,
        $father_middlename,
        $father_lastname,
        $father_contact_number,
        $father_birthdate
    );

    if ($stmt2->execute()) {
        // Insert data into `applicant_school_file`
        $sql3 = "INSERT INTO applicant_school_file (applicant_school_file_id, school_level, year_level, school_name, certificate_registration, school_identification) 
                 VALUES (?, ?, ?, ?, ?, ?)";
        $stmt3 = $conn->prepare($sql3);
        $stmt3->bind_param(
            "isssss",
            $applicant_id,
            $school_level,
            $year_level,
            $school_name,
            $certificate_registration,
            $school_identification
        );

        if ($stmt3->execute()) {
            showModal('Success', 'Application Submitted Successfully', true);
            exit();
        } else {
            showModal('Error', 'Error inserting into `applicant_school_file`.', false);
            exit();
        }
    } else {
        showModal('Error', 'Error inserting into `applicant_parent`.', false);
        exit();
    }
} else {
    showModal('Error', 'Error inserting into `applicant_demographic`.', false);
    exit();
}

$conn->close();

function showModal($title, $message, $success = false) {
    $buttonAction = $success ? "window.location.href=\"../index.php\"" : "window.location.href=\"../index.php\"";
    $button = "<button type='button' class='btn btn-primary' onclick='$buttonAction'>OK</button>";
    
    echo "
    <!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
        <title>$title</title>
    </head>
    <body>
        <div class='modal fade' tabindex='-1' aria-labelledby='modalLabel' aria-hidden='true'>
            <div class='modal-dialog'>
                <div class='modal-content'>
                    <div class='modal-header'>
                        <h5 class='modal-title'>$title</h5>
                    </div>
                    <div class='modal-body'>
                        <p>$message</p>
                    </div>
                    <div class='modal-footer'>
                        $button
                    </div>
                </div>
            </div>
        </div>

        <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js'></script>
        <script>
            window.onload = function() {
                var modal = new bootstrap.Modal(document.querySelector('.modal'));
                modal.show();

                // Close the modal after 3 seconds and redirect
                setTimeout(function() {
                    modal.hide();
                    window.location.href = '../index.php';
                }, 3000); // 3000ms = 3 seconds
            };
        </script>

    </body>
    </html>";
}
?>
