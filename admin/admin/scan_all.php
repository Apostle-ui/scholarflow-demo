<?php
// Include necessary libraries
require_once '../../assets/vendor/autoload.php';
use Smalot\PdfParser\Parser;
use thiagoalessio\TesseractOCR\TesseractOCR;
use PhpOffice\PhpWord\IOFactory;

// Database connection
$servername = "localhost";
$username = "root"; // Replace with your database username
$password = "";     // Replace with your database password
$dbname = "scholarship_portal";

// Create a database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Start the session at the top of your script
session_start();

// Get the admin's username from the session, with a fallback to 'unknown' if not set
$admin_username = isset($_SESSION['username']) ? $_SESSION['username'] : 'unknown';

// Fetch all applicants
$sql = "SELECT applicant_id FROM applicant_demographic";
$result = $conn->query($sql);

// Loop through each applicant
while ($row = $result->fetch_assoc()) {
    $applicant_id = $row['applicant_id'];

    // Fetch school data using the applicant_id
    $sql_school = "SELECT school_identification, certificate_registration 
                   FROM applicant_school_file WHERE applicant_school_file_id = ?";
    $stmt_school = $conn->prepare($sql_school);
    $stmt_school->bind_param("s", $applicant_id);
    $stmt_school->execute();
    $result_school = $stmt_school->get_result();

    if ($result_school->num_rows === 0) {
        continue; // Skip this applicant if no school data is found
    }

    $school = $result_school->fetch_assoc();

    // Get the file paths
    $school_identification_path = "../../php/uploads/" . $school['school_identification'];
    $certificate_registration_path = "../../php/uploads/" . $school['certificate_registration'];
    $referenceFile = "../../php/cor/cor.pdf"; // Reference file for certificate_registration

    // Check if the files exist
    if (!file_exists($school_identification_path) || !file_exists($certificate_registration_path)) {
        continue; // Skip this applicant if files do not exist
    }

    // Extract text from school_identification (for address check)
    $fileTypeSchool = strtolower(pathinfo($school_identification_path, PATHINFO_EXTENSION));
    $school_text = "";

    if (in_array($fileTypeSchool, ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'bmp', 'docx'])) {
        if ($fileTypeSchool == 'pdf') {
            $parser = new Parser();
            try {
                $pdf = $parser->parseFile($school_identification_path);
                $school_text = $pdf->getText();
                if (empty($school_text)) {
                    $school_text = (new TesseractOCR($school_identification_path))->lang('eng')->run();
                }
            } catch (Exception $e) {
                continue; // Skip this applicant if there is an error parsing the file
            }
        } elseif (in_array($fileTypeSchool, ['jpg', 'jpeg', 'png', 'gif', 'bmp'])) {
            $school_text = (new TesseractOCR($school_identification_path))->lang('eng')->run();
        } elseif ($fileTypeSchool === 'docx') {
            $school_text = extractTextFromDocx($school_identification_path);
        }
    }

    // Check if the address "Alabang, Muntinlupa City" is present in the school identification file
    $school_textNoSpaces = str_replace(' ', '', strtolower($school_text));
    $targetAddress = 'alabang,muntinlupacity';
    $addressFound = (strpos($school_textNoSpaces, $targetAddress) !== false);

    // Extract text from certificate_registration and verify against reference file
    $uploadedText = '';
    $fileTypeCert = strtolower(pathinfo($certificate_registration_path, PATHINFO_EXTENSION));
    if ($fileTypeCert === 'pdf') {
        $uploadedText = extractTextFromPDF($certificate_registration_path);
    } elseif (in_array($fileTypeCert, ['jpg', 'jpeg', 'png', 'bmp', 'gif'])) {
        $uploadedText = extractTextFromImage($certificate_registration_path);
    } elseif ($fileTypeCert === 'docx') {
        $uploadedText = extractTextFromDocx($certificate_registration_path);
    } else {
        continue; // Skip this applicant if the certificate registration file type is unsupported
    }

    $referenceText = extractTextFromPDF($referenceFile);

    // Compare the extracted text with the reference file
    $similarity = 0;
    if (!empty($uploadedText) && !empty($referenceText)) {
        similar_text(strtolower($referenceText), strtolower($uploadedText), $percent);
        $similarity = $percent;
    }

    // Determine the status based on conditions
    if ($addressFound && $similarity > 90) {
        // Proceed with inserting applicant into the appropriate table
        $status = 'eligible';
        $table_name = 'eligible_applicants_tbl';
        $column_name = 'accepted_by';
        $time = 'accepted_time';
    } else {
        $status = 'rejected';
        $table_name = 'rejected_applicants_tbl';
        $column_name = 'rejected_by';
        $time = 'rejected_time';
    }
    

    // Fetch all applicant data
    $sql_fetch = "SELECT 
        ad.applicant_id, ad.firstname, ad.middlename, ad.lastname, ad.gender, ad.birthdate, ad.email, ad.contact_number, ad.street,
        ap.mother_firstname, ap.mother_middlename, ap.mother_lastname, ap.mother_contact_number, ap.mother_birthdate,
        ap.father_firstname, ap.father_middlename, ap.father_lastname, ap.father_contact_number, ap.father_birthdate,
        asf.school_level, asf.year_level, asf.school_name, asf.certificate_registration, asf.school_identification
    FROM applicant_demographic ad
    LEFT JOIN applicant_parent ap ON ad.applicant_id = ap.applicant_parent_id
    LEFT JOIN applicant_school_file asf ON ad.applicant_id = asf.applicant_school_file_id
    WHERE ad.applicant_id=?";
    $stmt_fetch = $conn->prepare($sql_fetch);
    $stmt_fetch->bind_param("i", $applicant_id);
    $stmt_fetch->execute();
    $applicant_data = $stmt_fetch->get_result()->fetch_assoc();

    // Insert into the appropriate table based on the status (WITHOUT applicant_id in INSERT)
    $sql_insert = "INSERT INTO $table_name (
        applicant_id, firstname, middlename, lastname, gender, birthdate, email, contact_number, street,
        mother_firstname, mother_middlename, mother_lastname, mother_contact_number, mother_birthdate,
        father_firstname, father_middlename, father_lastname, father_contact_number, father_birthdate,
        school_level, year_level, school_name, certificate_registration, school_identification, $column_name, $time
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

    // Bind the parameters (exclude applicant_id from here)
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bind_param(
        "issssssssssssssssssssssss",
        $applicant_data['applicant_id'], $applicant_data['firstname'], $applicant_data['middlename'], $applicant_data['lastname'],
        $applicant_data['gender'], $applicant_data['birthdate'], $applicant_data['email'], 
        $applicant_data['contact_number'], $applicant_data['street'], 
        $applicant_data['mother_firstname'], $applicant_data['mother_middlename'], $applicant_data['mother_lastname'],
        $applicant_data['mother_contact_number'], $applicant_data['mother_birthdate'],
        $applicant_data['father_firstname'], $applicant_data['father_middlename'], $applicant_data['father_lastname'],
        $applicant_data['father_contact_number'], $applicant_data['father_birthdate'],
        $applicant_data['school_level'], $applicant_data['year_level'], $applicant_data['school_name'],
        $applicant_data['certificate_registration'], $applicant_data['school_identification'],
        $admin_username
    );
    $stmt_insert->execute();
    $stmt_insert->close();

    // Delete the applicant from the tables after moving data
    $sql_delete_demographic = "DELETE FROM applicant_demographic WHERE applicant_id=?";
    $sql_delete_parent = "DELETE FROM applicant_parent WHERE applicant_parent_id=?";
    $sql_delete_school = "DELETE FROM applicant_school_file WHERE applicant_school_file_id=?";
    $stmt_delete_demographic = $conn->prepare($sql_delete_demographic);
    $stmt_delete_parent = $conn->prepare($sql_delete_parent);
    $stmt_delete_school = $conn->prepare($sql_delete_school);

    $stmt_delete_demographic->bind_param("i", $applicant_id);
    $stmt_delete_parent->bind_param("i", $applicant_id);
    $stmt_delete_school->bind_param("i", $applicant_id);

    $stmt_delete_demographic->execute();
    $stmt_delete_parent->execute();
    $stmt_delete_school->execute();

    $stmt_delete_demographic->close();
    $stmt_delete_parent->close();
    $stmt_delete_school->close();
}

// Close the database connection
$conn->close();

// Return a response to the AJAX request
echo json_encode(['status' => 'success', 'message' => 'All applicants have been processed.']);
exit;


// Function to extract text from PDF
function extractTextFromPDF($filePath) {
    require_once '../../assets/vendor/autoload.php'; // Ensure the PDF library is installed
    $parser = new \Smalot\PdfParser\Parser();
    try {
        $pdf = $parser->parseFile($filePath);
        return $pdf->getText();
    } catch (Exception $e) {
        return '';
    }
}

// Function to extract text from image files
function extractTextFromImage($filePath) {
    // Requires Tesseract OCR or similar library
    require_once '../../assets/vendor/autoload.php'; // Ensure TesseractOCR library is installed
    try {
        $ocr = new \thiagoalessio\TesseractOCR\TesseractOCR($filePath);
        return $ocr->lang('eng')->run();
    } catch (Exception $e) {
        return '';
    }
}

// Function to extract text from .docx files
function extractTextFromDocx($filePath) {
    try {
        $phpWord = IOFactory::load($filePath);
        $text = '';
        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                if (method_exists($element, 'getText')) {
                    $text .= $element->getText() . "\n";
                }
            }
        }
        return $text;
    } catch (Exception $e) {
        return '';
    }
}

?>
