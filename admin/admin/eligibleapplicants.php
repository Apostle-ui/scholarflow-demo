<?php
// Start the session
session_start();

$userRole = isset($_SESSION['user_Organizer']['role']) ? $_SESSION['user_Organizer']['role'] : null;  // Get the role from session, default to empty string if not set
$editorName = isset($_SESSION['user_Organizer']['name']) ? $_SESSION['user_Organizer']['name'] : null; 

// Check if user role exists in session, else redirect to login
if ($userRole == '') {
    header("Location: login.php");
    exit();
}

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

// Get the admin's username from the session, with a fallback to 'unknown' if not set
$editorName = isset($_SESSION['user_Organizer']['name']) ? $_SESSION['user_Organizer']['name'] : null;  

// Function to log events
function logEvent($conn, $editorName, $editedAccountName, $editedAccountUsername, $action, $status) {
    $sql_log = "INSERT INTO event_logs (editor_name, edited_account_name, edited_account_username, action, status) 
                VALUES (?, ?, ?, ?, ?)";
    $stmt_log = $conn->prepare($sql_log);
    $stmt_log->bind_param("sssss", $editorName, $editedAccountName, $editedAccountUsername, $action, $status);
    $stmt_log->execute();
    $stmt_log->close();
}

// Process the form data when submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (isset($_POST['action'])) {
        $action = $_POST['action']; // 'accept' or 'reject'
        $status = ($action === 'full_scholar') ? 'full' : 'full_scholar';
        $applicant_id = isset($_POST['applicant_id']) ? $_POST['applicant_id'] : null;

        // Validate applicant_id
        if (!$applicant_id) {
            echo "Invalid applicant ID.";
            exit;
        }

        // Fetch applicant data
        $sql_fetch = "SELECT * FROM eligible_applicants_tbl WHERE applicant_id=?";
        $stmt_fetch = $conn->prepare($sql_fetch);
        $stmt_fetch->bind_param("i", $applicant_id);
        $stmt_fetch->execute();
        $applicant_data = $stmt_fetch->get_result()->fetch_assoc();
        $stmt_fetch->close();

        // Check if the applicant data exists
        if ($applicant_data) {
            // Decide target table based on action
            $table_name = $status === 'full' ? 'full_scholar_applicant' : 'half_scholar_applicant';

            // Insert into the selected table
            $sql_insert = "INSERT INTO $table_name (
                applicant_id, firstname, middlename, lastname, gender, birthdate, email, contact_number, street,
                mother_firstname, mother_middlename, mother_lastname, mother_contact_number, mother_birthdate,
                father_firstname, father_middlename, father_lastname, father_contact_number, father_birthdate,
                school_level, year_level, school_name, certificate_registration, school_identification
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt_insert = $conn->prepare($sql_insert);
            $stmt_insert->bind_param(
                "isssssssssssssssssssssss",
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
                $applicant_data['school_identification']
            );

            if ($stmt_insert->execute()) {
                // Log the successful action
                $log_action = $status === 'full' ? 'Marked as Full Scholar' : 'Marked as Half Scholar';
                logEvent($conn, $editorName, $applicant_data['firstname'] . ' ' . $applicant_data['lastname'], $applicant_data['email'], $log_action, 'success');

                // Delete the applicant from eligible_applicants_tbl
                $sql_delete_eligible = "DELETE FROM eligible_applicants_tbl WHERE applicant_id=?";
                $stmt_delete_eligible = $conn->prepare($sql_delete_eligible);
                $stmt_delete_eligible->bind_param("i", $applicant_id);
                $stmt_delete_eligible->execute();
                $stmt_delete_eligible->close();

                echo "<script>
                window.location.href = 'eligibleapplicants.php';
                    </script>";
            } else {
                // Log the failed action
                $log_action = $status === 'full' ? 'Marked as Full Scholar' : 'Marked as Half Scholar';
                logEvent($conn, $admin_username, $applicant_data['firstname'] . ' ' . $applicant_data['lastname'], $applicant_data['email'], $log_action, 'failure');

                echo "Failed to insert data into $table_name: " . $stmt_insert->error;
            }
            $stmt_insert->close();
        } else {
            echo "No applicant data found for the provided ID.";
        }
    }
}

// Fetch eligible applicants
$sql = "SELECT applicant_id, firstname, middlename, lastname, gender, birthdate FROM eligible_applicants_tbl";
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
    <title>Eligible Applicants</title>
    <link rel="icon" sizes="180x180" href="img/favicon.ico">
    <link rel="icon" type="image/png" sizes="32x32" href="img/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="img/favicon-16x16.png">
    <link rel="manifest" href="img/site.webmanifest">
</head>
<body>
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
            <a href="#" class="notification">
                <i class='bx bxs-bell'></i>
                <span class="num">8</span>
            </a>
            <a href="#" class="profile">
                <img src="img/profile.jpg">
            </a>
        </nav>
        <!-- NAVBAR -->

        <!-- MAIN -->
        <main>
            <div class="head-title">
                <div class="left">
                    <h1>Eligible Applicants</h1>
                </div>

                <form id="removeForm" method="POST" action="remove_all_eligible.php">
                    <button type="submit" class="btn-download" onclick="return confirmRemoveAllEligible(event)">
                        <i class='bx bxs-trash'></i>
                        <span class="text">Remove All Eligible</span>  
                    </button>
                </form>

                <form id="downloadForm" method="POST" action="download_eligible_applicants.php">
                    <button type="submit" class="btn-download" onclick="return confirmDownloadEligible(event)">
                        <i class='bx bxs-download'></i>
                        <span class="text">Download All Eligible</span>  
                    </button>
                </form>

                <form id="scanForm" method="POST" action="auto_full_half_all.php">
                    <button type="submit" class="btn-download" onclick="return confirmScanAllEligible(event)">
                        <i class='bx bx-scan'></i>
                        <span class="text">Scan All Eligible</span>  
                    </button>
                </form>

               <!-- Interview Modal -->
                <div class="modal" id="interviewModal" style="display: none;">
                    <div class="modal-content">
                        <span class="close-btn" onclick="closeInterviewModal()">&times;</span>
                        <h2>Score Scholarship Interview</h2>
                        <form id="interviewForm" action="submit_interview_score.php" method="POST">
                            <!-- Hidden input for applicant_id -->
                            <input type="hidden" name="applicant_id" value="">
                            
                            <label for="academic_performance">Academic Performance:</label>
                            <input type="text" id="academic_performance" name="academic_performance" min="0" max="5" required>

                            <label for="motivation">Motivation for the Scholarship:</label>
                            <input type="text" id="motivation" name="motivation" min="0" max="5" required>

                            <label for="community_involvement">Community Involvement:</label>
                            <input type="text" id="community_involvement" name="community_involvement" min="0" max="5" required>

                            <label for="communication_skills">Communication Skills:</label>
                            <input type="text" id="communication_skills" name="communication_skills" min="0" max="5" required>

                            <label for="future_goals">Alignment with Future Goals:</label>
                            <input type="text" id="future_goals" name="future_goals" min="0" max="5" required>

                            <button type="submit">Submit</button>
                        </form>
                    </div>
                </div>

                <!--Exam Modal -->
                <div class="modal" id="examinationModal" style="display: none;">
                    <div class="modal-content">
                    <span class="close-btn" onclick="closeExaminationModal()">&times;</span>
                        <h2>Score Scholarship Examination</h2>
                        <form id="examinationForm" action="submit_exam_score.php" method="POST">
                            <input type="hidden" id="applicant_id" name="applicant_id" value="">

                            <!-- Scoring Fields -->
                            <label for="deserve_scholarship">Why do you deserve this scholarship?</label>
                            <input type="text" id="deserve_scholarship" name="deserve_scholarship" min="0" max="5" required>

                            <label for="financial_need">Explain your financial need for this scholarship</label>
                            <input type="text" id="financial_need" name="financial_need" min="0" max="5" required>

                            <label for="academic_goals">What are your academic goals and how does this scholarship align with them?</label>
                            <input type="text" id="academic_goals" name="academic_goals" min="0" max="5" required>

                            <label for="future_impact">How will receiving this scholarship impact your future?</label>
                            <input type="text" id="future_impact" name="future_impact" min="0" max="5" required>

                            <label for="community_impact">How do you plan to contribute to your community after receiving the scholarship?</label>
                            <input type="text" id="community_impact" name="community_impact" min="0" max="5" required>

                            <button type="submit">Submit</button>
                        </form>
                    </div>
                </div>

                <!-- Edit Score Modal -->
                <div class="modal" id="editScoreModal" style="display: none;">
                    <div class="modal-content">
                        <span class="close-btn" onclick="closeEditScoreModal()">&times;</span>
                        <h2>Edit Scores</h2>
                        <form id="editScoreForm" action="edit_score.php" method="POST">
                            <input type="hidden" id="edit_applicant_id" name="applicant_id" value="">
                            
                            <label for="edit_deserve_scholarship">Why do you deserve this scholarship?</label>
                            <input type="text" id="edit_deserve_scholarship" name="deserve_scholarship" required>

                            <label for="edit_financial_need">Explain your financial need for this scholarship</label>
                            <input type="text" id="edit_financial_need" name="financial_need" required>

                            <label for="edit_academic_goals">What are your academic goals and how does this scholarship align with them?</label>
                            <input type="text" id="edit_academic_goals" name="academic_goals" required>

                            <label for="edit_future_impact">How will receiving this scholarship impact your future?</label>
                            <input type="text" id="edit_future_impact" name="future_impact" required>

                            <label for="edit_community_impact">How do you plan to contribute to your community after receiving the scholarship?</label>
                            <input type="text" id="edit_community_impact" name="community_impact" required>

                            <button type="submit">Update</button>
                        </form>
                    </div>
                </div>


            </div>

            <div class="table-data">
                <div class="order">
                    <div class="head">
                        <h3>Eligible Applicants</h3>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>First Name</th>
                                <th>Middle Name</th>
                                <th>Last Name</th>
                                <th>Gender</th>
                                <th>Birthdate</th>
                                <th>Exam and Interview</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($result->num_rows > 0) {
                                // Output data for each row
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>
                                            <td>" . $row['applicant_id'] . "</td>
                                            <td>" . $row['firstname'] . "</td>
                                            <td>" . $row['middlename'] . "</td>
                                            <td>" . $row['lastname'] . "</td>
                                            <td>" . $row['gender'] . "</td>
                                            <td>" . $row['birthdate'] . "</td>
                                            <td>

                                              <form method='POST' style='display: inline;'>
                                                    <input type='hidden' name='applicant_id' value='{$row['applicant_id']}'>
                                                    <button type='button' class='interview-score' title='Interview Score' onclick='openInterviewModal(this)' data-id='{$row['applicant_id']}'>
                                                        <i class='bx bxs-phone'></i>
                                                    </button>
                                                </form>

                                                <form method='POST' style='display: inline;'>
                                                    <input type='hidden' name='applicant_id' value='{$row['applicant_id']}'>
                                                    <button type='button' class='exam-score' title='Exam Score' onclick='openExaminationModal(this)' data-id='{$row['applicant_id']}'>
                                                        <i class='bx bxs-pencil'></i>
                                                    </button>
                                                </form>

                                               
                                                
                                
                                            </td>

                                            <td>
                                            

                                                <form action='eligibleapplicants.php' method='POST' style='display: inline;'>
                                                        <input type='hidden' name='applicant_id' value='{$row['applicant_id']}'>
                                                        <input type='hidden' name='action' value='full_scholar'>
                                                        <button type='button' class='move-bck-button' title='Confirm Full Scholar' onclick='confirmFullScholar(event)'>
                                                        <i class='bx bxs-check-circle'></i> 
                                                    </button>
                                                </form>

                                                <form action='eligibleapplicants.php' method='POST' style='display: inline;'>
                                                        <input type='hidden' name='applicant_id' value='{$row['applicant_id']}'>
                                                        <input type='hidden' name='action' value='half_scholar'>
                                                        <button type='button' class='move-bck-button' title='Confirm Half' onclick='confirmHalfScholar(event)'>
                                                        <i class='bx bxs-x-circle'></i> 
                                                    </button>
                                                </form>

                                               <form action='auto_full_half_scholar.php' method='POST' style='display: inline;' onsubmit='return confirmScanScore(event)'>
                                                    <input type='hidden' name='applicant_id' value='{$row['applicant_id']}'>
                                                    <button type='submit' class='move-bck-button' title='Scan Score'>
                                                        <i class='bx bx-scan'></i>
                                                    </button>
                                                </form>

                                                 <form action='remove_eligible.php' method='POST' style='display: inline;'>
                                                        <input type='hidden' name='applicant_id' value='{$row['applicant_id']}'>
                                                        <button type='button' class='move-bck-button' title='Remove Applicant' onclick='confirmRemove(event)'>
                                                        <i class='bx bxs-trash'></i> 
                                                    </button>
                                                </form>
                                            
                                            </td>
                                        </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='7'>No records found</td></tr>";
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

    <script src="script.js"></script>

</body>
</html>

<?php
// Close the connection
$conn->close();
?>
