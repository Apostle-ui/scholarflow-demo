<?php
// Database connection settings
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "scholarship_portal";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error); // Return connection error
}

// Get form data
$username = isset($_POST['username']) ? $_POST['username'] : '';
$email = isset($_POST['email']) ? $_POST['email'] : '';
$name = isset($_POST['name']) ? $_POST['name'] : '';
$contact_number = isset($_POST['contact_number']) ? $_POST['contact_number'] : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';
$password2 = isset($_POST['password-2']) ? $_POST['password-2'] : ''; // Get the re-entered password

// Preparing Statements
$stmt_username = $conn->prepare("SELECT COUNT(*) FROM accounts_tbl WHERE username = ?");
$stmt_email = $conn->prepare("SELECT COUNT(*) FROM accounts_tbl WHERE email = ?");
$stmt_name = $conn->prepare("SELECT COUNT(*) FROM accounts_tbl WHERE name = ?");

// Checking if the username is already in the database
$stmt_username->bind_param("s", $username);
$stmt_username->execute();
$stmt_username->bind_result($username_count);
$stmt_username->fetch();
$stmt_username->close(); // Close the statement after use

// Checking if the email is already in the database
$stmt_email->bind_param("s", $email);
$stmt_email->execute();
$stmt_email->bind_result($email_count);
$stmt_email->fetch();
$stmt_email->close(); // Close the statement after use

// Checking if the name is already in the database
$stmt_name->bind_param("s", $name);
$stmt_name->execute();
$stmt_name->bind_result($name_count);
$stmt_name->fetch();
$stmt_name->close(); // Close the statement after use

// Handling cases when username and email exist
if ($username_count > 0 && $email_count > 0) {
    showModal("Username and Email Exist", "The username and email you entered are already registered. Please use a different username and email.");
    exit();
}

// Handling case when the username exists
if ($username_count > 0) {
    showModal("Username Exists", "The username you entered is already taken. Please choose a different username.");
    exit();
}

// Handling case when the email exists
if ($email_count > 0) {
    showModal("Email Exists", "The email you entered is already registered. Please use a different email or log in.");
    exit();
}

// Handling case when the email exists
if ($name_count > 0) {
    showModal("Name Exists", "The name you entered is already registered. Please use a different name or log in.");
    exit();
}

// Hashing the password using Argon2
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Inserting Data into Database
$stmt = $conn->prepare("INSERT INTO accounts_tbl (username, email, password, name, contact_number) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $username, $email, $hashed_password, $name, $contact_number);

// Execute the statement
if ($stmt->execute()) {
    // Success Modal
    showModal("Registration Successful", "You have successfully registered! Do you want to log in or register again?", true);
} else {
    echo "Error: " . $stmt->error; // Display error message
}

// Close the statement and connection
$stmt->close();
$conn->close();

function showModal($title, $message, $success = false) {
    $button1 = $success ? "<button type='button' class='btn btn-primary' onclick='redirectToLogin()'>Log In</button>" : "<button type='button' class='btn btn-secondary' data-bs-dismiss='modal' onclick='redirectToSignup()'>Close</button>";
    $button2 = $success ? "<button type='button' class='btn btn-secondary' data-bs-dismiss='modal' onclick='redirectToSignup()'>Register Again</button>" : "";
    
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
                        <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                    </div>
                    <div class='modal-body'>
                        <p>$message</p>
                    </div>
                    <div class='modal-footer'>
                        $button1
                        $button2
                    </div>
                </div>
            </div>
        </div>

        <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js'></script>
        <script>
            window.onload = function() {
                var modal = new bootstrap.Modal(document.querySelector('.modal'));
                modal.show();
            };
            function redirectToLogin() {
                window.location.href = 'login.html';
            }
            function redirectToSignup() {
                window.location.href = 'signup.html';
            }
        </script>
    </body>
    </html>";
}
?>
