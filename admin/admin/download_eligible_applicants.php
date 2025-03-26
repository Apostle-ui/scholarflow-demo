<?php
// Include PHPExcel library (or PhpSpreadsheet)
require '../../assets/vendor/autoload.php'; // Update with the actual path

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Database connection
$servername = "localhost";
$username = "root"; // Replace with your DB username
$password = "";     // Replace with your DB password
$dbname = "scholarship_portal";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to fetch eligible applicant data
$sql = "SELECT 
            applicant_id, firstname, middlename, lastname, gender, birthdate, email, contact_number, street, 
            mother_firstname, mother_middlename, mother_lastname, mother_contact_number, mother_birthdate, 
            father_firstname, father_middlename, father_lastname, father_contact_number, father_birthdate, 
            school_level, year_level, school_name, certificate_registration, school_identification 
        FROM eligible_applicants_tbl";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Create new Spreadsheet
    $spreadsheet = new Spreadsheet();

    // Sheet 1: Demographic
    $demographicHeaders = [
        'Applicant ID', 'First Name', 'Middle Name', 'Last Name', 'Gender', 'Birthdate', 'Email', 'Contact Number', 'Street'
    ];
    $sheet1 = $spreadsheet->getActiveSheet();
    $sheet1->setTitle('Demographic');
    $sheet1->setCellValue('A1', 'Eligible Applicants - Demographic'); // Add header
    $sheet1->mergeCells('A1:I1'); // Merge the header across the columns
    $sheet1->getStyle('A1')->getFont()->setBold(true)->setSize(14); // Style header
    $sheet1->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER); // Center header
    $sheet1->fromArray($demographicHeaders, null, 'A2'); // Adjust headers position to row 2

    // Auto-size columns for sheet 1
    foreach(range('A', 'I') as $col) {
        $sheet1->getColumnDimension($col)->setAutoSize(true);
    }

    // Sheet 2: Parent (Separate Mother and Father data)
    $parentHeaders = [
        'Applicant ID', 'First Name', 'Middle Name', 'Last Name',
        'Mother First Name', 'Mother Middle Name', 'Mother Last Name', 'Mother Contact Number', 'Mother Birthdate',
        'Father First Name', 'Father Middle Name', 'Father Last Name', 'Father Contact Number', 'Father Birthdate'
    ];
    $sheet2 = $spreadsheet->createSheet();
    $sheet2->setTitle('Parent');
    $sheet2->setCellValue('A1', 'Eligible Applicants - Parent Data'); // Add header
    $sheet2->mergeCells('A1:N1'); // Merge the header across the columns
    $sheet2->getStyle('A1')->getFont()->setBold(true)->setSize(14); // Style header
    $sheet2->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER); // Center header
    $sheet2->fromArray($parentHeaders, null, 'A2'); // Adjust headers position to row 2

    // Auto-size columns for sheet 2
    foreach(range('A', 'N') as $col) {
        $sheet2->getColumnDimension($col)->setAutoSize(true);
    }

    // Sheet 3: School
    $schoolHeaders = [
        'Applicant ID', 'First Name', 'Middle Name', 'Last Name',
        'School Level', 'Year Level', 'School Name', 'Certificate Registration', 'School Identification'
    ];
    $sheet3 = $spreadsheet->createSheet();
    $sheet3->setTitle('School');
    $sheet3->setCellValue('A1', 'Eligible Applicants - School Data'); // Add header
    $sheet3->mergeCells('A1:I1'); // Merge the header across the columns
    $sheet3->getStyle('A1')->getFont()->setBold(true)->setSize(14); // Style header
    $sheet3->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER); // Center header
    $sheet3->fromArray($schoolHeaders, null, 'A2'); // Adjust headers position to row 2

    // Auto-size columns for sheet 3
    foreach(range('A', 'I') as $col) {
        $sheet3->getColumnDimension($col)->setAutoSize(true);
    }

    // Populate data for each sheet
    $row1 = 3; $row2 = 3; $row3 = 3;
    while ($row = $result->fetch_assoc()) {
        // Demographic data
        $sheet1->fromArray([
            $row['applicant_id'], $row['firstname'], $row['middlename'], $row['lastname'], 
            $row['gender'], $row['birthdate'], $row['email'], $row['contact_number'], $row['street']
        ], null, 'A' . $row1);
        $row1++;

        // Parent data (Separate Mother and Father data)
        $sheet2->fromArray([
            $row['applicant_id'], $row['firstname'], $row['middlename'], $row['lastname'], 
            $row['mother_firstname'], $row['mother_middlename'], $row['mother_lastname'], $row['mother_contact_number'], $row['mother_birthdate'], 
            $row['father_firstname'], $row['father_middlename'], $row['father_lastname'], $row['father_contact_number'], $row['father_birthdate']
        ], null, 'A' . $row2);
        $row2++;

        // School data
        $sheet3->fromArray([
            $row['applicant_id'], $row['firstname'], $row['middlename'], $row['lastname'], 
            $row['school_level'], $row['year_level'], $row['school_name'], $row['certificate_registration'], $row['school_identification']
        ], null, 'A' . $row3);
        $row3++;
    }

    // Set header for download
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="Eligible Applicants.xlsx"');
    header('Cache-Control: max-age=0');

    // Write to output
    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
} else {
    echo "No data found.";
}

$conn->close();
?>
