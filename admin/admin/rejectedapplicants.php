<?php
// Start the session
session_start();

$userRole = isset($_SESSION['user_Organizer']['role']) ? $_SESSION['user_Organizer']['role'] : null;  // Get the role from session, default to empty string if not set

// Check if user role exists in session, else redirect to login
if ($userRole == '') {
    header("Location: login.php");
    exit();
}

// Connect to the database
$servername = "localhost";
$username = "root"; // Replace with your DB username
$password = ""; // Replace with your DB password
$dbname = "scholarship_portal";
$sql = "SELECT * FROM applicants WHERE status = 'rejected'";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to fetch eligible applicants
$sql = "SELECT applicant_id, firstname, middlename, lastname, gender, birthdate FROM rejected_applicants_tbl";
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
    <title>Rejected Applicants</title>
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
            <h1 class="text">ScholarFlow</h1>
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

        <!-- MAIN -->
        <main>
            <div class="head-title">
                <div class="left">
                    <h1>Rejected Applicants</h1>
                </div>

                <form id="removeForm" method="POST" action="remove_all_rejected.php">
                    <button type="submit" class="btn-download" onclick="return confirmRemoveAllRejected(event)">
                        <i class='bx bxs-trash'></i>
                        <span class="text">Remove All Rejected</span>  
                    </button>
                </form>

                <form id="downloadForm" method="POST" action="download_rejected_applicants.php">
                    <button type="submit" class="btn-download" onclick="return confirmDownloadRejected(event)">
                        <i class='bx bxs-download'></i>
                        <span class="text">Download All Eligible</span>  
                    </button>
                </form>
            </div>

            <div class="table-data">
                <div class="order">
                    <div class="head">
                        <h3>Rejected Applicants</h3>
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
                                                <form action='remove_rejected.php' method='POST' style='display: inline;'>
                                                    <input type='hidden' name='applicant_id' value='{$row['applicant_id']}'>
                                                    <button type='button' class='move-bck-button' title='Remove Applicant' onclick='confirmRemove(event)'>
                                                        <i class='bx bxs-trash'></i>
                                                    </button>
                                                </form>
                                            </td>
                                          </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='6'>No records found</td></tr>";
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
	 <script>
        // JavaScript to handle the trash button click
        document.querySelectorAll('.move-back-btn').forEach(function(button) {
            button.addEventListener('click', function() {
                const applicantId = button.getAttribute('data-id');

                // Send a POST request to move the applicant back to the demographic table
                fetch('remove_rejected.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'applicant_id=' + applicantId,
                })
                .then(response => response.text())
                .then(data => {
                    if (data === "success") {
                        alert('Applicant has been moved back to the demographic table.');
                        location.reload(); // Refresh the page to reflect changes	
                    } else {
                        alert('An error occurred while processing the request.');
						location.reload();
                    }
                })
                .catch(error => {
                    alert('An error occurred: ' + error);
                });
            });
        });
    </script>

</body>
</html>

<?php
// Close the connection
$conn->close();
?>
