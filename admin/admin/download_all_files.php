<?php
require('../../assets/vendor/fpdf/fpdf.php'); // Include the FPDF library
require_once('../../assets/vendor/phpword/src/PhpWord/PhpWord.php');
require_once('../../assets/vendor/phpword/src/PhpWord/IOFactory.php');
require_once ('../../assets/vendor/autoload.php'); // Use Composer's autoloader

use PhpOffice\PhpWord\IOFactory;
use Dompdf\Dompdf;
use Dompdf\Options;

$zip = new ZipArchive(); // To create the ZIP archive

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

// Fetch all applicants
$sql_all_applicants = "SELECT applicant_id, firstname, lastname FROM applicant_demographic";
$result_all_applicants = $conn->query($sql_all_applicants);

if ($result_all_applicants->num_rows === 0) {
    die("No applicants found.");
}

// Create an array to store the paths of ZIP files
$zip_files = [];

while ($applicant_row = $result_all_applicants->fetch_assoc()) {
    $applicant_id = $applicant_row['applicant_id'];
    $applicant_firstname = $applicant_row['firstname'];
    $applicant_lastname = $applicant_row['lastname'];

    // Fetch applicant information
    $sql = "SELECT firstname, middlename, lastname, gender, birthdate, email, contact_number, province, city, barangay, street 
            FROM applicant_demographic WHERE applicant_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $applicant_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $applicant = $result->fetch_assoc();

    // Fetch parent data
    $sql_parent = "SELECT mother_firstname, mother_middlename, mother_lastname, mother_contact_number, mother_birthdate, 
                          father_firstname, father_middlename, father_lastname, father_contact_number, father_birthdate 
                   FROM applicant_parent WHERE applicant_parent_id = ?";
    $stmt_parent = $conn->prepare($sql_parent);
    $stmt_parent->bind_param("s", $applicant_id);
    $stmt_parent->execute();
    $result_parent = $stmt_parent->get_result();
    $parent = $result_parent->fetch_assoc();

    // Fetch school data
    $sql_school = "SELECT school_level, year_level, school_name, certificate_registration, school_identification 
                   FROM applicant_school_file WHERE applicant_school_file_id = ?";
    $stmt_school = $conn->prepare($sql_school);
    $stmt_school->bind_param("s", $applicant_id);
    $stmt_school->execute();
    $result_school = $stmt_school->get_result();
    $school = $result_school->fetch_assoc();

    // Temporary directory to store PDFs and converted files
    $temp_dir = '../../php/temp/';
    if (!is_dir($temp_dir)) {
        mkdir($temp_dir, 0777, true); // Create temp directory if it doesn't exist
    }

    // Generate a PDF with applicant information
    $pdf_info_filename = $temp_dir . $applicant_firstname . '_' . $applicant_lastname . '_info.pdf';
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 12);

    // Applicant Information
    $pdf->Cell(200, 10, "Applicant Information", 0, 1, 'C');
    $pdf->Ln(10);
    $pdf->Cell(100, 10, "Name: " . $applicant['firstname'] . " " . $applicant['middlename'] . " " . $applicant['lastname']);
    $pdf->Ln(10);
    $pdf->Cell(100, 10, "Gender: " . $applicant['gender']);
    $pdf->Ln(10);
    $pdf->Cell(100, 10, "Birthdate: " . $applicant['birthdate']);
    $pdf->Ln(10);
    $pdf->Cell(100, 10, "Email: " . $applicant['email']);
    $pdf->Ln(10);
    $pdf->Cell(100, 10, "Contact Number: " . $applicant['contact_number']);
    $pdf->Ln(10);
    $pdf->Cell(100, 10, "Address: " . $applicant['street'] . ", " . "Alabang" . ", " . "Muntinlupa City" . ", " . "Metro Manila");
    $pdf->Ln(10);

    $pdf->Cell(200, 10, " ", 0, 1, 'L');

    // Parent Information
    $pdf->Cell(200, 10, "Parent Information", 0, 1, 'C');
    $pdf->Ln(10);
    $pdf->Cell(100, 10, "Mother: " . $parent['mother_firstname'] . " " . $parent['mother_middlename'] . " " . $parent['mother_lastname'] . " (Contact: " . $parent['mother_contact_number'] . ", Birthdate: " . $parent['mother_birthdate'] . ")");
    $pdf->Ln(10);
    $pdf->Cell(100, 10, "Father: " . $parent['father_firstname'] . " " . $parent['father_middlename'] . " " . $parent['father_lastname'] . " (Contact: " . $parent['father_contact_number'] . ", Birthdate: " . $parent['father_birthdate'] . ")");
    $pdf->Ln(10);

    $pdf->Cell(200, 10, " ", 0, 1, 'L');

    // School Information
    $pdf->Cell(200, 10, "School Information", 0, 1, 'C');
    $pdf->Ln(10);
    $pdf->Cell(100, 10, "School Level: " . $school['school_level']);
    $pdf->Ln(10);
    $pdf->Cell(100, 10, "Year Level: " . $school['year_level']);
    $pdf->Ln(10);
    $pdf->Cell(100, 10, "School Name: " . $school['school_name']);
    $pdf->Ln(10);

    // Save applicant information to PDF
    $pdf->Output('F', $pdf_info_filename);

    // Prepare files for the ZIP
    $files_for_zip = [$pdf_info_filename];

    // Add uploaded files to the ZIP
    $certificate_registration = "../../php/uploads/" . $school['certificate_registration'];
    $school_identification = "../../php/uploads/" . $school['school_identification'];

    if (isImage($certificate_registration)) {
        $files_for_zip[] = convertImageToPdf($certificate_registration, $temp_dir);
    } elseif (file_exists($certificate_registration)) {
        $files_for_zip[] = $certificate_registration;
    }

    if (isImage($school_identification)) {
        $files_for_zip[] = convertImageToPdf($school_identification, $temp_dir);
    } elseif (file_exists($school_identification)) {
        $files_for_zip[] = $school_identification;
    }

    // Check if DOCX files exist and convert them to PDF before adding to ZIP
    $docx_files = [$certificate_registration, $school_identification];

    // First, remove DOCX files from the $files_for_zip array (if they exist)
    $files_for_zip = array_filter($files_for_zip, function ($file) {
        return pathinfo($file, PATHINFO_EXTENSION) !== 'docx';
    });

    // Then, process the DOCX files and convert them to PDFs before adding
    foreach ($docx_files as $docx_file) {
        if (pathinfo($docx_file, PATHINFO_EXTENSION) === 'docx') {
            // Convert DOCX to PDF and add only the PDF to the zip
            $converted_docx = convertDocxToPdf($docx_file, $temp_dir);
            $files_for_zip[] = $converted_docx; // Add the converted DOCX PDF to ZIP
        }
    }

    // Check if the required files are available for the ZIP
    if (empty($files_for_zip)) {
        die("No valid files to create the ZIP.");
    }

    // Create a ZIP file for each applicant
    $zip_filename = '../../php/uploads/' . $applicant_firstname . " " . $applicant_lastname . ".zip";
    if ($zip->open($zip_filename, ZipArchive::CREATE) === TRUE) {
        foreach ($files_for_zip as $file) {
            if (file_exists($file)) {
                $zip->addFile($file, basename($file)); // Add files to ZIP
            }
        }

        $zip->close();

        // Add the applicant's ZIP file to the list of ZIPs to send later
        $zip_files[] = $zip_filename;
    } else {
        die("Failed to create ZIP file for " . $applicant_firstname . " " . $applicant_lastname);
    }
}

// After the loop, send all ZIP files in one archive
if (!empty($zip_files)) {
    // Create a final ZIP archive containing all individual ZIP files
    $final_zip = new ZipArchive();
    $final_zip_filename = '../../php/uploads/all_applicants.zip';

    if ($final_zip->open($final_zip_filename, ZipArchive::CREATE) === TRUE) {
        foreach ($zip_files as $file) {
            if (file_exists($file)) {
                $final_zip->addFile($file, basename($file)); // Add files to final ZIP
            }
        }

        $final_zip->close();

        // Send the final ZIP file to the browser
        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="' . basename($final_zip_filename) . '"');
        header('Content-Length: ' . filesize($final_zip_filename));

        readfile($final_zip_filename);

        // Clean up temporary files
        foreach ($zip_files as $file) {
            unlink($file);
        }
        unlink($final_zip_filename); // Delete the final ZIP file

        // Clean up temp directory
        $files = glob($temp_dir . '*'); 
        foreach($files as $file) {
            unlink($file);
        }
        rmdir($temp_dir); // Remove temp directory
    } else {
        die("Failed to create the final ZIP file.");
    }
}

$conn->close();

// Function to check if the file is an image (PNG, JPG)
function isImage($file_path)
{
    $image_types = ['png', 'jpg', 'jpeg'];
    $extension = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
    return in_array($extension, $image_types);
}

// Function to convert image (PNG/JPG) to PDF
function convertImageToPdf($image_path, $temp_dir)
{
    $pdf_filename = $temp_dir . basename($image_path, '.' . pathinfo($image_path, PATHINFO_EXTENSION)) . '.pdf';

    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->Image($image_path, 10, 10, 190);

    $pdf->Output('F', $pdf_filename);
    return $pdf_filename;
}

// Function to convert DOCX to PDF using PhpWord and Dompdf
function convertDocxToPdf($docx_path, $temp_dir)
{
    $phpWord = \PhpOffice\PhpWord\IOFactory::load($docx_path);

    // Save DOCX as HTML (intermediate format)
    $html_filename = $temp_dir . basename($docx_path, '.docx') . '.html';
    $phpWord->save($html_filename, 'HTML');

    // Convert HTML to PDF using Dompdf
    $pdf_filename = $temp_dir . basename($docx_path, '.docx') . '.pdf';
    $options = new Options();
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isPhpEnabled', true);
    
    $dompdf = new Dompdf($options);
    $dompdf->loadHtml(file_get_contents($html_filename));
    $dompdf->render();
    file_put_contents($pdf_filename, $dompdf->output());

    // Clean up the intermediate HTML file
    unlink($html_filename);

    return $pdf_filename;
}
?>
