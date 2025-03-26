<?php

session_start();
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "scholarship_portal";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$editorName = isset($_SESSION['user_Organizer']['name']) ? $_SESSION['user_Organizer']['name'] : null; // Get the role from session

// Log Event Function
function logEvent($conn, $editorName, $editedAccountName, $editedAccountUsername, $action, $status) {
    $sql = "INSERT INTO event_logs (editor_name, edited_account_name, edited_account_username, action, event_time, status) 
            VALUES (?, ?, ?, ?, NOW(), ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $editorName, $editedAccountName, $editedAccountUsername, $action, $status);
    $stmt->execute();
    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstname = $_POST['firstname'];
    $middlename = $_POST['middlename'];
    $lastname = $_POST['lastname'];
    $gender = $_POST['gender'];
    $birthdate = $_POST['birthdate'];
    $email = $_POST['email'];
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
    $target_dir = "../../php/uploads/";
    $certificate_registration = $_FILES['certificate']['name'];
    $school_identification = $_FILES['school-identification']['name'];
    $certificate_target = $target_dir . basename($certificate_registration);
    $school_id_target = $target_dir . basename($school_identification);

    if (!move_uploaded_file($_FILES['certificate']['tmp_name'], $certificate_target)) {
        die("Error moving uploaded Certificate of Registration.");
    }

    if (!move_uploaded_file($_FILES['school-identification']['tmp_name'], $school_id_target)) {
        die("Error moving uploaded School Identification.");
    }

    $sql1 = "INSERT INTO applicant_demographic (firstname, middlename, lastname, gender, birthdate, email, contact_number, province, city, barangay, street) 
             VALUES ('$firstname', '$middlename', '$lastname', '$gender', '$birthdate', '$email', '$contact_number', '$province', '$city', '$barangay', '$street')";
    
    if ($conn->query($sql1) === TRUE) {
        $applicant_id = $conn->insert_id;

        $sql2 = "INSERT INTO applicant_parent (applicant_parent_id, mother_firstname, mother_middlename, mother_lastname, mother_contact_number, mother_birthdate, father_firstname, father_middlename, father_lastname, father_contact_number, father_birthdate) 
                 VALUES ('$applicant_id', '$mother_firstname', '$mother_middlename', '$mother_lastname', '$mother_contact_number', '$mother_birthdate', '$father_firstname', '$father_middlename', '$father_lastname', '$father_contact_number', '$father_birthdate')";
        
        if ($conn->query($sql2) === TRUE) {
            $sql3 = "INSERT INTO applicant_school_file (applicant_school_file_id, school_level, year_level, school_name, certificate_registration, school_identification) 
                     VALUES ('$applicant_id', '$school_level', '$year_level', '$school_name', '$certificate_registration', '$school_identification')";
            
            if ($conn->query($sql3) === TRUE) {
                // Log event on successful addition

                $editedAccountName = "$firstname $middlename $lastname"; // Applicant's full name
                $editedAccountUsername = $email; // Applicant's email
                $action = "Added an applicant";
                $status = "success";
                logEvent($conn, $editorName, $editedAccountName, $editedAccountUsername, $action, $status);
                
                header("location: applicants.php");
            } else {
                echo "Error: " . $sql3 . "<br>" . $conn->error;
            }
        } else {
            echo "Error: " . $sql2 . "<br>" . $conn->error;
        }
    } else {
        echo "Error: " . $sql1 . "<br>" . $conn->error;
    }
}

$conn->close();
?>
