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
    $sql = "SELECT * FROM half_scholar_applicant WHERE applicant_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $applicant_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $applicant = $result->fetch_assoc();

    if ($applicant) {
        $sql_eligible_applicants = "
        INSERT INTO eligible_applicants_tbl (
            applicant_id, firstname, middlename, lastname, gender, birthdate, email, contact_number, street,
            mother_firstname, mother_middlename, mother_lastname, mother_contact_number, mother_birthdate,
            father_firstname, father_middlename, father_lastname, father_contact_number, father_birthdate,
            school_level, year_level, school_name, certificate_registration, school_identification
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
    $stmt_eligible_applicants = $conn->prepare($sql_eligible_applicants);
    $stmt_eligible_applicants->bind_param(
        "isssssssssssssssssssssss",
        $applicant_id,
        $applicant['firstname'],
        $applicant['middlename'],
        $applicant['lastname'],
        $applicant['gender'],
        $applicant['birthdate'],
        $applicant['email'],
        $applicant['contact_number'],
        $applicant['street'],
        $applicant['mother_firstname'],
        $applicant['mother_middlename'],
        $applicant['mother_lastname'],
        $applicant['mother_contact_number'],
        $applicant['mother_birthdate'],
        $applicant['father_firstname'],
        $applicant['father_middlename'],
        $applicant['father_lastname'],
        $applicant['father_contact_number'],
        $applicant['father_birthdate'],
        $applicant['school_level'],
        $applicant['year_level'],
        $applicant['school_name'],
        $applicant['certificate_registration'],
        $applicant['school_identification']
    );
        $stmt_eligible_applicants->execute();

        // Delete the applicant from rejected_applicants_tbl
        $delete_sql = "DELETE FROM half_scholar_applicant WHERE applicant_id = ?";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->bind_param("i", $applicant_id);
        $delete_stmt->execute();

         // Reset auto-increment for rejected_applicants_tbl
         $maxSql = "SELECT MAX(applicant_id) FROM half_scholar_applicant";
         $result = $conn->query($maxSql);
         if ($result) {
             $row = $result->fetch_row();
             $newAutoIncrement = $row[0] + 1;
             $resetSql = "ALTER TABLE half_scholar_applicant AUTO_INCREMENT = $newAutoIncrement";
             $conn->query($resetSql);
         }

        // Close the connection
        $stmt_eligible_applicants->close();
       
        $conn->close();

        echo "<script>
            window.location.href = 'halfscholar.php';
          </script>";
    } else {
        echo "error: applicant not found";
    }
} else {
    echo "error: no id provided";
}
?>
