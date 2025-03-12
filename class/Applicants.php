<?php

include 'Database.php';

class Applicants extends Database {

    public function getAllApplicants($userid) {
        $query = "SELECT rt.study_protocol_title 
            FROM Researcher_title_informations rt
            WHERE rt.user_id = :user_id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':user_id', $userid);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}

