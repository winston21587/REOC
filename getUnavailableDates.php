<?php
// Include database connection
require_once 'dbConnCode.php'; // Adjust this path if necessary to your DB connection script

header('Content-Type: application/json');

// Initialize an array to store all unavailable dates
$unavailableDates = [];

try {
    // SQL to get dates from appointments where the count of appointments is 20 or more
    $query1 = "SELECT DISTINCT appointment_date FROM appointments GROUP BY appointment_date HAVING COUNT(*) >= 20";
    $result1 = $conn->query($query1);
    $fullDates = [];
    if ($result1) {
        while ($row = $result1->fetch_assoc()) {
            $fullDates[] = $row['appointment_date'];
        }
    }

    // SQL to get dates from notavail_appointment table
    $query2 = "SELECT DISTINCT unavailable_date FROM notavail_appointment";
    $result2 = $conn->query($query2);
    $notAvailableDates = [];
    if ($result2) {
        while ($row = $result2->fetch_assoc()) {
            $notAvailableDates[] = $row['unavailable_date'];
        }
    }

    // Merge and remove duplicate dates
    $unavailableDates = array_values(array_unique(array_merge($fullDates, $notAvailableDates)));

} catch (Exception $e) {
    // Handle any errors connected to the database here
    error_log("Database error: " . $e->getMessage());
    echo json_encode(['error' => 'Database error']);
    exit;
}

// Convert dates to JSON and output
echo json_encode(['unavailableDates' => $unavailableDates]);
?>
