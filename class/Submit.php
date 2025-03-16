<?php
include "Database.php";

class Submit extends Database{

    function ApplicationStatus($user_id){
        $query = "SELECT status FROM application_status WHERE id = :user_id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        if($stmt->execute()){
            return $stmt->fetch();
        }
        return false;
    }

    function GetColleges(){
        $query = "SELECT college_name_and_color FROM colleges";
        $stmt = $this->pdo->prepare($query);
        if($stmt->execute()){
            return $stmt->fetchAll();
        }
        return false;
    }

    function fetchUserEmail($user_id){
        $query = "SELECT email FROM users WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $user_id);
        if($stmt->execute()){
            return $stmt->fetch();
        }
        return false;
    }
    function researchTitleInfo($user_id, $study_protocol_title, $college, $research_category, $adviser_name){

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

    function researchInvolved($researcher_title_id, $first_name, $last_name, $middle_initial){
        $query = 'INSERT INTO Researcher_involved (researcher_title_id, first_name, last_name, middle_initial ) VALUES
         (:researcher_title_id, :first_name, :last_name, :middle_initial )';
         $stmt = $this->pdo->prepare($query);

        $stmt->bindParam(':researcher_title_id', $researcher_title_id);
        $stmt->bindParam(':first_name', $first_name);
        $stmt->bindParam(':last_name', $last_name);
        $stmt->bindParam(':middle_initial', $middle_initial);

        return $stmt->execute();
    }

    function getTitleID($user_id, $study_protocol_title){
        $query = 'SELECT id FROM researcher_title_informations WHERE user_id = :user_id AND study_protocol_title = :study_protocol_title';
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':study_protocol_title', $study_protocol_title);
        if($stmt->execute()){
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['id'];
        }
        return false;
    }

    function UploadFile($researcher_title_id, $file_type, $file_name, $file_path){
        $query = 'INSERT INTO researcher_files (researcher_title_id, file_type, filename, file_path)
         VALUES (:researcher_title_id, :file_type, :file_name, :file_path)';
         $stmt = $this->pdo->prepare($query);
         $stmt->bindParam(':researcher_title_id', $researcher_title_id);
         $stmt->bindParam(':file_type', $file_type);
         $stmt->bindParam(':file_name', $file_name);
         $stmt->bindParam(':file_path', $file_path);
         return $stmt->execute();
    }

    function moveUploadFiles($researcher_title_id, $unique_other_file_name, $file_path){
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
}