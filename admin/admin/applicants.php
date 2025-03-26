<?php
// Start the session
session_start();

$userRole = isset($_SESSION['user_Organizer']['role']) ? $_SESSION['user_Organizer']['role'] : null;
// Get the role from session, default to empty string if not set

// Check if user role exists in session, else redirect to login
if ($userRole == '') {
    header("Location: login.php");
    exit();
}

$editorName = isset($_SESSION['user_Organizer']['name']) ? $_SESSION['user_Organizer']['name'] : null;

// Connect to the database
$servername = "localhost";
$username = "root";  // Replace with your DB username
$password = "";      // Replace with your DB password
$dbname = "scholarship_portal";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function logEvent($conn, $editorName, $editedAccountName, $editedAccountUsername, $action, $status) {
    $sql_log = "INSERT INTO event_logs (editor_name, edited_account_name, edited_account_username, action, status) 
                VALUES (?, ?, ?, ?, ?)";
    $stmt_log = $conn->prepare($sql_log);
    $stmt_log->bind_param("sssss", $editorName, $editedAccountName, $editedAccountUsername, $action, $status);
    $stmt_log->execute();
    $stmt_log->close();
}

// Function to generate detailed action logs for updated fields
function generateChangeLogs($existingData, $newData) {
    $logs = [];
    foreach ($newData as $field => $newValue) {
        $oldValue = isset($existingData[$field]) ? $existingData[$field] : null;
        if ($oldValue !== $newValue) {
            $logs[] = "Changed $field from '{$oldValue}' to '{$newValue}'";
        }
    }
    return $logs;
}


// Process the form data when the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // File uploads
$certificate_file = isset($_FILES['certificate_upload']['name']) ? $_FILES['certificate_upload']['name'] : null;
$school_id_file = isset($_FILES['school_id_upload']['name']) ? $_FILES['school_id_upload']['name'] : null;

// Applicant form data
$applicant_id = isset($_POST['applicant_id']) ? $_POST['applicant_id'] : null;
$firstname = isset($_POST['firstname']) ? $_POST['firstname'] : '';
$middlename = isset($_POST['middlename']) ? $_POST['middlename'] : '';
$lastname = isset($_POST['lastname']) ? $_POST['lastname'] : '';
$gender = isset($_POST['gender']) ? $_POST['gender'] : '';
$birthdate = isset($_POST['birthdate']) ? $_POST['birthdate'] : '';
$email = isset($_POST['email']) ? $_POST['email'] : '';
$contact_number = isset($_POST['contact_number']) ? $_POST['contact_number'] : '';
$street = isset($_POST['street']) ? $_POST['street'] : '';

// Parent info
$mother_firstname = isset($_POST['mother_firstname']) ? $_POST['mother_firstname'] : '';
$mother_middlename = isset($_POST['mother_middlename']) ? $_POST['mother_middlename'] : '';
$mother_lastname = isset($_POST['mother_lastname']) ? $_POST['mother_lastname'] : '';
$mother_contact_number = isset($_POST['mother_contact_number']) ? $_POST['mother_contact_number'] : '';
$mother_birthdate = isset($_POST['mother_birthdate']) ? $_POST['mother_birthdate'] : '';

$father_firstname = isset($_POST['father_firstname']) ? $_POST['father_firstname'] : '';
$father_middlename = isset($_POST['father_middlename']) ? $_POST['father_middlename'] : '';
$father_lastname = isset($_POST['father_lastname']) ? $_POST['father_lastname'] : '';
$father_contact_number = isset($_POST['father_contact_number']) ? $_POST['father_contact_number'] : '';
$father_birthdate = isset($_POST['father_birthdate']) ? $_POST['father_birthdate'] : '';

// School info
$school_level = isset($_POST['school_level']) ? $_POST['school_level'] : '';
$year_level = isset($_POST['year_level']) ? $_POST['year_level'] : '';
$school_name = isset($_POST['school_name']) ? $_POST['school_name'] : '';

// Define the correct directory for uploads
$upload_dir = '../../php/uploads/';

// Check if the directory exists, if not, create it
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Handling file uploads
if (isset($_FILES['certificate_upload']) && $_FILES['certificate_upload']['error'] == 0) {
    // Get the original file name
    $certificate_file_name = basename($_FILES['certificate_upload']['name']);

    // Generate a unique ID but keep the original file name
    $unique_id = uniqid();  // Unique ID generated for each file
    $certificate_upload_path = $upload_dir . $unique_id . '_' . $certificate_file_name; // Store file with unique ID in the path

    // Move the uploaded file to the specified directory
    if (move_uploaded_file($_FILES['certificate_upload']['tmp_name'], $certificate_upload_path)) {
        // Store the original file name in the database
        $certificate_file = $certificate_file_name; // Store original file name (no unique ID in it)
    } else {
        echo "Error moving uploaded Certificate file.";
    }
}

if (isset($_FILES['school_id_upload']) && $_FILES['school_id_upload']['error'] == 0) {
    // Get the original file name
    $school_id_file_name = basename($_FILES['school_id_upload']['name']);

    // Generate a unique ID but keep the original file name
    $unique_id = uniqid();  // Unique ID generated for each file
    $school_id_upload_path = $upload_dir . $unique_id . '_' . $school_id_file_name; // Store file with unique ID in the path

    // Move the uploaded file to the specified directory
    if (move_uploaded_file($_FILES['school_id_upload']['tmp_name'], $school_id_upload_path)) {
        // Store the original file name in the database
        $school_id_file = $school_id_file_name; // Store original file name (no unique ID in it)
    } else {
        echo "Error moving uploaded School ID file.";
    }
}


      // Log demographic update
if ($applicant_id && (!empty($firstname) || !empty($lastname))) {
    $sql_check_demographic = "SELECT firstname, middlename, lastname, gender, birthdate, email, contact_number, street FROM applicant_demographic WHERE applicant_id=?";
    $stmt_check_demographic = $conn->prepare($sql_check_demographic);
    $stmt_check_demographic->bind_param("i", $applicant_id);
    $stmt_check_demographic->execute();
    $result_demographic = $stmt_check_demographic->get_result();
    $existing_demographic = $result_demographic->fetch_assoc();
    $stmt_check_demographic->close();

    $demographic_changes = [];

    foreach ([
        'firstname', 'middlename', 'lastname', 'gender', 'birthdate', 'email', 'contact_number', 'street'
    ] as $field) {
        if ($existing_demographic[$field] !== $$field) {
            $demographic_changes[] = "Changed $field from '{$existing_demographic[$field]}' to '{$$field}'";
        }
    }

    if (!empty($demographic_changes)) {
        $sql = "UPDATE applicant_demographic 
                SET firstname=?, middlename=?, lastname=?, gender=?, birthdate=?, email=?, contact_number=?, street=? 
                WHERE applicant_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssssi", $firstname, $middlename, $lastname, $gender, $birthdate, $email, $contact_number, $street, $applicant_id);
        $stmt->execute();
        $stmt->close();

        // Log the changes
        foreach ($demographic_changes as $change) {
            logEvent($conn, $editorName, "$firstname $lastname", $email, $change, "success");
        }
    }
}

// Log parent update
if (!empty($mother_firstname) || !empty($mother_lastname) || !empty($father_firstname) || !empty($father_lastname)) {
    $sql_check_parent = "SELECT mother_firstname, mother_middlename, mother_lastname, mother_contact_number, mother_birthdate, 
                        father_firstname, father_middlename, father_lastname, father_contact_number, father_birthdate 
                        FROM applicant_parent WHERE applicant_parent_id=?";
    $stmt_check_parent = $conn->prepare($sql_check_parent);
    $stmt_check_parent->bind_param("i", $applicant_id);
    $stmt_check_parent->execute();
    $result_parent = $stmt_check_parent->get_result();
    $existing_parent = $result_parent->fetch_assoc();
    $stmt_check_parent->close();

    $parent_changes = [];

    foreach ([
        'mother_firstname', 'mother_middlename', 'mother_lastname', 'mother_contact_number', 'mother_birthdate',
        'father_firstname', 'father_middlename', 'father_lastname', 'father_contact_number', 'father_birthdate'
    ] as $field) {
        if ($existing_parent[$field] !== $$field) {
            $parent_changes[] = "Changed $field from '{$existing_parent[$field]}' to '{$$field}'";
        }
    }

    if (!empty($parent_changes)) {
        $sql_parent = "UPDATE applicant_parent 
            SET mother_firstname=?, mother_middlename=?, mother_lastname=?, mother_contact_number=?, mother_birthdate=?, 
                father_firstname=?, father_middlename=?, father_lastname=?, father_contact_number=?, father_birthdate=? 
            WHERE applicant_parent_id=?";
        $stmt_parent = $conn->prepare($sql_parent);
        $stmt_parent->bind_param("ssssssssssi", $mother_firstname, $mother_middlename, $mother_lastname, $mother_contact_number, $mother_birthdate, 
            $father_firstname, $father_middlename, $father_lastname, $father_contact_number, $father_birthdate, $applicant_id);
        $stmt_parent->execute();
        $stmt_parent->close();

        // Log the changes
        foreach ($parent_changes as $change) {
            logEvent($conn, $editorName, "$firstname $lastname", $email, $change, "success");
        }
    }
}

// Update school files in the database
if (!empty($school_level) || !empty($school_name) || !empty($certificate_file) || !empty($school_id_file)) {
    $sql_get_files = "SELECT school_level, year_level, school_name, certificate_registration, school_identification FROM applicant_school_file WHERE applicant_school_file_id=?";
    $stmt_get_files = $conn->prepare($sql_get_files);
    $stmt_get_files->bind_param("i", $applicant_id);
    $stmt_get_files->execute();
    $result_get_files = $stmt_get_files->get_result();
    $existing_school = $result_get_files->fetch_assoc();
    $stmt_get_files->close();

    $final_certificate_file = !empty($certificate_file) ? $certificate_file : $existing_school['certificate_registration'];
    $final_school_id_file = !empty($school_id_file) ? $school_id_file : $existing_school['school_identification'];

    $school_changes = [];

    foreach ([
        'school_level', 'year_level', 'school_name'
    ] as $field) {
        if ($existing_school[$field] !== $$field) {
            $school_changes[] = "Changed $field from '{$existing_school[$field]}' to '{$$field}'";
        }
    }

    if ($existing_school['certificate_registration'] !== $final_certificate_file) {
        $school_changes[] = "Changed certificate_registration from '{$existing_school['certificate_registration']}' to '$final_certificate_file'";
    }

    if ($existing_school['school_identification'] !== $final_school_id_file) {
        $school_changes[] = "Changed school_identification from '{$existing_school['school_identification']}' to '$final_school_id_file'";
    }

    if (!empty($school_changes)) {
        $sql_school = "UPDATE applicant_school_file 
            SET school_level=?, year_level=?, school_name=?, certificate_registration=?, school_identification=? 
            WHERE applicant_school_file_id=?";
        $stmt_school = $conn->prepare($sql_school);
        $stmt_school->bind_param("sssssi", $school_level, $year_level, $school_name, $final_certificate_file, $final_school_id_file, $applicant_id);
        $stmt_school->execute();
        $stmt_school->close();

        // Log the changes
        foreach ($school_changes as $change) {
            logEvent($conn, $editorName, "$firstname $lastname", $email, $change, "success");
        }
    }
}



// Get the admin's username from the session, with a fallback to 'unknown' if not set
$admin_username = isset($_SESSION['username']) ? $_SESSION['username'] : 'unknown';

// Process applicant status (accept or reject)
if (isset($_POST['action'])) {
    $action = $_POST['action']; // 'accept' or 'reject'
    $status = ($action === 'accept') ? 'eligible' : 'rejected';
    $applicant_id = $_POST['applicant_id']; // Assuming the applicant ID is sent via POST

    // Check if applicant exists
    $sql_check = "SELECT * FROM applicant_demographic WHERE applicant_id=?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("i", $applicant_id);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check && $result_check->num_rows > 0) {
        // Fetch all applicant data
        $sql_fetch = "SELECT 
            ad.applicant_id, ad.firstname, ad.middlename, ad.lastname, ad.gender, ad.birthdate, ad.email, ad.contact_number, ad.street,
            ap.mother_firstname, ap.mother_middlename, ap.mother_lastname, ap.mother_contact_number, ap.mother_birthdate,
            ap.father_firstname, ap.father_middlename, ap.father_lastname, ap.father_contact_number, ap.father_birthdate,
            asf.school_level, asf.year_level, asf.school_name, asf.certificate_registration, asf.school_identification
        FROM applicant_demographic ad
        LEFT JOIN applicant_parent ap ON ad.applicant_id = ap.applicant_parent_id
        LEFT JOIN applicant_school_file asf ON ad.applicant_id = asf.applicant_school_file_id
        WHERE ad.applicant_id=?";
        $stmt_fetch = $conn->prepare($sql_fetch);
        $stmt_fetch->bind_param("i", $applicant_id);
        $stmt_fetch->execute();
        $applicant_data = $stmt_fetch->get_result()->fetch_assoc();

        // Insert into the appropriate table based on the status
        $table_name = $status === 'eligible' ? 'eligible_applicants_tbl' : 'rejected_applicants_tbl';
        $column_name = $status === 'eligible' ? 'accepted_by' : 'rejected_by';

        if ($status === 'eligible') {
            $sql_insert = "INSERT INTO $table_name (
                applicant_id, firstname, middlename, lastname, gender, birthdate, email, contact_number, street,
                mother_firstname, mother_middlename, mother_lastname, mother_contact_number, mother_birthdate,
                father_firstname, father_middlename, father_lastname, father_contact_number, father_birthdate,
                school_level, year_level, school_name, certificate_registration, school_identification, $column_name, accepted_time
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        } else {
            $sql_insert = "INSERT INTO $table_name (
                applicant_id, firstname, middlename, lastname, gender, birthdate, email, contact_number, street,
                mother_firstname, mother_middlename, mother_lastname, mother_contact_number, mother_birthdate,
                father_firstname, father_middlename, father_lastname, father_contact_number, father_birthdate,
                school_level, year_level, school_name, certificate_registration, school_identification, $column_name, rejected_time
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        }

        // Prepare and execute the query
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param(
            "issssssssssssssssssssssss", 
            $applicant_data['applicant_id'], $applicant_data['firstname'], $applicant_data['middlename'], $applicant_data['lastname'],
            $applicant_data['gender'], $applicant_data['birthdate'], $applicant_data['email'],
            $applicant_data['contact_number'], $applicant_data['street'],
            $applicant_data['mother_firstname'], $applicant_data['mother_middlename'], $applicant_data['mother_lastname'],
            $applicant_data['mother_contact_number'], $applicant_data['mother_birthdate'],
            $applicant_data['father_firstname'], $applicant_data['father_middlename'], $applicant_data['father_lastname'],
            $applicant_data['father_contact_number'], $applicant_data['father_birthdate'],
            $applicant_data['school_level'], $applicant_data['year_level'], $applicant_data['school_name'],
            $applicant_data['certificate_registration'], $applicant_data['school_identification'],
            $admin_username
        );
        $stmt_insert->execute();
        $stmt_insert->close();

       // Log the event with a specific message based on the action
        $editedAccountName = $applicant_data['firstname'] . " " . $applicant_data['lastname'];
        $editedAccountUsername = $applicant_data['email']; // Assuming the email serves as the username

        // Determine the action message
        $actionMessage = ($action === 'accept') 
            ? "Marked the Applicant as Eligible" 
            : "Marked the Applicant as Rejected";

        // Log the event
        logEvent($conn, $editorName, $editedAccountName, $editedAccountUsername, $actionMessage, "success");

        // Delete the applicant from the tables after moving data
        $sql_delete_demographic = "DELETE FROM applicant_demographic WHERE applicant_id=?";
        $sql_delete_parent = "DELETE FROM applicant_parent WHERE applicant_parent_id=?";
        $sql_delete_school = "DELETE FROM applicant_school_file WHERE applicant_school_file_id=?";
        $stmt_delete_demographic = $conn->prepare($sql_delete_demographic);
        $stmt_delete_parent = $conn->prepare($sql_delete_parent);
        $stmt_delete_school = $conn->prepare($sql_delete_school);

        $stmt_delete_demographic->bind_param("i", $applicant_id);
        $stmt_delete_parent->bind_param("i", $applicant_id);
        $stmt_delete_school->bind_param("i", $applicant_id);

        $stmt_delete_demographic->execute();
        $stmt_delete_parent->execute();
        $stmt_delete_school->execute();

        $stmt_delete_demographic->close();
        $stmt_delete_parent->close();
        $stmt_delete_school->close();
    }
}
           
}


// Check for deletion
if (isset($_GET['delete_id'])) {
    $applicant_id = $_GET['delete_id'];
    $editorName = $_SESSION['user_Organizer']['name']; // Assuming organizer name is stored in the session
    $deleteSuccess = true;

    // Retrieve applicant name and email before deletion
    $sql = "SELECT CONCAT(firstname, ' ', middlename, ' ', lastname) AS full_name, email FROM applicant_demographic WHERE applicant_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $applicant_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $applicantName = "Unknown"; // Default to "Unknown" if no name is found
    $applicantEmail = "Unknown"; // Default to "Unknown" if no email is found
    if ($row = $result->fetch_assoc()) {
        $applicantName = $row['full_name'];
        $applicantEmail = $row['email'];
    }
    $stmt->close();

    // Delete from applicant_demographic
    $sql = "DELETE FROM applicant_demographic WHERE applicant_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $applicant_id);
    if (!$stmt->execute()) {
        $deleteSuccess = false;
    }

    // Delete from applicant_parent
    $sql = "DELETE FROM applicant_parent WHERE applicant_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $applicant_id);
    if (!$stmt->execute()) {
        $deleteSuccess = false;
    }

    // Delete from applicant_school_file
    $sql = "DELETE FROM applicant_school_file WHERE applicant_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $applicant_id);
    if (!$stmt->execute()) {
        $deleteSuccess = false;
    }

    // Log the event once based on the overall success of the deletion
    if ($deleteSuccess) {
        logEvent($conn, $editorName, $applicantName, $applicantEmail, "Deleted a Data", "success");
    } else {
        logEvent($conn, $editorName, $applicantName, $applicantEmail, "Failed to delete a Data", "error");
    }

    // Close the statement
    $stmt->close();



// Redirect to avoid resubmission
header("Location: applicants.php");
exit;
}

// Query to fetch applicant demographic data with parent and school info
$sql = "SELECT ad.applicant_id, ad.firstname, ad.middlename, ad.lastname, ad.gender, ad.birthdate, ad.email, ad.contact_number, ad.street,
ap.mother_firstname, ap.mother_middlename, ap.mother_lastname, ap.mother_contact_number, ap.mother_birthdate,
ap.father_firstname, ap.father_middlename, ap.father_lastname, ap.father_contact_number, ap.father_birthdate,
asf.school_level, asf.year_level, asf.school_name, asf.certificate_registration, asf.school_identification
FROM applicant_demographic ad
LEFT JOIN applicant_parent ap ON ad.applicant_id = ap.applicant_parent_id
LEFT JOIN applicant_school_file asf ON ad.applicant_id = asf.applicant_school_file_id";


$result = $conn->query($sql);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>Applicants</title>
    <link rel="icon" sizes="180x180" href="img/favicon.ico">
    <link rel="icon" type="image/png" sizes="32x32" href="img/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="img/favicon-16x16.png">
    <link rel="manifest" href="img/site.webmanifest">
</head>
<body>
<!-------------TO REMOVE THE FLASHY THING WHEN IN DARK MODE---------------->
<script>
  // Check if dark mode was previously enabled
  if (localStorage.getItem('darkMode') === 'enabled') {
    document.documentElement.classList.add('dark'); // Apply dark mode to the root element
  }
</script>
<!-------------TO REMOVE THE FLASHY THING WHEN IN DARK MODE---------------->

    <!-- SIDEBAR -->
		<section id="sidebar">
        <a href="#" class="brand">
            <img src="img/logo.png" alt="logo">
            <h1 class="text">cholarFlow</h1>
        </a>
        <ul class="side-menu top">
            <!-- Always show dashboard link -->
            <li>
                <a href="index.php">
                    <i class='bx bxs-dashboard'></i>
                    <span class="text">Dashboard</span>
                </a>
            </li>

            <!-- Admin can see Accounts, Organizer cannot -->
            <?php if ($userRole == 'Admin') { ?>
                <li>
                    <a href="accounts.php">
                        <i class='bx bxs-user'></i>
                        <span class="text">Accounts</span>
                    </a>
                </li>
            <?php } ?>

            <!-- Admin cannot see any other sections -->
            <?php if ($userRole == 'Admin') { ?>
                <!-- No dropdowns for Admin, only Dashboard and Accounts -->
            <?php } ?>

            <!-- Organizer can see all except Accounts -->
            <?php if ($userRole == 'Organizer') { ?>
                <!-- Applicants and Scholars accessible for Organizer -->
                <li class="active">
                    <a href="#" class="dropdown-toggle">
                        <i class='bx bxs-user'></i>
                        <span class="text">Applicants</span>
                        <i class='bx bxs-chevron-down toggle-icon'></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="applicants.php">
                                <i class='bx bxs-user-badge'></i>
                                <span class="text">List of Applicants</span>
                            </a>
                        </li>
                        <li>
                            <a href="eligibleapplicants.php">
                                <i class='bx bxs-user-check'></i>
                                <span class="text">Eligible Applicants</span>
                            </a>
                        </li>
                        <li>
                            <a href="rejectedapplicants.php">
                                <i class='bx bxs-user-x'></i>
                                <span class="text">Rejected Applicants</span>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle">
                        <i class='bx bxs-group'></i>
                        <span class="text">Scholars</span>
                        <i class='bx bxs-chevron-down toggle-icon'></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="fullscholar.php">
                                <i class='bx bxs-group'></i>
                                <span class="text">Full Scholar</span>
                            </a>
                        </li>
                        <li>
                            <a href="halfscholar.php">
                                <i class='bx bxs-group'></i>
                                <span class="text">Half Scholar</span>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle">
                        <i class='bx bxs-user-badge'></i>
                        <span class="text">Audit Trail</span>
                        <i class='bx bxs-chevron-down toggle-icon'></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="userInformation.php">
                                <i class='bx bxs-user-badge'></i>
                                <span class="text">User Information</span>
                            </a>
                        </li>
                        <li>
                            <a href="userLastLogin.php">
                                <i class='bx bxs-user-badge'></i>
                                <span class="text">Last Login Information</span>
                            </a>
                        </li>
                        <li>
                            <a href="userEventLogs.php">
                                <i class='bx bxs-user-badge'></i>
                                <span class="text">Event Logs</span>
                            </a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="notification.php">
                        <i class='bx bxs-bell'></i>
                        <span class="text">Notifications</span>
                    </a>
                </li>
            <?php } ?>
        </ul>
        
        <!-- Settings and Logout -->
        <ul class="side-menu">
            <li>
                <a href="#">
                    <i class='bx bxs-cog'></i>
                    <span class="text">Settings</span>
                </a>
            </li>
            <li>
            <a href="../../logout.php" class="logout">
            <i class='bx bxs-log-out-circle'></i>
                    <span class="text">Logout</span>
                </a>
            </li>
        </ul>
    </section>
    <!-- SIDEBAR -->

    <!-- CONTENT -->
    <section id="content">
        <!-- NAVBAR -->
        <nav>
            <i class='bx bx-menu'></i>
            <a href="#" class="nav-link">Categories</a>
            <form action="#">
                <div class="form-input">
                    <input type="search" placeholder="Search...">
                    <button type="submit" class="search-btn"><i class='bx bx-search'></i></button>
                </div>
            </form>
            <input type="checkbox" id="switch-mode" hidden>
            <label for="switch-mode" class="switch-mode"></label>
            <a href="#" class="profile">
                <img src="img/profile.jpg">
            </a>
        </nav>
        <!-- NAVBAR -->

        <!-- MAIN -->
        <main>
            <div class="head-title">
                <div class="left">
                    <h1>Applicants</h1>
                </div>

                <div style="display: flex; align-items: center; margin-left: auto; margin-right: 15px;">
                    <span style="margin-right: 8px; font-weight: bold;">Auto Eligible</span>
                    <label class="switch-mode">
                        <input type="checkbox" id="auto-eligible-switch" style="opacity: 0; width: 0; height: 0;">
                        <span class="slider"></span>
                    </label>
                </div>
                
                <form action="download_all_files.php" onsubmit="return applicants_confirmDownload(event);" method="POST">
                    <button type="submit" class="btn-download">
                        <i class='bx bxs-download'></i>
                        <span class="text">Download Files</span>
                    </button>
                </form>

                <form action="delete_all_applicants.php" method="POST" onsubmit="return applicants_confirmDeletion(event);">
                    <button type="submit" class="btn-download">
                        <i class='bx bxs-trash'></i>
                        <span class="text">Delete All Applicants</span>
                    </button>
                </form>

                <form action="download_applicants.php" method="POST" onsubmit="return applicants_confirmApplicantDownload(event);">
                    <button type="submit" class="btn-download">
                        <i class='bx bxs-download'></i>
                        <span class="text">Download All Applicants</span>
                    </button>
                </form>

            </div>
            

        <div class="table-data">
        <div class="order">
        <div class="head">
            <h3>Applicants</h3>
        </div>
        <?php if (isset($message)): ?>
            <div class="alert alert-danger"><?= $message; ?></div> 
        <?php endif; ?>
        <table>
            <thead> 
                <tr>
                    <th>ID</th>
                    <th>First Name</th>
                    <th>Middle Name</th>
                    <th>Last Name</th>
                    <th>Gender</th>
                    <th>Birthdate</th>
                    <th>Update and Delete</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                       
                                <td>{$row['applicant_id']}</td>
                                <td>{$row['firstname']}</td>
                                <td>{$row['middlename']}</td>
                                <td>{$row['lastname']}</td>
                                <td>{$row['gender']}</td>
                                <td>{$row['birthdate']}</td>
                                <td>

                                    <a href='#' class='edit-button' title='Edit Applicant' onclick='openApplicantEditModal(
                                        {$row['applicant_id']}, 
                                        \"{$row['firstname']}\", 
                                        \"{$row['middlename']}\", 
                                        \"{$row['lastname']}\", 
                                        \"{$row['gender']}\", 
                                        \"{$row['birthdate']}\",
                                        \"{$row['email']}\",
                                        \"{$row['contact_number']}\",
                                        \"{$row['street']}\",
                                        \"{$row['mother_firstname']}\", 
                                        \"{$row['mother_middlename']}\", 
                                        \"{$row['mother_lastname']}\", 
                                        \"{$row['mother_contact_number']}\", 
                                        \"{$row['mother_birthdate']}\",
                                        \"{$row['father_firstname']}\", 
                                        \"{$row['father_middlename']}\", 
                                        \"{$row['father_lastname']}\", 
                                        \"{$row['father_contact_number']}\", 
                                        \"{$row['father_birthdate']}\",
                                        \"{$row['school_level']}\", 
                                        \"{$row['year_level']}\", 
                                        \"{$row['school_name']}\", 
                                        \"{$row['certificate_registration']}\", 
                                        \"{$row['school_identification']}\"
                                    )'><i class='bx bx-edit'></i></a>
                                    <a href='#' class='delete-button' title='Delete Applicant' onclick='openApplicantDeleteModal({$row['applicant_id']})'>
                                    <i class='bx bx-trash'></i>
                                    </a> 
                                    
                                </td>

                                <td>
                                 <div class='action-buttons'>
                                        <!-- Accept Button-->
                                        <form action='applicants.php' method='POST' style='display: inline;'>
                                            <input type='hidden' name='applicant_id' value='{$row['applicant_id']}'>
                                            <input type='hidden' name='action' value='accept'>
                                            <button type='button' class='accept-button' title='Accept Applicant' onclick='confirmAccept(event)'>
                                                <i class='bx bxs-check-circle'></i> 
                                            </button>
                                        </form>


                                        <!-- Reject Button -->
                                        <form action='applicants.php' method='POST' style='display: inline;'>
                                            <input type='hidden' name='applicant_id' value='{$row['applicant_id']}'>
                                            <input type='hidden' name='action' value='reject'>
                                            <button type='button' class='reject-button' title='Reject Applicant' onclick='confirmReject(event)'>
                                            <i class='bx bxs-x-circle'></i> 
                                            </button>
                                        </form>

                                        <!-- Download Button -->
                                        <form action='download_file.php' style='display: inline;'>
                                            <input type='hidden' name='applicant_id' value='{$row['applicant_id']}'>
                                            <button type='button' class='download-button' title='Download files uploaded by the applicant' onclick='confirmDownload(event)'>
                                            <i class='bx bxs-download'></i> 
                                            </button>
                                        </form>

                                        <!-- Scan Button -->
                                        <form action='scan_file.php' method='POST' style='display: inline;'>
                                            <input type='hidden' name='applicant_id' value='{$row['applicant_id']}'>
                                            <button type='button' class='scan-button' title='Scan files uploaded by the applicant' onclick='confirmScan(event)'>
                                            <i class='bx bx-scan'></i> 
                                            </button>
                                        </form>
                                        
                                    </div>
                                
                                
                                </td>
                                
                            </tr>";
                    }
                } else {
                    echo "<tr><td colspan='7'>No applicants found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
    </div>
        </main>
        <!-- MAIN -->
    </section>
    <!-- CONTENT -->
    <!-- Floating Plus Icon -->
    <div class="floating-icon" onclick="openApplicantAddModal()">
            <i class='bx bxs-user-plus'></i>
    </div>


    <!-- Add Applicant Modal -->
    <div id="addApplicantsModal" class="modal">
		<div class="modal-content">
			<span class="close" onclick="closeApplicantAddModal()">&times;</span>
			<h2>Application Form</h2>
        <form action="addApplicants.php" method="POST" enctype="multipart/form-data">

        <!-- First Form Section (Demographic profile) -->
    <div class="form-section" id="form1">
        <h4>Demographic Profile</h4>
        <div class="name-container">
            <label for="fullname">Full Name:</label>
            <div class="name-fields">
                <div class="name-field">
                    <input type="text" id="firstname" name="firstname" required>
                    <p>First Name</p>
                </div>
                <div class="name-field">
                    <input type="text" id="middlename" name="middlename">
                    <p>Middle Name</p>
                </div>
                <div class="name-field">
                    <input type="text" id="lastname" name="lastname" required>
                    <p>Last Name</p>
                </div>
            </div>
        </div>

        <div class="gender-birthdate-container">
            <label for="gender">Gender:</label>
            <select id="gender" name="gender" required>
                <option value="" disabled selected>Please select</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
            </select>

            <label for="birthdate">Birthdate:</label>
            <input type="date" id="birthdate" name="birthdate" required>
        </div>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>

        <label for="number">Contact Number:</label>
        <input type="text" id="number" name="number" required>

        <div class="address">
            <label for="address">Address:</label>
            <div class="address-fields">
                <div class="address-field">
                    <input type="text" id="province" name="province" value="Metro Manila" required readonly>
                    <p>Province</p>
                </div>
                <div class="address-field">
                    <input type="text" id="city" name="city" value="Muntinlupa" readonly>
                    <p>City</p>
                </div>
                <div class="address-field">
                    <input type="text" id="barangay" name="barangay" value="Alabang" required readonly>
                    <p>Barangay</p>
                </div>
            </div>
            <div class="street-field">
                <input type="text" id="street-number" name="street-number" required>
                <p>Street/House Number/Building</p>
            </div>
        </div>
            <button type="button" class="next-button" onclick="validateForm1()">Next</button>
    </div>
    

        <!-- Second Form Section -->
        <div class="form-section" id="form2" style="display: none;">
            <h4>Parent Information</h4>
            <label for="mother-fullname">Mother's Name:</label>
            <div class="name-fields">
                <div class="name-field">
                    <input type="text" id="mother-firstname" name="mother-firstname" required>
                    <p>First Name</p>
                </div>
                <div class="name-field">
                    <input type="text" id="mother-middlename" name="mother-middlename">
                    <p>Middle Name</p>
                </div>
                <div class="name-field">
                    <input type="text" id="mother-lastname" name="mother-lastname" required>
                    <p>Last Name</p>
                </div>
            </div>

            <label for="mother-contact">Contact Number:</label>
            <input type="text" id="mother-contact" name="mother-contact" required>

            <label for="mother-birthdate">Birthdate:</label>
            <input type="date" id="mother-birthdate" name="mother-birthdate" required>

            <label for="father-fullname">Father's Name:</label>
            <div class="name-fields">
                <div class="name-field">
                    <input type="text" id="father-firstname" name="father-firstname" required>
                    <p>First Name</p>
                </div>
                <div class="name-field">
                    <input type="text" id="father-middlename" name="father-middlename">
                    <p>Middle Name</p>
                </div>
                <div class="name-field">
                    <input type="text" id="father-lastname" name="father-lastname" required>
                    <p>Last Name</p>
                </div>
            </div>

            <label for="father-contact">Contact Number:</label>
            <input type="text" id="father-contact" name="father-contact" required>

            <label for="father-birthdate">Birthdate:</label>
            <input type="date" id="father-birthdate" name="father-birthdate" required>

            <div class="button-container">
                <button type="button" class="next-button" onclick="showNextForm(1)">Back</button>
                <button type="button" class="next-button" onclick="validateForm2()">Next</button>
            </div>
        </div>


        <!-- Third Form Section -->
        <div class="form-section" id="form3" style="display: none;">
            <h4>School Information and Requirements</h4>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="current-school-level">Current School Level:</label>
                    <select id="current-school-level" name="current-school-level" required onchange="updateGradeOptions()">
                        <option value="" disabled selected>Please select</option>
                        <option value="Senior High School">Senior High School</option>
                        <option value="College">College</option>
                    </select>
                </div>
            
                <div class="form-group">
                    <label for="grade-level">Current Grade/Year Level:</label>
                    <select id="grade-level" name="grade-level" required>
                        <option value="" disabled selected>Please select</option>
                    </select>
                </div>
            </div>
            
            <label for="school-name">School Name:</label>
            <input type="text" id="school-name" name="school-name" required>

            <div class="form-row">
                <div class="form-group">
                    <label for="certificate">Certificate of Registration:</label>
                    <input type="file" id="certificate" name="certificate" accept=".png, .jpg, .jpeg, .pdf, .docx" required onchange="updateFileName('certificate')">
                    <div id="certificate-file-name" class="file-name"></div>
                    <label for="certificate" class="label-file">Choose File</label>
                </div>   
                
                <div class="form-group">
                    <label for="school-identification">School ID:</label>
                    <input type="file" id="school-identification" name="school-identification" accept=".png, .jpg, .jpeg, .pdf, .docx" required onchange="updateFileName('school-identification')">
                    <div id="school-identification-file-name" class="file-name"></div>
                    <label for="school-identification" class="label-file">Choose File</label>
                </div>
            </div>

            <div class="button-container">
                <button type="button" class="next-button" onclick="showNextForm(2)">Back</button>
                <button type="submit" class="next-button" onclick="validateForm3()">Submit Application</button>
            </div>

        </div>
        </form>
		</div>
	</div>

    <!-- Edit Applicant Modal -->
<div id="editApplicantsModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeApplicantEditModal()">&times;</span>
        <h2>Edit Applicant</h2>
        <form id="editForm" method="POST" action="applicants.php" enctype="multipart/form-data">
            <input type="hidden" name="applicant_id" id="applicant_id"> <!-- Corrected ID here -->

            <h3>Applicant Information</h3>
            <label for="edit_firstname">First Name:</label>
            <input type="text" name="firstname" id="edit_firstname" required>

            <label for="edit_middlename">Middle Name:</label>
            <input type="text" name="middlename" id="edit_middlename" required>

            <label for="edit_lastname">Last Name:</label>
            <input type="text" name="lastname" id="edit_lastname" required>

            <label for="edit_gender">Gender:</label>
            <select name="gender" id="edit_gender" required>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
            </select>

            <label for="edit_birthdate">Birthdate:</label>
            <input type="date" name="birthdate" id="edit_birthdate" required>

            <label for="edit_email">Email:</label>
            <input type="text" name="email" id="edit_email"  required>

            <label for="edit_contact_number">Contact Number:</label>
            <input type="text" name="contact_number" id="edit_contact_number"  required>

            <label for="edit_street_number">Street Number</label>
            <input type="text" name="street" id="edit_street_number" required>

            <h3>Parent Information</h3>
            <label for="edit_mother_firstname">Mother's First Name:</label>
            <input type="text" name="mother_firstname" id="edit_mother_firstname" required>

            <label for="edit_mother_middlename">Mother's Middle Name:</label>
            <input type="text" name="mother_middlename" id="edit_mother_middlename" required>

            <label for="edit_mother_lastname">Mother's Last Name:</label>
            <input type="text" name="mother_lastname" id="edit_mother_lastname" required>

            <label for="edit_mother_contact_number">Mother's Contact Number:</label>
            <input type="text" name="mother_contact_number" id="edit_mother_contact_number" required>

            <label for="edit_mother_birthdate">Mother's Birthdate:</label>
            <input type="date" name="mother_birthdate" id="edit_mother_birthdate" required>

            <label for="edit_father_firstname">Father's First Name:</label>
            <input type="text" name="father_firstname" id="edit_father_firstname" required>

            <label for="edit_father_middlename">Father's Middle Name:</label>
            <input type="text" name="father_middlename" id="edit_father_middlename" required>

            <label for="edit_father_lastname">Father's Last Name:</label>
            <input type="text" name="father_lastname" id="edit_father_lastname" required>

            <label for="edit_father_contact_number">Father's Contact Number:</label>
            <input type="text" name="father_contact_number" id="edit_father_contact_number" required>

            <label for="edit_father_birthdate">Father's Birthdate:</label>
            <input type="date" name="father_birthdate" id="edit_father_birthdate" required>

            <h3>School Information</h3>

            <label for="edit_school_level">School Level:</label>
            <select name="school_level" id="edit_school_level" required onchange="updateEditGradeOptions()">
                <option value="" disabled selected>Please select</option>
                <option value="Senior High School">Senior High School</option>
                <option value="College">College</option>
            </select>

            <label for="edit_year_level">Year Level:</label>
            <select name="year_level" id="edit_year_level" required>
                <option value="" disabled selected>Please select</option>
            </select>


            <label for="edit_school_name">School Name:</label>
            <input type="text" name="school_name" id="edit_school_name" required>

        <div>
            <label for="edit_certificate_upload">
                Certificate Registration:
                <i class="fa fa-upload" aria-hidden="true" style="cursor: pointer;"></i>
            </label>
            <input type="file" name="certificate_upload" id="edit_certificate_upload" accept=".pdf,.jpg,.png,.docx" style="display: none;" onchange="showFileName('edit_certificate_upload', 'certificateFileName')">
            <span id="certificateFileName" class="file-name"></span> <!-- Display filename -->
        </div>

        <div>
            <label for="edit_school_id_upload">
                School ID:
                <i class="fa fa-upload" aria-hidden="true" style="cursor: pointer;"></i>
            </label>
            <input type="file" name="school_id_upload" id="edit_school_id_upload" accept=".pdf,.jpg,.png,.docx" style="display: none;" onchange="showFileName('edit_school_id_upload', 'schoolIdFileName')">
            <span id="schoolIdFileName" class="file-name"></span> <!-- Display filename -->
        </div>

            <button type="submit" onclick="validateEdit(event)">Update</button>
        </form>
    </div>
</div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <h2>Confirm Deletion</h2>
            <p>Are you sure you want to delete this applicant's information?</p>
            <form id="deleteForm" method="GET">
                <input type="hidden" name="delete_id" id="delete_id">
                <button type="submit">Yes, Delete</button>
                <button type="button" onclick="closeApplicantDeleteModal()">Cancel</button>
            </form>
        </div>
    </div>

	<script src="script.js"></script>


</body>
</html>

<?php
// Close the database connection
$conn->close();
?>