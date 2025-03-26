<?php
// Start the session
session_start();

$userRole = isset($_SESSION['user_Admin']['role']) ? $_SESSION['user_Admin']['role'] : null; // Get the role from session

// Check if user role exists in session, else redirect to login
if ($userRole == '') {
    header("Location: login.php");
    exit();
}

// Connect to the database
$servername = "localhost";
$username = "root"; // Replace with your DB username
$password = "";     // Replace with your DB password
$dbname = "scholarship_portal";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to log actions in the event_logs table
function logEvent($conn, $editorName, $editedAccountName, $editedAccountUsername, $action, $status) {
    $sql_log = "INSERT INTO event_logs (editor_name, edited_account_name, edited_account_username, action, status) 
                VALUES (?, ?, ?, ?, ?)";
    $stmt_log = $conn->prepare($sql_log);
    $stmt_log->bind_param("sssss", $editorName, $editedAccountName, $editedAccountUsername, $action, $status);
    $stmt_log->execute();
    $stmt_log->close();
}



// Check if form data is submitted for adding a user
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['username']) && !isset($_POST['account_id'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $name = $_POST['name'];
    $contact_number = $_POST['contact_number'];
    $role = $_POST['role'];

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert query
    $sql = "INSERT INTO accounts_tbl (username, email, password, name, contact_number, role) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $username, $email, $hashed_password, $name, $contact_number, $role);

    if ($stmt->execute()) {
        logEvent(
            $conn,
            $_SESSION['user_Admin']['name'],
            $name,
            $email,
            "Added a new account",
            "success"
        );
    } else {
        $message = "Error adding user: " . $stmt->error;
        logEvent(
            $conn,
            $_SESSION['user_Admin']['name'],
            $name,
            $email,
            "Failed to add a new account",
            "error"
        );
    }

    $stmt->close();

    // Redirect to avoid form resubmission
    header("Location: accounts.php");
    exit;
}

// Check if form data is submitted for updating a user
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['account_id'])) {
    $account_id = $_POST['account_id'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $name = $_POST['name'];
    $contact_number = $_POST['contact_number'];
    $role = $_POST['role'];

    // Fetch current user account data
    $sql_fetch_user = "SELECT * FROM accounts_tbl WHERE account_id=?";
    $stmt_fetch_user = $conn->prepare($sql_fetch_user);
    $stmt_fetch_user->bind_param("i", $account_id);
    $stmt_fetch_user->execute();
    $existing_user = $stmt_fetch_user->get_result()->fetch_assoc();
    $stmt_fetch_user->close();

    // Log changes and update user information
    foreach (['username', 'email', 'password', 'name', 'contact_number', 'role'] as $field) {
        if ($existing_user[$field] !== $_POST[$field]) {  // Compare existing value with new value
            logEvent(
                $conn,
                $_SESSION['user_Admin']['name'],
                $existing_user['name'],
                $existing_user['email'],
                "Changed $field from '{$existing_user[$field]}' to '{$_POST[$field]}'",
                "success"
            );
        }
    }

    // Hash the password if it was updated
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Update query
    $sql_update_user = "UPDATE accounts_tbl SET username=?, email=?, password=?, name=?, contact_number=?, role=? WHERE account_id=?";
    $stmt_update_user = $conn->prepare($sql_update_user);
    $stmt_update_user->bind_param("ssssssi", $username, $email, $hashed_password, $name, $contact_number, $role, $account_id);

    if (!$stmt_update_user->execute()) {
        logEvent(
            $conn,
            $_SESSION['user_Admin']['name'],
            $existing_user['name'],
            $existing_user['email'],
            "Failed to update account",
            "error"
        );
    }

    $stmt_update_user->close();

    // Redirect to avoid form resubmission
    header("Location: accounts.php");
    exit;
}

// Check if an ID is set for deletion
if (isset($_GET['delete_id'])) {
    $account_id = $_GET['delete_id'];

    // Fetch account info before deletion
    $sql_fetch_user = "SELECT * FROM accounts_tbl WHERE account_id=?";
    $stmt_fetch_user = $conn->prepare($sql_fetch_user);
    $stmt_fetch_user->bind_param("i", $account_id);
    $stmt_fetch_user->execute();
    $existing_user = $stmt_fetch_user->get_result()->fetch_assoc();
    $stmt_fetch_user->close();

    // Delete query
    $sql = "DELETE FROM accounts_tbl WHERE account_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $account_id);

    if ($stmt->execute()) {
        logEvent(
            $conn,
            $_SESSION['user_Admin']['name'],
            $existing_user['name'],
            $existing_user['email'],
            "Deleted account",
            "success"
        );
    } else {
        logEvent(
            $conn,
            $_SESSION['user_Admin']['name'],
            $existing_user['name'],
            $existing_user['email'],
            "Failed to delete account",
            "error"
        );
    }

    $stmt->close();

    // Redirect to avoid resubmission
    header("Location: accounts.php");
    exit;
}

// Query to fetch user accounts
$sql = "SELECT account_id, username, email, password, name, contact_number, role FROM accounts_tbl";
$result = $conn->query($sql);

?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="style.css">    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>Accounts</title>
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
                <li class="active">
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
                <li class="dropdown">
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
                <li>
                    <a href="reports.php">
                        <i class='bx bxs-bar-chart-alt-2'></i>
                        <span class="text">Reports</span>
                    </a>
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
                    <h1>Accounts</h1>
                </div>
            </div>

            <div class="table-data">
                <div class="order">
                    <div class="head">
                        <h3>Accounts</h3>
                    </div>
                    <?php if (isset($message)): ?>
                        <div class="alert alert-danger"><?= $message; ?></div> 
                    <?php endif; ?>
                    <table>
                        <thead> 
                            <tr>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Password</th>
                                <th>Name</th>
                                <th>Contact Number</th>
                                <th>Role</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($result->num_rows > 0) {
                                while($row = $result->fetch_assoc()) {
                                    echo "<tr>
                                            <td>{$row['username']}</td>
                                            <td>{$row['email']}</td>
                                            <td>*****</td> 
                                            <td>{$row['name']}</td>
                                            <td>{$row['contact_number']}</td>
                                            <td>{$row['role']}</td>
                                            <td>
                                                <a href='#' class='edit-button' 
                                                onclick='openAccountEditModal
                                                ({$row['account_id']}, \"{$row['username']}\", \"{$row['email']}\", \"{$row['password']}\"
                                                , \"{$row['name']}\", \"{$row['contact_number']}\", \"{$row['role']}\")'
                                                title='Edit User'><i class='bx bx-edit'></i>
                                                </a>

                                                <a href='#' class='delete-button' onclick='openAccountDeleteModal({$row['account_id']})' title='Delete User'>
                                                    <i class='bx bx-trash'></i>
                                                </a>
                                            </td>
                                        </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='5'>No accounts found</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </section>

            <!-- Custom Modal -->
        <div id="notificationModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <p id="notificationMessage"></p>
            <button id="closeModal" class="btn">OK</button>
        </div>
        </div>


    <!-- Edit User Modal -->
    <div id="editAccountModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeAccountEditModal()">&times;</span>
            <h2>Edit Account</h2>
            <form id="editForm" method="POST">
                <input type="hidden" name="account_id" id="account_id">
                
                <label for="username">Username:</label>
                <input type="text" name="username" id="username" >

                <label for="email">Email:</label>
                <input type="email" name="email" id="email" >

                <label for="password">Password:</label>
                <input type="text" name="password" id="password" >

                <label for="name">Name:</label>
                <input type="text" name="name" id="name" >

                <label for="contact_number">Contact Number:</label>
                <input type="text" name="contact_number" id="contact_number" >

                <label for="role">Role:</label>
                <select name="role" id="role" >
                <option value="" disabled selected>Select Role</option>
                <option value="Applicant">Applicant</option>
                <option value="Organizer">Organizer</option>
                <option value="Admin">Administrator</option>
            </select>
                

                <button type="submit" onclick="validateEditAccount(event)">Update</button>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <h2>Confirm Deletion</h2>
            <p>Are you sure you want to delete this account?</p>
            <form id="deleteForm" method="GET">
                <input type="hidden" name="delete_id" id="delete_id">
                <button type="submit">Yes, Delete</button>
                <button type="button" onclick="closeAccountDeleteModal()">Cancel</button>
            </form>
        </div>
    </div>

		<!-- Floating Plus Icon -->
	<div class="floating-icon" onclick="openAccountAddModal()">
		<i class='bx bxs-user-plus'></i>
	</div>

	<!-- Add User Modal -->
<div id="addModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeAccountAddModal()">&times;</span>
        <h2>Add Account</h2>
        <form id="addForm" method="POST" onsubmit="return validateAddAccount(event)">
            <label for="add_username">Username:</label>
            <input type="text" name="username" id="add_username" >

            <label for="add_email">Email:</label>
            <input type="email" name="email" id="add_email" >

            <label for="add_password">Password:</label>
            <input type="password" name="password" id="add_password" >

            <label for="add_name">Name:</label>
            <input type="text" name="name" id="add_name" >

            <label for="add_contact_number">Contact Number:</label>
            <input type="text" name="contact_number" id="add_contact_number" > 

            <label for="add_role">Role:</label>
            <select name="role" id="add_role" >
                <option value="" disabled selected>Select Role</option>
                <option value="Applicant">Applicant</option>
                <option value="Organizer">Organizer</option>
                <option value="Admin">Administrator</option>
            </select>

            <button type="submit">Add</button>
        </form>
    </div>
</div>



<script src = "script.js"></script>



</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
    