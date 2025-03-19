<?php
include "Database.php";

class Submit extends Database{

    public function ApplicationStatus($user_id){
        $query = "SELECT status FROM application_status WHERE id = :user_id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        if($stmt->execute()){
            return $stmt->fetch();
        }
        return false;
    }

    public function GetColleges(){
        $query = "SELECT college_name_and_color FROM colleges";
        $stmt = $this->pdo->prepare($query);
        if($stmt->execute()){
            return $stmt->fetchAll();
        }
        return false;
    }

    public function fetchUserEmail($user_id){
        $query = "SELECT email FROM users WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $user_id);
        if($stmt->execute()){
            return $stmt->fetch();
        }
        return false;
    }
    public function researchTitleInfo($user_id, $study_protocol_title, $college, $research_category, $adviser_name){

        $query = "INSERT INTO Researcher_title_informations(user_id, study_protocol_title, college, research_category, adviser_name)
         VALUES (:user_id, :study_protocol_title, :college, :research_category, :adviser_name)";
        $stmt = $this->pdo->prepare($query);

        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':study_protocol_title', $study_protocol_title);
        $stmt->bindParam(':college', $college);
        $stmt->bindParam(':research_category', $research_category);
        $stmt->bindParam(':adviser_name', $adviser_name);

        return $stmt->execute();
    }

    public function researchInvolved($researcher_title_id, $first_name, $last_name, $middle_initial){
        $query = 'INSERT INTO Researcher_involved (researcher_title_id, first_name, last_name, middle_initial ) VALUES
         (:researcher_title_id, :first_name, :last_name, :middle_initial )';
         $stmt = $this->pdo->prepare($query);

        $stmt->bindParam(':researcher_title_id', $researcher_title_id);
        $stmt->bindParam(':first_name', $first_name);
        $stmt->bindParam(':last_name', $last_name);
        $stmt->bindParam(':middle_initial', $middle_initial);

        return $stmt->execute();
    }

    public function getTitleID($user_id, $study_protocol_title){
        $query = 'SELECT id FROM researcher_title_informations WHERE user_id = :user_id AND study_protocol_title = :study_protocol_title';
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':study_protocol_title', $study_protocol_title);
        if($stmt->execute()){
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ? $row['id'] : false;
        }
        return false;
    }

    public function UploadFile($researcher_title_id, $file_type, $file_name, $file_path){
        $query = 'INSERT INTO researcher_files (researcher_title_id, file_type, filename, file_path)
         VALUES (:researcher_title_id, :file_type, :file_name, :file_path)';
         $stmt = $this->pdo->prepare($query);
         $stmt->bindParam(':researcher_title_id', $researcher_title_id);
         $stmt->bindParam(':file_type', $file_type);
         $stmt->bindParam(':file_name', $file_name);
         $stmt->bindParam(':file_path', $file_path);
         return $stmt->execute();
    }

    public function moveUploadFiles($researcher_title_id, $unique_other_file_name, $file_path){
        $query = 'INSERT INTO researcher_files (researcher_title_id, file_type, filename, file_path)
         VALUES (:researcher_title_id, :file_type, :filename, :file_path)';
         $fname = 'Other';
         $stmt = $this->pdo->prepare($query);
         $stmt->bindParam(':researcher_title_id', $researcher_title_id);
         $stmt->bindParam(':file_type', $fname);
         $stmt->bindParam(':filename',$unique_other_file_name );
         $stmt->bindParam(':file_path', $file_path);
         
         return $stmt->execute();

    }

    public function getAvailableConsultation(){
        // $query = "SELECT 
        // id, 
        // consultant_id, 
        // weekday, 
        // start_time, 
        // end_time,
        // status,
        // DATE_ADD(CURDATE(), INTERVAL ((FIND_IN_SET(weekday, `Monday,Tuesday,Wednesday,Thursday,Friday`) 
        //     - WEEKDAY(CURDATE()) + 7) % 7) DAY) AS next_appointment_date
        //     FROM consultant_availability
        //     WHERE status = `open`
        //     ORDER BY next_appointment_date ASC
        //     LIMIT 1";

        $query = "SELECT 
        id, 
        consultant_id, 
        weekday, 
        start_time, 
        end_time,
        DATE_ADD(CURDATE(), INTERVAL ((FIND_IN_SET(weekday, 'Monday,Tuesday,Wednesday,Thursday,Friday') 
            - WEEKDAY(CURDATE()) + 7) % 7) DAY) AS next_appointment_date
        FROM consultant_availability
        WHERE status = 'open'
        ORDER BY next_appointment_date ASC, start_time ASC
        LIMIT 1";
        $stmt = $this->pdo->prepare($query);
        if($stmt->execute()){
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return false;
    }

    public function setconsultSchedStatus($consult_id){
        $query = "UPDATE consultant_availability SET status = 'booked' WHERE id = :consult_id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(":consult_id", $consult_id);

        return $stmt->execute();
    }

    public function setAppointment($researcher_title_id,$availability_id,$appointment_date){
        $query = "INSERT INTO appointments (researcher_title_id, availability_id, appointment_date)
         VALUES (:researcher_title_id, :availability_id, :appointment_date)";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':researcher_title_id', $researcher_title_id);
        $stmt->bindParam(':availability_id', $availability_id);
        $stmt->bindParam(':appointment_date', $appointment_date);
        return $stmt->execute();
    }
}