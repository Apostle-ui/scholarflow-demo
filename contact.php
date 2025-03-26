<?php
// Import PHPMailer classes into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'assets/vendor/autoload.php';

session_start();
$logged_in_email = isset($_SESSION['user_Applicant']['email']) ? $_SESSION['user_Applicant']['email'] : null;
$logged_in_name = isset($_SESSION['user_Applicant']['name']) ? $_SESSION['user_Applicant']['name'] : null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $name = $_POST['name'];
    $email = $_POST['email'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];

    // Create a new PHPMailer instance
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'nerivancarl@gmail.com';  // Replace with your email
        $mail->Password = 'jbqu himx zmgs sghj';  // Replace with your app password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipients
        if ($logged_in_email && $logged_in_name) {
            // Use logged-in user's email and name
            $mail->setFrom($logged_in_email, $logged_in_name);
        } else {
            // Fallback to form input email and name
            $mail->setFrom($email, $name);
        }

        $mail->addAddress('nerivancarl@gmail.com', 'ScholarFlow'); // Recipient email and name

        // Content
        $mail->isHTML(true); // Set email format to HTML
        $mail->Subject = $subject;
        $mail->Body    = "<h3>Message from: $name</h3>
                          <p>Email: $email</p>
                          <p>Subject: $subject</p>
                          <p>Message: $message</p>";
        $mail->AltBody = "Message from: $name\nEmail: $email\nSubject: $subject\nMessage: $message";

        // Send the email
        if ($mail->send()) {
            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
            echo "<script>
                window.onload = function() {
                    Swal.fire({
                        icon: 'success',
                        title: 'Message Sent!',
                        text: 'Your email has been sent successfully.',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        window.location = 'index.php#contact'; // Redirect after confirmation
                    });
                }
            </script>";
        } else {
            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
            echo "<script>
                window.onload = function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Message Not Sent',
                        text: 'There was an error sending your message. Please try again later.',
                        confirmButtonText: 'OK'
                    });
                }
            </script>";
        }
    } catch (Exception $e) {
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
        echo "<script>
            window.onload = function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred: " . $mail->ErrorInfo . "',
                    confirmButtonText: 'OK'
                });
            }
        </script>";
    }
}
?>
