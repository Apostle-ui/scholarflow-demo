<?php
// Connect to the database
$servername = "localhost";
$username = "root"; // Replace with your DB username
$password = ""; // Replace with your DB password
$dbname = "scholarship_portal";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if applicant_id is provided in the GET request
if (isset($_GET['applicant_id'])) {
    $applicant_id = $_GET['applicant_id'];

    // Query to fetch applicant scores from the database
    $sql = "SELECT * FROM applicant_score WHERE applicant_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $applicant_id); // Bind the applicant_id as integer parameter

    // Execute the query
    $stmt->execute();

    // Get the result
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Fetch the applicant's score data
        $row = $result->fetch_assoc();

        // Return the data as JSON
        echo json_encode($row);
    } else {
        echo json_encode(null); // No data found for this applicant_id
    }

    // Close the statement
    $stmt->close();
} else {
    echo json_encode(["error" => "No applicant ID provided"]);
}

// Close the connection
$conn->close();
?>
