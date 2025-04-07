<?php
require '../../vendor/autoload.php';

use setasign\Fpdi\Fpdi;

// Create new PDF instance
$pdf = new Fpdi();

// Load the existing PDF
$pdf->AddPage();
$pdf->setSourceFile("Result-of-Review-Form.pdf");
$tplId = $pdf->importPage(1);
$pdf->useTemplate($tplId);

// Set font and color
$pdf->SetFont('Arial', '', 12);
$pdf->SetTextColor(0, 0, 0);

// Example user inputs (these would come from your form normally)
$title = $_POST['title'] ?? "Sample Title";
$reviewType = $_POST['review_type'] ?? "EXPEDITED";
$numSets = $_POST['num_sets'] ?? "2";
$envelope = $_POST['envelope_type'] ?? "long brown";
$protocolChecks = $_POST['ethics_review_1'] ?? []; 
$consentChecks = $_POST['ethics_review_2'] ?? []; 


$x = 12.3;
$check = 'X';

function checkAndMark($pdf, $x, $y, $value, $checks) {
    return in_array($value, $checks) ? $pdf->SetXY($x, $y) & $pdf->Write(0, 'X') : null;
}
$pdf->SetXY(37, 63);  // Adjust XY coordinates based on where the title is
$pdf->Write(0, $title);

$pdf->SetXY(47, 70);  // Adjust to where review type text should be
$pdf->Write(0, $reviewType);

$pdf->SetXY(105, 225); // Adjust to where num_sets appears in paragraph
$pdf->Write(0, $numSets);

$pdf->SetXY(20, 229); // Envelope type placement
$pdf->Write(0, $envelope);

$pdf->SetFont('Arial', '', 15);
// Now we insert the text over the PDF in fixed positions
// Protocol/Proposal section (values 1–6)
checkAndMark($pdf, $x, 96.5, '1', $protocolChecks);
checkAndMark($pdf, $x, 101, '2', $protocolChecks);
checkAndMark($pdf, $x, 105.49, '3', $protocolChecks);
checkAndMark($pdf, $x, 109.98, '4', $protocolChecks);
checkAndMark($pdf, $x, 114.47, '5', $protocolChecks);
checkAndMark($pdf, $x, 118.96, '6', $protocolChecks);

// Informed Consent section (values 1–13)
checkAndMark($pdf, $x, 154.5, '1', $consentChecks);
checkAndMark($pdf, $x, 158.99, '2', $consentChecks);
checkAndMark($pdf, $x, 163.48, '3', $consentChecks);
checkAndMark($pdf, $x, 167.97, '4', $consentChecks);
checkAndMark($pdf, $x, 172.46, '5', $consentChecks);
checkAndMark($pdf, $x, 176.95, '6', $consentChecks);
checkAndMark($pdf, $x, 181.44, '7', $consentChecks);
checkAndMark($pdf, $x, 185.93, '8', $consentChecks);
checkAndMark($pdf, $x, 190.42, '9', $consentChecks);
checkAndMark($pdf, $x, 194.91, '10', $consentChecks);
checkAndMark($pdf, $x, 199.4, '11', $consentChecks);
// checkAndMark($pdf, $x, 203.89, '12', $consentChecks); // if needed
checkAndMark($pdf, $x, 207, '12', $consentChecks);
checkAndMark($pdf, $x, 212.87, '13', $consentChecks);

// Recommended Actions (values A and B, for example)
// checkAndMark($pdf, 25, 274, 'A', $recommendedActions);
// checkAndMark($pdf, 108, 274, 'B', $recommendedActions);
$pdf->SetXY($x, 412.87);
if (!empty($_POST)) {
    $pdf->Write(0, print_r($_POST, true));
} else {
    $pdf->Write(0, 'No POST data received.');
}


// Output the new PDF
$pdf->Output('I', 'Filled_Result_Form.pdf');  // 'I' to display in browser
