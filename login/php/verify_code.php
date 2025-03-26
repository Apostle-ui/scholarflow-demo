<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $code = $_POST['code'];

    // Check if the entered code matches the stored code
    if ($code == $_SESSION['reset_code']) {
        // Code Verified Successfully, show success modal with delay
        showModal('Success', 'The code has been verified successfully.', true);
    } else {
        // Invalid Code, show error modal with delay
        showModal('Error', 'Invalid code entered. Please try again.', false);
    }
}

function showModal($title, $message, $success = false) {
    // Set button action based on success or error
    $buttonAction = $success ? "window.location.href=\"../fpass3.html\"" : "window.location.href=\"../fpass2.html\"";
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
