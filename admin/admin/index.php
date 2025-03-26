<?php

// Start the session
session_start();


$userRole = isset($_SESSION['user_Organizer']['role']) 
    ? $_SESSION['user_Organizer']['role'] 
    : (isset($_SESSION['user_Admin']['role']) ? $_SESSION['user_Admin']['role'] : null);  // Get the role from session, default to empty string if not set

// Check if user role exists in session, else redirect to login
if ($userRole == '') {
    header("Location: login.html");
    exit();
}

// Database connection directly inside index.php
$servername = "localhost"; // Database server
$username = "root";    // Database username
$password = "";    // Database password
$dbname = "scholarship_portal"; // Database name

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch count of applicants (role: Applicant)
$applicantQuery = "SELECT COUNT(*) AS total_applicants FROM accounts_tbl WHERE role = 'Applicant'";
$applicantResult = mysqli_query($conn, $applicantQuery);
$applicantData = mysqli_fetch_assoc($applicantResult);
$totalApplicantAccounts = $applicantData['total_applicants'];

// Fetch count of organizers (role: Organizer)
$organizerQuery = "SELECT COUNT(*) AS total_organizers FROM accounts_tbl WHERE role = 'Organizer'";
$organizerResult = mysqli_query($conn, $organizerQuery);
$organizerData = mysqli_fetch_assoc($organizerResult);
$totalOrganizers = $organizerData['total_organizers'];

// Fetch count of admins (role: Admin)
$adminQuery = "SELECT COUNT(*) AS total_admins FROM accounts_tbl WHERE role = 'Admin'";
$adminResult = mysqli_query($conn, $adminQuery);
$adminData = mysqli_fetch_assoc($adminResult);
$totalAdmins = $adminData['total_admins'];


// Fetch count of applicants
$applicantQuery = "SELECT COUNT(*) AS total_applicants FROM applicant_demographic";
$applicantResult = mysqli_query($conn, $applicantQuery);
$applicantData = mysqli_fetch_assoc($applicantResult);
$totalApplicants = $applicantData['total_applicants'];

// Fetch count of eligible applicants
$eligibleQuery = "SELECT COUNT(*) AS total_eligible FROM eligible_applicants_tbl";
$eligibleResult = mysqli_query($conn, $eligibleQuery);
$eligibleData = mysqli_fetch_assoc($eligibleResult);
$totalEligible = $eligibleData['total_eligible'];

// Fetch count of rejected applicants
$rejectedQuery = "SELECT COUNT(*) AS total_rejected FROM rejected_applicants_tbl";
$rejectedResult = mysqli_query($conn, $rejectedQuery);
$rejectedData = mysqli_fetch_assoc($rejectedResult);
$totalRejected = $rejectedData['total_rejected'];

// Fetch count of male applicants across all tables
$maleQuery = "
    SELECT 
        (SELECT COUNT(*) FROM applicant_demographic WHERE gender = 'Male') +
		(SELECT COUNT(*) FROM eligible_applicants_tbl WHERE gender = 'Male') +
		(SELECT COUNT(*) FROM rejected_applicants_tbl WHERE gender = 'Male') +
        (SELECT COUNT(*) FROM full_scholar_applicant WHERE gender = 'Male') +
        (SELECT COUNT(*) FROM half_scholar_applicant WHERE gender = 'Male') AS total_men";
$maleResult = mysqli_query($conn, $maleQuery);
$maleData = mysqli_fetch_assoc($maleResult);
$totalMen = $maleData['total_men'];

// Fetch count of female applicants across all tables
$femaleQuery = "
    SELECT 
        (SELECT COUNT(*) FROM applicant_demographic WHERE gender = 'Female') +
		(SELECT COUNT(*) FROM eligible_applicants_tbl WHERE gender = 'Female') +
		(SELECT COUNT(*) FROM rejected_applicants_tbl WHERE gender = 'Female') +
        (SELECT COUNT(*) FROM full_scholar_applicant WHERE gender = 'Female') +
        (SELECT COUNT(*) FROM half_scholar_applicant WHERE gender = 'Female') AS total_women";
$femaleResult = mysqli_query($conn, $femaleQuery);
$femaleData = mysqli_fetch_assoc($femaleResult);
$totalWomen = $femaleData['total_women'];

// Close the database connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
	<link rel="stylesheet" href="style.css">
	<title>Dashboard</title>
	<link rel="icon" sizes="180x180" href="img/favicon.ico">
    <link rel="icon" type="image/png" sizes="32x32" href="img/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="img/favicon-16x16.png">
    <link rel="manifest" href="img/site.webmanifest">
</head>
<body>
	<script>
		// Check if dark mode was previously enabled
		if (localStorage.getItem('darkMode') === 'enabled') {
		  document.documentElement.classList.add('dark'); // Apply dark mode to the root element
		}
	</script>
	
	    <!-- SIDEBAR -->
		<section id="sidebar">
        <a href="#" class="brand">
            <img src="img/logo.png" alt="logo">
            <h1 class="text">cholarFlow</h1>
        </a>
        <ul class="side-menu top">
            <!-- Always show dashboard link -->
            <li class="active">
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
                <li>
                    <a href="notification.php">
                        <i class='bx bxs-user-badge'></i>
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
					<h1>Dashboard</h1>
				</div>
			</div>

			<ul class="box-info-accounts">
				<li>
					<i class='bx bxs-group' ></i>
					<span class="text">
						<h3><?php echo $totalApplicantAccounts; ?></h3>
						<p>Applicant Accounts</p>
					</span>
				</li>
				<li>
					<i class='bx bxs-group'></i>
					<span class="text">
						<h3><?php echo $totalOrganizers; ?></h3>
						<p>Organizer applicants</p>
					</span>
				</li>
				<li>
					<i class='bx bxs-group'></i>
					<span class="text">
						<h3><?php echo $totalAdmins; ?></h3>
						<p>Admin Accounts</p>
					</span>
				</li>
			</ul>

			<ul class="box-info-applicants">
				<li>
					<i class='bx bxs-user-detail' ></i>
					<span class="text">
						<h3><?php echo $totalApplicants; ?></h3>
						<p>Pending Applicants </p>
					</span>
				</li>
				<li>
					<i class='bx bxs-user-check'></i>
					<span class="text">
						<h3><?php echo $totalEligible; ?></h3>
						<p>Eligible applicants</p>
					</span>
				</li>
				<li>
					<i class='bx bxs-user-x'></i>
					<span class="text">
						<h3><?php echo $totalRejected; ?></h3>
						<p>Rejected applicants</p>
					</span>
				</li>
			</ul>

 		<!-- Charts Section -->
		<div class="charts">
			<div class="chart-row">
				<div class="chart-container">
					<canvas id="barChart"></canvas>
				</div>
				<div class="chart-container">
					<canvas id="lineChart"></canvas>
				</div>
			</div>

			<!-- Gender Distribution (Pie Chart) -->
			<div class="gender-chart-container">
				<canvas id="genderPieChart"></canvas>
			</div>
		</div>


		</main>
		<!-- MAIN -->
	</section>
	<!-- CONTENT -->

	<!-- Chart.js Script -->
	<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

	<script>
		  // Bar Chart Data
		const barChartData = {
			labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
			datasets: [{
				label: 'Applicants',
				data: [2500, 2000, 3000, 4000, 2500, 3500],
				backgroundColor: 'rgba(75, 192, 192, 1)', // Solid color (no transparency)
				borderColor: 'rgba(75, 192, 192, 1)',     // Solid border color
				borderWidth: 1
			}]
		};


        // Line Chart Data
        const lineChartData = {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Secured Applicants',
                data: [2000, 2500, 3000, 3500, 3000, 3800],
                fill: false,
                borderColor: 'rgba(153, 102, 255, 1)',
                tension: 0.1
            }]
        };

        // Gender Pie Chart Data
        const genderPieData = {
            labels: ['Men', 'Women'],
            datasets: [{
                label: 'Gender Distribution',
                data: [<?php echo $totalMen; ?>, <?php echo $totalWomen; ?>],  // Use PHP variables to inject data
                backgroundColor: ['rgba(54, 162, 235, 0.2)', 'rgba(255, 99, 132, 0.2)'],
                borderColor: ['rgba(54, 162, 235, 1)', 'rgba(255, 99, 132, 1)'],
                borderWidth: 1
            }]
        };

        // Bar Chart Config
        const barConfig = {
            type: 'bar',
            data: barChartData,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                return tooltipItem.label + ': ' + tooltipItem.raw + ' applicants';
                            }
                        }
                    }
                }
            }
        };

        // Line Chart Config
        const lineConfig = {
            type: 'line',
            data: lineChartData,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                }
            }
        };

        // Pie Chart Config
        const pieConfig = {
            type: 'pie',
            data: genderPieData,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                return tooltipItem.label + ': ' + tooltipItem.raw + ' applicants';
                            }
                        }
                    }
                }
            }
        };

        // Render the charts
        const barChart = new Chart(document.getElementById('barChart'), barConfig);
        const lineChart = new Chart(document.getElementById('lineChart'), lineConfig);
        const genderPieChart = new Chart(document.getElementById('genderPieChart'), pieConfig);
	</script>

	<script src="script.js"></script>
    <script>
    let isLoggedIn = <?php echo isset($_SESSION['email']) ? 'true' : 'false'; ?>;
</script>
</body>
</html>
