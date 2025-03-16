<?php
include './Database.php';
class Appointment extends Database {


    





    // function getAvailableSlots($consultant_id, $date) {
    
    //     // Get the weekday from the selected date
    //     $weekday = date('l', strtotime($date));
    
    //     // Fetch all time slots for the given weekday
    //     $query = "SELECT ca.start_time, ca.end_time 
    //               FROM consultant_availability ca
    //               WHERE ca.consultant_id = :consultant_id AND ca.weekday = :weekday
    //               AND NOT EXISTS (
    //                   SELECT 1 FROM appointments a 
    //                   WHERE a.consultant_id = ca.consultant_id 
    //                   AND a.appointment_date = :date 
    //                   AND a.start_time = ca.start_time
    //               )";
    
    //     $stmt = $this->pdo->prepare($query);
    //     $stmt->execute([
    //         ':consultant_id' => $consultant_id,
    //         ':weekday' => $weekday,
    //         ':date' => $date
    //     ]);
    
    //     return $stmt->fetchAll(PDO::FETCH_ASSOC);
    // }


    // function bookAppointment($researcher_id, $consultant_id, $appointment_date, $start_time, $end_time) {

    
    //     // Check if the selected time slot is available
    //     $query = "SELECT 1 FROM appointments 
    //               WHERE consultant_id = :consultant_id 
    //               AND appointment_date = :appointment_date 
    //               AND start_time = :start_time";
    
    //     $stmt = $this->pdo->prepare($query);
    //     $stmt->execute([
    //         ':consultant_id' => $consultant_id,
    //         ':appointment_date' => $appointment_date,
    //         ':start_time' => $start_time
    //     ]);
    
    //     if ($stmt->fetch()) {
    //         return "Time slot is already booked.";
    //     }
    
    //     // Insert the appointment
    //     $query = "INSERT INTO appointments (researcher_id, consultant_id, appointment_date, start_time, end_time) 
    //               VALUES (:researcher_id, :consultant_id, :appointment_date, :start_time, :end_time)";
        
    //     $stmt = $this->pdo->prepare($query);
    //     $stmt->execute([
    //         ':researcher_id' => $researcher_id,
    //         ':consultant_id' => $consultant_id,
    //         ':appointment_date' => $appointment_date,
    //         ':start_time' => $start_time,
    //         ':end_time' => $end_time
    //     ]);
    
    //     return "Appointment booked successfully!";
    // }
    

}