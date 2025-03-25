<?php

// Database connection function (reusable)
function connectDatabase() {
    $servername = "localhost";
    $username = "admin";
    $password = "admin";
    $database = "ReocWebDB";

    try {
        return new PDO("mysql:host=$servername;dbname=$database", $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
    } catch (PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
}

// Get next available consultation
function getAvailableConsultation($pdo) {
    $maxWeeksToCheck = 4; // Check up to 4 weeks ahead
    $weekdays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
    
    $currentDate = new DateTime(); // Get today's date
    $currentWeekday = $currentDate->format('l'); // Get today's weekday (e.g., Monday)

    for ($weekOffset = 0; $weekOffset < $maxWeeksToCheck; $weekOffset++) {
        foreach ($weekdays as $weekday) {
            // Calculate the next available weekday
            $daysToAdd = (array_search($weekday, $weekdays) - array_search($currentWeekday, $weekdays) + 7) % 7;
            if ($daysToAdd === 0 && $weekOffset > 0) {
                $daysToAdd = 7; // Move to the next week if the same weekday
            }
            
            $appointmentDate = (clone $currentDate)->modify("+$daysToAdd days")->format('Y-m-d');

            // Check if the slot is available on this exact date
            $query = "SELECT id, consultant_id, weekday, start_time, end_time
                      FROM consultant_availability 
                      WHERE status = 'open' AND weekday = :weekday
                      ORDER BY start_time ASC
                      LIMIT 1";

            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':weekday', $weekday, PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetch();

            if ($result) {
                $result['next_appointment_date'] = $appointmentDate; // Attach computed date
                return $result;
            }
        }
        $currentDate->modify('+7 days'); // Move to the next week
    }

    return false; // No available appointment in the next 4 weeks
}

// Mark slot as booked
function setConsultSchedStatus($pdo, $consult_id) {
    $query = "UPDATE consultant_availability SET status = 'booked' WHERE id = :consult_id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(":consult_id", $consult_id, PDO::PARAM_INT);
    return $stmt->execute();
}

// Insert into appointments table
function setAppointment($pdo, $researcher_title_id, $availability_id, $appointment_date) {
    $query = "INSERT INTO appointments (researcher_title_id, availability_id, appointment_date)
              VALUES (:researcher_title_id, :availability_id, :appointment_date)";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':researcher_title_id', $researcher_title_id, PDO::PARAM_INT);
    $stmt->bindParam(':availability_id', $availability_id, PDO::PARAM_INT);
    $stmt->bindParam(':appointment_date', $appointment_date);
    return $stmt->execute();
}

// MAIN EXECUTION
$pdo = connectDatabase(); // Use a single database connection
$researcher_title_id = 33; // Replace with actual researcher ID

// Step 1: Get next available consultation slot
$consultation = getAvailableConsultation($pdo);

if ($consultation) {
    $consultID = $consultation['id'];
    $date = $consultation['next_appointment_date'];

    // Step 2: Mark the slot as "booked"
    setConsultSchedStatus($pdo, $consultID);

    // Step 3: Create the appointment
    setAppointment($pdo, $researcher_title_id, $consultID, $date);

    echo "✅ Appointment booked for " . $date . " at " . $consultation['start_time'];
} else {
    echo "❌ No available appointments in the next 4 weeks.";
}
?>
