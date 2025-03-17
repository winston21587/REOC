<?php
// include "Database.php";
class Appointment extends Database{

    function getAvailableConsultation(){
        $query = "SELECT 
    id, 
    consultant_id, 
    weekday, 
    start_time, 
    end_time,
    status,
    DATE_ADD(CURDATE(), INTERVAL (
        (CASE 
            WHEN weekday = Monday THEN 0
            WHEN weekday = Tuesday THEN 1
            WHEN weekday = Wednesday THEN 2
            WHEN weekday = Thursday THEN 3
            WHEN weekday = Friday THEN 4
            END - WEEKDAY(CURDATE()) + 7) % 7
            ) DAY) AS next_appointment_date
            FROM consultant_availability
            WHERE status = open
            ORDER BY next_appointment_date ASC
            LIMIT 1;
            ";
        $stmt = $this->pdo->prepare($query);
        if($stmt->execute()){
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return false;
    }





}