<?php

// Start the session
session_start();


$userRole = isset($_SESSION['user_Organizer']['role']) ? $_SESSION['user_Organizer']['role'] : null;  // Get the role from session, default to empty string if not set

// Check if user role exists in session, else redirect to login
if ($userRole == '') {
    header("Location: login.html");
    exit();
}

// Include database connection
$servername = "localhost";
$username = "root"; // Replace with your DB username
$password = ""; // Replace with your DB password
$dbname = "scholarship_portal";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to show SweetAlert notifications with window.onload
function showSwalAlert($title, $text, $icon, $redirectUrl = null) {
    $swalScript = "
        <script>
        window.onload = function() {
            Swal.fire({
                title: '$title',
                text: '$text',
                icon: '$icon',
                confirmButtonText: 'OK'
            }).then(function() {";
    
    if ($redirectUrl) {
        $swalScript .= "window.location.href = '$redirectUrl';";
    }
    
    $swalScript .= "});
        }
        </script>";
    
    echo $swalScript;
}

// Query to get emails from the applicant_demographic table
$sql = "SELECT email FROM applicant_demographic";
$result = $conn->query($sql);

$accounts_sql = "SELECT email FROM accounts_tbl";
$accounts_result = $conn->query($accounts_sql);

// Query to get emails from other applicant groups
$eligible_sql = "SELECT email FROM eligible_applicants_tbl";
$eligible_result = $conn->query($eligible_sql);

$rejected_sql = "SELECT email FROM rejected_applicants_tbl";
$rejected_result = $conn->query($rejected_sql);

$full_scholar_sql = "SELECT email FROM full_scholar_applicant";
$full_scholar_result = $conn->query($full_scholar_sql);

$half_scholar_sql = "SELECT email FROM half_scholar_applicant";
$half_scholar_result = $conn->query($half_scholar_sql);



// Initialize $stmt to null
$stmt = null;

// Handle form submission for sending notifications
if ($_SERVER["REQUEST_METHOD"] === "POST" && !isset($_POST['delete_all'])) {
    $title = $_POST['title'] ?? null;
    $message = $_POST['message'] ?? null;
    $recipient = $_POST['recipients'] ?? null;
    $individual_recipient = $_POST['individual_recipients'] ?? null;

    // Handle group recipients
    if ($recipient === 'All Applicants') {
        $accounts_query = "SELECT email FROM accounts_tbl WHERE role = 'Applicant'";
        $accounts_emails = $conn->query($accounts_query);

        if ($accounts_emails->num_rows > 0) {
            while ($row = $accounts_emails->fetch_assoc()) {
                $email = $row['email'];
                $stmt = $conn->prepare("INSERT INTO notifications_tbl (title, message, recipient_email, recipients_group) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("ssss", $title, $message, $email, $recipient);
                $stmt->execute();
                $stmt->close(); // Close the statement after execution
            }
            showSwalAlert('Notification Sent!', 'Notification sent to all applicants successfully!', 'success', 'notification.php');
        } else {
            showSwalAlert('No Applicants Found', 'No applicants found to send the notification.', 'error', 'notification.php');
        }
    }
    elseif ($recipient === 'Pending Applicants') {
        $eligible_query = "SELECT email FROM applicant_demographic";
        $eligible_emails = $conn->query($eligible_query);

        if ($eligible_emails->num_rows > 0) {
            while ($row = $eligible_emails->fetch_assoc()) {
                $email = $row['email'];
                $stmt = $conn->prepare("INSERT INTO notifications_tbl (title, message, recipient_email, recipients_group) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("ssss", $title, $message, $email, $recipient);
                $stmt->execute();
                $stmt->close(); // Close the statement after execution
            }
            showSwalAlert('Notification Sent!', 'Notification sent to all pending applicants successfully!', 'success', 'notification.php');
        } else {
            showSwalAlert('No Applicants Found', 'No pending applicants found to send the notification.', 'error', 'notification.php');
        }
    }
    elseif ($recipient === 'Eligible Applicants') {
        $eligible_query = "SELECT email FROM eligible_applicants_tbl";
        $eligible_emails = $conn->query($eligible_query);

        if ($eligible_emails->num_rows > 0) {
            while ($row = $eligible_emails->fetch_assoc()) {
                $email = $row['email'];
                $stmt = $conn->prepare("INSERT INTO notifications_tbl (title, message, recipient_email, recipients_group) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("ssss", $title, $message, $email, $recipient);
                $stmt->execute();
                $stmt->close(); // Close the statement after execution
            }
            showSwalAlert('Notification Sent!', 'Notification sent to all eligible applicants successfully!', 'success', 'notification.php');
        } else {
            showSwalAlert('No Applicants Found', 'No eligible applicants found.', 'error', 'notification.php');
        }
    }
    elseif ($recipient === 'Rejected Applicants') {
        $rejected_query = "SELECT email FROM rejected_applicants_tbl";
        $rejected_emails = $conn->query($rejected_query);

        if ($rejected_emails->num_rows > 0) {
            while ($row = $rejected_emails->fetch_assoc()) {
                $email = $row['email'];
                $stmt = $conn->prepare("INSERT INTO notifications_tbl (title, message, recipient_email, recipients_group) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("ssss", $title, $message, $email, $recipient);
                $stmt->execute();
                $stmt->close(); // Close the statement after execution
            }
            showSwalAlert('Notification Sent!', 'Notification sent to all rejected applicants successfully!', 'success', 'notification.php');
        } else {
            showSwalAlert('No Applicants Found', 'No rejected applicants found.', 'error', 'notification.php');
        }
    }
    elseif ($recipient === 'Full Scholar Applicants') {
        $full_scholar_query = "SELECT email FROM full_scholar_applicant";
        $full_scholar_emails = $conn->query($full_scholar_query);

        if ($full_scholar_emails->num_rows > 0) {
            while ($row = $full_scholar_emails->fetch_assoc()) {
                $email = $row['email'];
                $stmt = $conn->prepare("INSERT INTO notifications_tbl (title, message, recipient_email, recipients_group) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("ssss", $title, $message, $email, $recipient);
                $stmt->execute();
                $stmt->close(); // Close the statement after execution
            }
            showSwalAlert('Notification Sent!', 'Notification sent to all full scholar applicants successfully!', 'success', 'notification.php');
        } else {
            showSwalAlert('No Applicants Found', 'No full scholar applicants found.', 'error', 'notification.php');
        }
    }
    elseif ($recipient === 'Half Scholar Applicants') {
        $half_scholar_query = "SELECT email FROM half_scholar_applicant";
        $half_scholar_emails = $conn->query($half_scholar_query);

        if ($half_scholar_emails->num_rows > 0) {
            while ($row = $half_scholar_emails->fetch_assoc()) {
                $email = $row['email'];
                $stmt = $conn->prepare("INSERT INTO notifications_tbl (title, message, recipient_email, recipients_group) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("ssss", $title, $message, $email, $recipient);
                $stmt->execute();
                $stmt->close(); // Close the statement after execution
            }
            showSwalAlert('Notification Sent!', 'Notification sent to all half scholar applicants successfully!', 'success', 'notification.php');
        } else {
            showSwalAlert('No Applicants Found', 'No half scholar applicants found.', 'error', 'notification.php');
        }
    }

    // Handle individual email notifications
    elseif (!empty($individual_recipient)) {
        // Determine the group name for the individual email
        $group_name = '';

        // Check if the email exists in the applicant_demographic table
        $check_email_query = "SELECT email FROM applicant_demographic WHERE email = ?";
        $stmt = $conn->prepare($check_email_query);
        $stmt->bind_param("s", $individual_recipient);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            $group_name = 'Pending Applicants';
        } else {
            // Check other tables for the email
            $check_eligible_query = "SELECT email FROM eligible_applicants_tbl WHERE email = ?";
            $stmt = $conn->prepare($check_eligible_query);
            $stmt->bind_param("s", $individual_recipient);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $group_name = 'Eligible Applicants';
            } else {
                $check_rejected_query = "SELECT email FROM rejected_applicants_tbl WHERE email = ?";
                $stmt = $conn->prepare($check_rejected_query);
                $stmt->bind_param("s", $individual_recipient);
                $stmt->execute();
                $stmt->store_result();

                if ($stmt->num_rows > 0) {
                    $group_name = 'Rejected Applicants';
                } else {
                    $check_full_scholar_query = "SELECT email FROM full_scholar_applicant WHERE email = ?";
                    $stmt = $conn->prepare($check_full_scholar_query);
                    $stmt->bind_param("s", $individual_recipient);
                    $stmt->execute();
                    $stmt->store_result();

                    if ($stmt->num_rows > 0) {
                        $group_name = 'Full Scholar Applicants';
                    } else {
                        $check_half_scholar_query = "SELECT email FROM half_scholar_applicant WHERE email = ?";
                        $stmt = $conn->prepare($check_half_scholar_query);
                        $stmt->bind_param("s", $individual_recipient);
                        $stmt->execute();
                        $stmt->store_result();

                        if ($stmt->num_rows > 0) {
                            $group_name = 'Half Scholar Applicants';
                        } else {
                            $group_name = '';
                        }
                    }
                }
            }
        }

        // Insert notification for individual recipient with dynamic group name
        $stmt = $conn->prepare("INSERT INTO notifications_tbl (title, message, recipient_email, recipients_group) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $title, $message, $individual_recipient, $group_name);

        if ($stmt->execute()) {
            showSwalAlert('Notification Sent!', 'Notification sent to the individual email successfully!', 'success', 'notification.php');
        } else {
            showSwalAlert('Error', 'Error: ' . htmlspecialchars($stmt->error, ENT_QUOTES, 'UTF-8'), 'error', 'notification.php');
        }
        
        // Close the statement after execution
        $stmt->close();
        
    }
}


// Query to fetch   sent
$notification_query = "SELECT * FROM notifications_tbl ORDER BY notification_id ASC";
$notifications_result = $conn->query($notification_query);

$conn->close();
?>



<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
	<link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
	<title>Notification</title>
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
                <li class="active">
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
			<i class='bx bx-menu' ></i>
			<a href="#" class="nav-link">Categories</a>
			<form action="#">
				<div class="form-input">
					<input type="search" placeholder="Search...">
					<button type="submit" class="search-btn"><i class='bx bx-search' ></i></button>
				</div>
			</form>
			<input type="checkbox" id="switch-mode" hidden>
			<label for="switch-mode" class="switch-mode"></label>
			<a href="#" class="notification">
				<i class='bx bxs-bell' ></i>
				<span class="num">8</span>
			</a>
			<a href="#" class="profile">
				<img src="img/profile.jpg">
			</a>
		</nav>
		<!-- NAVBAR -->

        <!-- Notification -->
<main>
<div class="head-title">
    <div class="left">
        <h1>Notification</h1>
    </div>
	
	<!-- Delete All Button -->
	<div class="delete-all-notifications">
        <form action="delete_all_notifications.php" method="POST">
            <button type="submit" name="delete_all" class="delete-all-button">Delete All Notifications</button>
        </form>
		</div>
		
	</div>

	<div class="notification-container">
		
    <div class="notification-form">
        <!-- Notification Form -->
        <form action="notification.php" method="POST">
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" id="title" name="title" required placeholder="Enter title">
            </div>
            <div class="form-group">
                <label for="message">Message</label>
                <textarea id="message" name="message" rows="4" required placeholder="Enter your message"></textarea>
            </div>
			
			<!-- Group Recipients Dropdown -->
			<div class="form-group">
				<label for="group_recipients">Group Recipients</label>
				<select id="group_recipients" name="recipients">
					<option value="">Select Group Recipient</option>
                    <option value="All Applicants">All Applicants</option>
					<option value="Pending Applicants">Pending Applicants</option>
					<option value="Eligible Applicants">Eligible Applicants</option>
					<option value="Rejected Applicants">Rejected Applicants</option>
					<option value="Full Scholar Applicants">Full Scholar Applicants</option>
					<option value="Half Scholar Applicants">Half Scholar Applicants</option>
				</select>
			</div>

			<!-- Individual Recipients Dropdown -->
			<div class="form-group">
				<label for="individual_recipients">Individual Recipients</label>
				<select id="individual_recipients" name="individual_recipients">
					<option value="">Select Individual Recipient</option>
					<?php
					// Loop through the result and create dropdown options for individual emails
					while ($row = mysqli_fetch_assoc($result)) {
						echo "<option value='" . $row['email'] . "'>" . $row['email'] . "</option>";
					}
                    while ($row = mysqli_fetch_assoc($accounts_result)) {
						echo "<option value='" . $row['email'] . "'>" . $row['email'] . "</option>";
					}
					while ($row = mysqli_fetch_assoc($eligible_result)) {
						echo "<option value='" . $row['email'] . "'>" . $row['email'] . "</option>";
					}
					while ($row = mysqli_fetch_assoc($rejected_result)) {
						echo "<option value='" . $row['email'] . "'>" . $row['email'] . "</option>";
					}
					while ($row = mysqli_fetch_assoc($full_scholar_result)) {
						echo "<option value='" . $row['email'] . "'>" . $row['email'] . "</option>";
					}
					while ($row = mysqli_fetch_assoc($half_scholar_result)) {
						echo "<option value='" . $row['email'] . "'>" . $row['email'] . "</option>";
					}
					?>
				</select>
			</div>
			
            <div class="form-group">
                <button type="submit" class="send-button">Send</button>
            </div>
        </form>
    </div>

	


    <!-- Notification Table -->
<div class="notification-table">
    <h2>Sent Notifications</h2>
    <table>
        <thead>
            <tr>
                <th>Title</th>
                <th>Message</th>
                <th>Recipient</th>
				<th>Group</th>
                <th>Timestamp</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($notifications_result->num_rows > 0): ?>
            <?php while ($notification = $notifications_result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($notification['title']); ?></td>
                    <td><?php echo htmlspecialchars($notification['message']); ?></td>
					<td><?php echo htmlspecialchars($notification['recipient_email']); ?></td>
					<td><?php echo htmlspecialchars($notification['recipients_group']); ?></td>
                    <td><?php echo date('Y-m-d H:i:s', strtotime($notification['created_at'])); ?></td>
                    <td>

					<form action="edit_notification.php" method="POST" style="display: inline;">
						<input type="hidden" name="notification_id" value="<?php echo $notification['notification_id']; ?>">
						<button type="button" class="edit_button" title="Edit Notification" onclick="showEditNotificationModal(<?php echo $notification['notification_id']; ?>)">
							<i class="bx bx-edit"></i>
						</button>
					</form>

					<!-- Edit Notification Modal -->
					<div class="notificationModal" id="editNotificationModal_<?php echo $notification['notification_id']; ?>" style="display: none;">
						<div class="modal-content">
							<form action="update_notification.php" method="POST">
								<input type="hidden" name="notification_id" value="<?php echo $notification['notification_id']; ?>">
								<label for="title">Title</label>
								<input type="text" id="title" name="title" value="<?php echo $notification['title']; ?>" required><br>
								
								<label for="message">Message</label>
								<textarea id="message" name="message" required><?php echo $notification['message']; ?></textarea><br>

								<label for="recipient_email">Recipient Email</label>
								<input type="email" id="recipient_email" name="recipient_email" value="<?php echo $notification['recipient_email']; ?>" readonly><br>

								<label for="recipients_group">Recipients Group</label>
								<input type="text" id="recipients_group" name="recipients_group" value="<?php echo $notification['recipients_group']; ?>" readonly><br>

								<button type="submit">Save Changes</button>
							</form>
						</div>
					</div>

                        <form action="delete_notification.php" method="POST" style="display: inline;">
                            <input type="hidden" name="notification_id" value="<?php echo $notification['notification_id']; ?>">
                            <button type="button" class="remove-button" title="Delete Notification" onclick="confirmDeleteNotification(event)">
                                <i class="bx bxs-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="5">No notifications sent yet.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
</div>

	</main>
	
<script src="script.js"></script>
</body>
</html>
