<?php
// Include database configuration
include('C:\xampp\htdocs\scholarportal_new\login\php\db.config.php');

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $newPassword = $_POST['new-password'];
    $rePassword = $_POST['re-password'];

    // Check if the passwords match
    if ($newPassword === $rePassword) {
        // Update the password in the database
        $stmt = $pdo->prepare("UPDATE accounts_tbl SET password = :password WHERE email = :email");
        $stmt->execute(['password' => password_hash($newPassword, PASSWORD_DEFAULT), 'email' => $_SESSION['email']]);

        // Display success modal and redirect after 3 seconds
        showModal('Success', 'Your password has been reset successfully.', true);
    } else {
        // Display error modal and reload the page after 3 seconds
        showModal('Error', 'Passwords do not match. Please try again.', false);
    }
}

function showModal($title, $message, $success = false) {
    // Set button action based on success or error
    $buttonAction = $success ? "window.location.href=\"../login.html\"" : "window.location.href=\"../fpass3.html\"";
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
                    window.location.reload();  // Reload the page after hiding the modal for error
                }, 3000); // 3000ms = 3 seconds
            };
        </script>
    </body>
    </html>";
}
?>
