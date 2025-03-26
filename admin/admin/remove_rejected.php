<?php
if (isset($_POST['applicant_id'])) {
    $applicant_id = $_POST['applicant_id'];

    // Create connection
    $servername = "localhost";
    $username = "root"; // Replace with your DB username
    $password = ""; // Replace with your DB password
    $dbname = "scholarship_portal";

    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Get the applicant's information from rejected_applicants_tbl
    $sql = "SELECT * FROM rejected_applicants_tbl WHERE applicant_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $applicant_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $applicant = $result->fetch_assoc();

    if ($applicant) {
        // Insert data into applicant_demographic
        $sql_demographic = "INSERT INTO applicant_demographic (applicant_id, firstname, middlename, lastname, gender, birthdate, email, contact_number, street) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt_demographic = $conn->prepare($sql_demographic);
        $stmt_demographic->bind_param(
        "issssssss",
        $applicant_id,
        $applicant['firstname'],
        $applicant['middlename'],
        $applicant['lastname'],
        $applicant['gender'],
        $applicant['birthdate'],
        $applicant['email'],
        $applicant['contact_number'],
        $applicant['street']
        );
        $stmt_demographic->execute();

        // Insert data into applicant_parent
        $sql_parent = "INSERT INTO applicant_parent (applicant_parent_id, mother_firstname, mother_middlename, mother_lastname, mother_contact_number, mother_birthdate, father_firstname, father_middlename, father_lastname, father_contact_number, father_birthdate) 
                       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt_parent = $conn->prepare($sql_parent);
        $stmt_parent->bind_param(
            "issssssssss",
            $applicant_id,
            $applicant['mother_firstname'],
            $applicant['mother_middlename'],
            $applicant['mother_lastname'],
            $applicant['mother_contact_number'],
            $applicant['mother_birthdate'],
            $applicant['father_firstname'],
            $applicant['father_middlename'],
            $applicant['father_lastname'],
            $applicant['father_contact_number'],
            $applicant['father_birthdate']
        );
        $stmt_parent->execute();

        // Insert data into applicant_school_file
        $sql_school = "INSERT INTO applicant_school_file (applicant_school_file_id, school_level, year_level, school_name, certificate_registration, school_identification) 
                       VALUES (?, ?, ?, ?, ?, ?)";
        $stmt_school = $conn->prepare($sql_school);
        $stmt_school->bind_param(
            "isssss",
            $applicant_id,
            $applicant['school_level'],
            $applicant['year_level'],
            $applicant['school_name'],
            $applicant['certificate_registration'],
            $applicant['school_identification']
        );
        $stmt_school->execute();

        // Delete the applicant from rejected_applicants_tbl
        $delete_sql = "DELETE FROM rejected_applicants_tbl WHERE applicant_id = ?";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->bind_param("i", $applicant_id);
        $delete_stmt->execute();

        // Close the connection
        $stmt_demographic->close();
        $stmt_parent->close();
        $stmt_school->close();
        $delete_stmt->close();
        $conn->close();

        echo "<script>
            window.location.href = 'rejectedapplicants.php';
          </script>";
    } else {
        echo "error: applicant not found";
    }
} else {
    echo "error: no id provided";
}
?>
