<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "scholarship_portal";

// Start session
session_start();

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password']; // Use plaintext password

    // Sanitize input
    $username = $conn->real_escape_string($username);

    // Check credentials
    $adminSql = "SELECT password, role, email, name, account_id, is_logged_in FROM accounts_tbl WHERE username = '$username'";
    $adminResult = $conn->query($adminSql);

    if ($adminResult->num_rows > 0) {
        $adminRow = $adminResult->fetch_assoc();
        $stored_password = $adminRow['password'];
        $role = $adminRow['role'];
        $email = $adminRow['email'];
        $name = $adminRow['name'];
        $account_id = $adminRow['account_id'];
        $is_logged_in = $adminRow['is_logged_in'];

        // Restrict if this specific account is already logged in
        if ($is_logged_in) {
            showModal(
                "Login Restricted",
                "This account is already logged in on another device. Please log out first.",
                'login.html'
            );
        }

        // Restrict if another account with the same role is already logged in on the same device
        if (
            ($role === 'Applicant' && isset($_SESSION['user_Applicant']) && $_SESSION['user_Applicant']['username'] !== $username) ||
            ($role === 'Organizer' && isset($_SESSION['user_Organizer']) && $_SESSION['user_Organizer']['username'] !== $username) ||
            ($role === 'Admin' && isset($_SESSION['user_Admin']) && $_SESSION['user_Admin']['username'] !== $username)
        ) {
            showModal(
                "Login Restricted",
                "Another account is already logged in on this device. Please log out first.",
                'login.html'
            );
        }

        // Verify the entered password with the stored password
        if (password_verify($password, $stored_password)) {
            // Update is_logged_in field to mark the account as logged in
            $updateLoginStatus = "UPDATE accounts_tbl SET is_logged_in = 1 WHERE username = '$username'";
            $conn->query($updateLoginStatus);

            // Use role-specific session keys to avoid overwriting data
            $_SESSION['user_' . $role] = [
                'username' => $username,
                'role' => $role,
                'email' => $email,
                'name' => $name,
                'account_id' => $account_id,
            ];

            // Update the last login time
            $adminUpdateSql = "UPDATE accounts_tbl SET last_login_time = NOW() WHERE username = '$username'";
            $conn->query($adminUpdateSql);

            // Redirect based on the user's role
            if ($role == 'Applicant') {
                showModal("Login Successful", "Welcome, $name! You are now logged in as an Applicant.", 'scholarship.html', true);
            } else {
                showModal("Login Successful", "Welcome, $name! You are now logged in as an $role.", '../admin/admin/index.php', false);
            }
        } else {
            showModal("Login Failed", "Invalid password. Please try again.", 'login.html');
        }
    } else {
        showModal("Login Failed", "User not found. Please check your username and try again.", 'login.html');
    }
}

// Reusable function to display modal
function showModal($title, $message, $redirect, $showSessionAlert = false) {
    $redirect = htmlspecialchars($redirect, ENT_QUOTES, 'UTF-8'); // Escape the redirect URL
    echo "
    <!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
        <link rel='stylesheet' href='styles/login.css'>
        <title>$title</title>
    </head>
    <body>
        <div class='modal fade' id='customModal' tabindex='-1' aria-labelledby='customModalLabel' aria-hidden='true'>
            <div class='modal-dialog'>
                <div class='modal-content'>
                    <div class='modal-header'>
                        <h5 class='modal-title' id='customModalLabel'>$title</h5>
                    </div>
                    <div class='modal-body'>
                        $message
                    </div>
                    <div class='modal-footer'>
                        <button type='button' class='btn btn-secondary' data-bs-dismiss='modal' onclick='redirectToPage()'>Close</button>
                    </div>
                </div>
            </div>
        </div>

        <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js'></script>
        <script>
            const redirectUrl = '$redirect';
            window.onload = function () {
                var customModal = new bootstrap.Modal(document.getElementById('customModal'));
                customModal.show();

                setTimeout(() => {
                    customModal.hide();
                    window.location.href = redirectUrl;
                }, 3000);
            };

            function redirectToPage() {
                window.location.href = redirectUrl;
            }
        </script>
    </body>
    </html>";
    exit();
}

$conn->close();
?>
