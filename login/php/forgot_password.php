<?php
// Include database configuration and PHPMailer
include('C:\xampp\htdocs\scholarportal_new\login\php\db.config.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../../assets/vendor/autoload.php';

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    // Check if email exists in the database
    $stmt = $pdo->prepare("SELECT * FROM accounts_tbl WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Generate a 4-digit code
        $code = rand(1000, 9999);

        // Save the code in the database
        $stmt = $pdo->prepare("UPDATE accounts_tbl SET reset_code = :code WHERE email = :email");
        $stmt->execute(['code' => $code, 'email' => $email]);

        // Send the email using PHPMailer
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'nerivancarl@gmail.com';  // Replace with your email
            $mail->Password = 'jbqu himx zmgs sghj';  // Replace with your app password or email password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Recipients
            $mail->setFrom('no-reply@example.com', 'ScholarFlow');
            $mail->addAddress($email);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Request';
            $mail->Body = "We received a request to reset your password. Your 4-digit code is: <b>$code</b>";

            $mail->send();

            // Store email and code in session
            $_SESSION['email'] = $email;
            $_SESSION['reset_code'] = $code;

            // Display success modal
            showModal('Success', 'A 4-digit code has been sent to your email.', true);
        } catch (Exception $e) {
            $_SESSION['message'] = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            // Display error modal
            showModal('Error', 'There was an issue sending the email. Please try again.', false);
        }
    } else {
        // Display error modal if email not found
        showModal('Error', 'The email was not found in our records. Please check and try again.', false);
    }
}

function showModal($title, $message, $success = false) {
    // Button action changes depending on success or error
    $buttonAction = $success ? "window.location.href=\"../fpass2.html\"" : "window.location.href=\"../fpass.html\"";
    $button = "<button type='button' class='btn btn-primary' onclick='$buttonAction'>OK</button>";
    
    echo "
    <!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
        <link href='styles/signup.css' rel ='stylesheet'>
        <title>$title</title>
        <link rel='icon' sizes='180x180' href='pic/favicon.icon'>
        <link rel='icon' type='image/png' sizes='32x32' href='pic/favicon-32x32.png'>
        <link rel='icon' type='image/png' sizes='16x16' href='pic/favicon-16x16.png'>
        <link rel='manifest' href='pic/site.webmanifest'>
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
                
                // Close the modal after 3 seconds and reload the page if it's an error
                setTimeout(function() {
                    modal.hide();
                    window.location.reload();  // Reload the page after hiding the modal
                }, 3000); // 3000ms = 3 seconds
            };
        </script>
    </body>
    </html>";
}
?>
