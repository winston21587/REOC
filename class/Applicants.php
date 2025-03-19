<?php

include 'Database.php';

class Applicants extends Database {

    public function getAllApplicants($userid) {
        $query = "SELECT study_protocol_title,id
            FROM Researcher_title_informations 
            WHERE user_id = :user_id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':user_id', $userid);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getAppointedDate($id) {
    $query = 'SELECT * from appointments WHERE researcher_title_id = :id';
    $stmt = $this->pdo->prepare($query);
    $stmt->bindParam(':id', $id);
    if($stmt->execute()){
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    return false;
    
    
    }
}

