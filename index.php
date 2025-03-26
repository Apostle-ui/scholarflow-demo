<?php
// Start session to access logged-in applicant's email
session_start();

// Access the email for the logged-in applicant
$logged_in_email = isset($_SESSION['user_Applicant']['email']) ? $_SESSION['user_Applicant']['email'] : null;



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

$notifications = [];

// Fetch notifications for the logged-in user
if ($logged_in_email) {
    // Query to fetch notifications where recipient_email matches or recipients_group matches
    $stmt = $conn->prepare("
        SELECT title, message, created_at, recipients_group
        FROM notifications_tbl
        WHERE recipient_email = ? OR recipients_group = '?'");  // Example: filter for 'pending_applicants'

    $stmt->bind_param("s", $logged_in_email);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $notifications[] = $row;
    }

    $stmt->close();
}

$status_text = null; // Initialize as null to ensure we only set it when matched

if ($logged_in_email) {
    // Tables with their corresponding statuses
    $tables = [
        'applicant_demographic' => 'Pending',
        'eligible_applicants_tbl' => 'Eligible',
        'rejected_applicants_tbl' => 'Rejected',
        'full_scholar_applicant' => 'Full Scholar',
        'half_scholar_applicant' => 'Half Scholar'
    ];

    // Iterate over tables to find the email
    foreach ($tables as $table => $status) {
        $stmt = $conn->prepare("SELECT 1 FROM $table WHERE email = ? LIMIT 1");
        if (!$stmt) {
            die("Prepare failed: " . $conn->error); // Debugging: Show error if prepare fails
        }

        $stmt->bind_param("s", $logged_in_email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $status_text = $status; // Update status if found
            break; // Exit loop once a match is found
        }

        $stmt->close();
    }
}

// If no match found, set a fallback status
if (!$status_text) {
    $status_text = "No Application Form"; // You can change this default to fit your needs
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Welcome to ScholarFlow</title>
  <meta name="description" content="">
  <meta name="keywords" content="">
  <link rel="icon" sizes="180x180" href="assets/img/favicon.ico">
    <link rel="icon" type="image/png" sizes="32x32" href="assets/img/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="assets/img/favicon-16x16.png">
    <link rel="manifest" href="assets/img/site.webmanifest">


  <!-- Favicons -->
  <!--<link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">-->

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Open+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,300;1,400;1,500;1,600;1,700;1,800&family=Raleway:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

  <!-- Main CSS File -->
  <link href="assets/css/main.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  
</head>

<body class="index-page">

  <header id="header" class="header d-flex align-items-center fixed-top">
    <div class="container-fluid container-xl position-relative d-flex align-items-center justify-content-between">
      <a href="index.html" class="logo d-flex align-items-center">
        <img src="assets/img/logo2.png" alt="">
        <h1 class="sitename">cholarFlow</h1>
      </a>
  
      <nav id="navmenu" class="navmenu">
    <?php
    // Assign a CSS class based on the status
    $status_class = '';
    switch ($status_text) {
        case 'Pending':
            $status_class = 'status-pending';
            break;
        case 'Eligible':
            $status_class = 'status-eligible';
            break;
        case 'Rejected':
            $status_class = 'status-rejected';
            break;
        case 'Full Scholar':
            $status_class = 'status-full-scholar';
            break;
        case 'Half Scholar':
            $status_class = 'status-half-scholar';
            break;
    }
    ?>
    <ul>
         <li><a href="#hero" class="active">Home</a></li>
        <li><a href="#about">About</a></li>
        <li><a href="#testimonials">Testimonials</a></li>
        <li class="notification">
            <a href="javascript:void(0);" class="notification-toggle">Notification</a>
            <ul class="notification-dropdown">
                <?php if (!empty($notifications)): ?>
                    <?php foreach ($notifications as $notification): ?>
                        <li>
                            <div class="notification-item">
                                <span class="notification-title"><?php echo htmlspecialchars($notification['title']); ?></span>
                                <span class="notification-message"><?php echo htmlspecialchars($notification['message']); ?></span>
                                <span class="notification-timestamp" data-timestamp="<?php echo htmlspecialchars($notification['created_at']); ?>"></span>
                            </div>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li>
                        <div class="notification-item">
                            <span class="notification-title">No Notifications</span>
                            <span class="notification-message">You currently have no new notifications.</span>
                        </div>
                    </li>
                <?php endif; ?>
            </ul>
        </li>
        <li><a href="#contact">Contact</a></li>
        <li id="user">
          <a class="mobile-account-text">Account</a> <!-- Mobile Account Text -->
          <div id="user_menu">
            <?php 
              // Check if user is logged in
              $logged_in_email = isset($_SESSION['user_Applicant']['email']) ? $_SESSION['user_Applicant']['email'] : null;
              
              if ($logged_in_email): // If the user is logged in, show the Log Out button
            ?>
              <a href="logout.php" id="user_logout">Log Out</a> <!-- Log Out button -->
            <?php else: // If the user is not logged in, show the Log In and Sign Up buttons ?>
              <a href="login/login.html" id="user_login">Log In</a> <!-- Log In button -->
              <a href="login/signup.html" id="user_signup">Sign Up</a> <!-- Sign Up button -->
            <?php endif; ?>
          </div>
        </li>
            <a href="#" id="openModal" class="status-oval <?php echo $status_class; ?>">
                <?php echo htmlspecialchars($status_text); ?>
            </a>
        </li>
    </ul>
    <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
</nav>


    </div>
  </header>

  <main class="main">

    <!-- Hero Section -->
    <section id="hero" class="hero section dark-background">

      <img src="assets/img/bg.png" class="hero-bg" alt="" data-aos="fade-in">

      <div class="container text-center" data-aos="fade-up" data-aos-delay="100">
        <h2>Scholarships open doors to brighter futures by making education accessible to all</h2>
        <div>
          <a href="#apply" class="cta-btn">Apply</a>
          <a href="#contact" class="cta-btn2">Contact us</a>
        </div>
      </div>

    </section><!-- /Hero Section -->

    <!-- Announcement modal pop up -->
    <div class="announcement-modal" id="modal">
      <div class="announcement-modal-content">
        <h1>ANNOUNCEMENT</h1>
        <p>Welcome to ScholarFlow</p>
        <button id="close-announcement-modal">Okay</button>
      </div>
    </div>
    <!-- /Announcement modal pop up -->

    <!-- About Section -->
    <section id="about" class="about section">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <h2>About</h2>
        <p>Our scholarship portal is dedicated to helping students find financial support for their education. We connect students with scholarship opportunities and provide the tools they need to succeed.</p>
      </div><!-- End Section Title -->

      <div class="container">

        <div class="row gy-4">

          <div class="col-lg-6 order-1 order-lg-2" data-aos="fade-up" data-aos-delay="100">
            <img src="assets/img/about.png" class="img-fluid" alt="">
          </div>

          <div class="col-lg-6 order-2 order-lg-1 content" data-aos="fade-up" data-aos-delay="200">
            <h3>Simplifying scholarship applications for all students.</h3>
            <p class="fst-italic">
            We assist students in applying for scholarships with ease and comfort. Our platform streamlines the application process, offering a smooth experience and equal opportunities for everyone. Here are the top 3 problems we solve in scholarship applications through our platform:
            </p>
            <ul>
              <li><i class="bi bi-check-circle"></i> <span>No more long queues—apply directly on our platform without waiting in line.</span></li>
              <li><i class="bi bi-check-circle"></i> <span>No more scheduling hassles—submit your application without visiting the barangay hall.</span></li>
              <li><i class="bi bi-check-circle"></i> <span>No more missing information—our platform keeps students informed about deadlines and requirements.</span></li>
            </ul>
          </div>

        </div>

      </div>

    </section><!-- /About Section -->



    <!-- Apply Section -->
    <section id="apply" class="call-to-action section dark-background">

      <img src="assets/img/cta-bg.png" alt="">

      <div class="container">

        <div class="row" data-aos="zoom-in" data-aos-delay="100">
          <div class="col-xl-9 text-center text-xl-start">
            <h3>Document Requirements for Applying</h3>
            <p>To apply for the ABAS Scholarship, please prepare the following documents:</p>
            <ul>
              <li>School ID</li>
              <li>Voter’s ID ( It can be your own ID or your parent's ID.)</li>
              <li>Certificate of Matricula</li>
              <li>Certificate of Grades</li>
          </ul>
          </div>
          <div class="col-xl-3 cta-btn-container text-center">
            <a class="cta-btn align-middle" id="openModal">I have all the requirements and would like to apply</a>
          </div>
        </div>

      </div>

    </section>
    
    <!-- Apply Section -->
    <div id="popup" class="popup" style="display:none;">
      <p id="popup-message"></p>
      <button onclick="closePopup()">Close</button>
  </div>

    <!-- Application form modal pop up -->
    <div id="formModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <div class="application-form">

              <h2>Application Form</h2>
              <form action="php/apply.php" method="POST" enctype="multipart/form-data">
              <input type="hidden" name="useraccounts_id" value="<?php echo isset($_SESSION['user_Applicant']['account_id']) ? 
              htmlspecialchars($_SESSION['user_Applicant']['account_id'], ENT_QUOTES, 'UTF-8') : ''; ?>">
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
                  <button type="button" class="next-button" onclick="validateForm()">Next</button>
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
                      <button type="button" class="next-button" onclick="showNextForm(1)">Back</button>
                      <button type="submit" class="next-button" onclick="validateForm3()">Submit Application</button>
                  </div>
      
              </div>
              </form>
            </div>
        </div>
    </div>

    <!-- Testimonials Section -->
    <section id="testimonials" class="testimonials section">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <h2>Testimonials</h2>
        <p>Discover what students think about our platform.</p>
      </div><!-- End Section Title -->

      <div class="container" data-aos="fade-up" data-aos-delay="100">

        <div class="swiper init-swiper">
          <script type="application/json" class="swiper-config">
            {
              "loop": true,
              "speed": 600,
              "autoplay": {
                "delay": 5000
              },
              "slidesPerView": "auto",
              "pagination": {
                "el": ".swiper-pagination",
                "type": "bullets",
                "clickable": true
              }
            }
          </script>
          <div class="swiper-wrapper">

            <div class="swiper-slide">
              <div class="testimonial-item">
                <div class="row gy-4 justify-content-center">
                  <div class="col-lg-6">
                    <div class="testimonial-content">
                      <p>
                        <i class="bi bi-quote quote-icon-left"></i>
                        <span>This website is very user-friendly! I was able to fill out my application form smoothly without encountering any errors. What I appreciate most is that I no longer need to visit our barangay hall in person, which is convenient for me as a college student studying in Manila.</span>
                        <i class="bi bi-quote quote-icon-right"></i>
                      </p>
                      <h3>Elizabeth Rose M. Dalas</h3>
                      <h4>College Student</h4>
                      <div class="stars">
                        <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                      </div>
                    </div>
                  </div>
                  <div class="col-lg-2 text-center">
                    <img src="assets/img/testimonials/beth.jpg" class="img-fluid testimonial-img" alt="">
                  </div>
                </div>
              </div>
            </div><!-- End testimonial item -->

            <div class="swiper-slide">
              <div class="testimonial-item">
                <div class="row gy-4 justify-content-center">
                  <div class="col-lg-6">
                    <div class="testimonial-content">
                      <p>
                        <i class="bi bi-quote quote-icon-left"></i>
                        <span>The website was very easy to use, and I had no trouble filling out my application form. Everything was clear, and I finished it quickly without any problems.</span>
                        <i class="bi bi-quote quote-icon-right"></i>
                      </p>
                      <h3>Jovina Belinario Cureg</h3>
                      <h4>College Student</h4>
                      <div class="stars">
                        <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                      </div>
                    </div>
                  </div>
                  <div class="col-lg-2 text-center">
                    <img src="assets/img/testimonials/Jovina.png" class="img-fluid testimonial-img" alt="">
                  </div>
                </div>
              </div>
            </div><!-- End testimonial item -->

            <div class="swiper-slide">
              <div class="testimonial-item">
                <div class="row gy-4 justify-content-center">
                  <div class="col-lg-6">
                    <div class="testimonial-content">
                      <p>
                        <i class="bi bi-quote quote-icon-left"></i>
                        <span>I like how convenient the website is for students like me. I didn’t have to go to the barangay hall, which saved me time and effort.</span>
                        <i class="bi bi-quote quote-icon-right"></i>
                      </p>
                      <h3>Missy Mistimina Nacario</h3>
                      <h4>College Student</h4>
                      <div class="stars">
                        <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                      </div>
                    </div>
                  </div>
                  <div class="col-lg-2 text-center">
                    <img src="assets/img/testimonials/Missy .png" class="img-fluid testimonial-img" alt="">
                  </div>
                </div>
              </div>
            </div><!-- End testimonial item -->
            

          </div>
          <div class="swiper-pagination"></div>
        </div>

      </div>

    </section><!-- /Testimonials Section -->

    <!-- Contact Section -->
    <section id="contact" class="contact section">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <h2>Contact</h2>
        <p>Feel free to contact us anytime, We will get  back to you as soon as we can.</p>
      </div><!-- End Section Title -->

      <div class="container" data-aos="fade" data-aos-delay="100">

        <div class="row gy-4">

          <div class="col-lg-4">
            <div class="info-item d-flex" data-aos="fade-up" data-aos-delay="200">
              <i class="bi bi-geo-alt flex-shrink-0"></i>
              <div>
                <h3>Address</h3>
                <p>Alabang, Muntinlupa City</p>
              </div>
            </div><!-- End Info Item -->

            <div class="info-item d-flex" data-aos="fade-up" data-aos-delay="300">
              <i class="bi bi-telephone flex-shrink-0"></i>
              <div>
                <h3>Call Us</h3>
                <p>09636462682</p>
              </div>
            </div><!-- End Info Item -->

            <div class="info-item d-flex" data-aos="fade-up" data-aos-delay="400">
              <i class="bi bi-envelope flex-shrink-0"></i>
              <div>
                <h3>Email Us</h3>
                <p>scholarflow@gmail.com</p>
              </div>
            </div><!-- End Info Item -->

          </div>

          <!-- Include SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="col-lg-8">
    <form id="contactForm" action="contact.php" method="post" class="php-email-form" data-aos="fade-up" data-aos-delay="200">
        <div class="row gy-4">
            <div class="col-md-6">
                <input type="text" name="name" class="form-control" placeholder="Your Name" required="">
            </div>

            <div class="col-md-6">
                <input type="email" class="form-control" name="email" placeholder="Your Email" required="">
            </div>

            <div class="col-md-12">
                <input type="text" class="form-control" name="subject" placeholder="Subject" required="">
            </div>

            <div class="col-md-12">
                <textarea class="form-control" name="message" rows="6" placeholder="Message" required=""></textarea>
            </div>

            <div class="col-md-12 text-center">
                <button type="button" id="submitButton">Send Message</button>
            </div>
        </div>
    </form>
</div>

<script>
    // JavaScript function to show confirmation and loading popup
    function confirmFormSubmission(event) {
        event.preventDefault(); // Prevent the default form submission

        Swal.fire({
            title: 'Are you sure?',
            text: 'Do you want to send this message?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, send it!',
            cancelButtonText: 'Cancel',
            reverseButtons: false, // Keep default button order
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading spinner after confirmation
                Swal.fire({
                    title: 'Sending...',
                    text: 'Please wait while your message is being sent.',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => {
                        Swal.showLoading(); // Show loading spinner
                    }
                });

                // Submit the form after a short delay to simulate processing time
                setTimeout(() => {
                    document.getElementById('contactForm').submit(); // Submit the form
                }, 1000); // Adjust delay as needed
            } else {
                // Show a canceled message
                Swal.fire({
                    icon: 'info',
                    title: 'Action Canceled',
                    text: 'Your message was not sent.',
                });
            }
        });
    }

    // Attach the event listener to the button
    document.getElementById('submitButton').addEventListener('click', confirmFormSubmission);
</script>



<!-- End Contact Form -->


    </section>
    <!-- /Contact Section -->

  </main>

  <footer id="footer" class="footer dark-background">

    <div class="container">
      <div class="row gy-3">
        <div class="col-lg-3 col-md-6 d-flex">
          <i class="bi bi-geo-alt icon"></i>
          <div class="address">
            <h4>Address</h4>
            <p>Alabang</p>
            <p></p>
          </div>

        </div>

        <div class="col-lg-3 col-md-6 d-flex">
          <i class="bi bi-telephone icon"></i>
          <div>
            <h4>Contact</h4>
            <p>
              <strong>Phone:</strong> <span>09636462682</span><br>
              <strong>Email:</strong> <span>scholarflow@gmail.com</span><br>
            </p>
          </div>
        </div>

        <div class="col-lg-3 col-md-6 d-flex">
          <i class="bi bi-clock icon"></i>
          <div>
            <h4>Opening Hours</h4>
            <p>
              <strong>Mon-Fri:</strong> <span>9:00 am - 5:00 pm</span><br>
            </p>
          </div>
        </div>

        <div class="col-lg-3 col-md-6">
          <h4>Follow Us</h4>
          <div class="social-links d-flex">
            <a href="https://www.facebook.com/BagongAlabang2018" class="facebook"><i class="bi bi-facebook"></i></a>
          </div>
        </div>

      </div>
    </div>

    <div class="container copyright text-center mt-4">
      <p>© <span>Copyright</span> <strong class="px-1 sitename">ScholarFlow</strong> <span>All Rights Reserved</span></p>
      <div class="credits">
        Designed by <a href="https://bootstrapmade.com/">Group1</a>
      </div>
    </div>

  </footer>

  <!-- Scroll Top -->
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Preloader -->
  <div id="preloader"></div>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>
  <script src="assets/vendor/aos/aos.js"></script>
  <script src="assets/vendor/typed.js/typed.umd.js"></script>
  <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="assets/vendor/imagesloaded/imagesloaded.pkgd.min.js"></script>
  <script src="assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>
  <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>

  <!-- Main JS File -->
  <script src="assets/js/main.js">

    
  </script>

</body>

</html>