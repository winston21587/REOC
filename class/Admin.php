<?php


include 'Database.php';

class admin extends Database {

public function fetchPic($id){

    $query = "SELECT picture FROM faculty_members WHERE id = :id";
    $stmt = $this->pdo->prepare($query);

    $stmt->bindParam(':id', $id);

    if( $stmt->execute()){
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    return false;
    
}

public function fetchCurrentPic($id){
    $query = 'SELECT picture FROM Schedule WHERE id = :id';
    $stmt = $this->pdo->prepare($query);
    $stmt->bindParam(':id', $id);
    if( $stmt->execute()){
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    return false;
}

public function FetchavailableMonth(){
    $query = " SELECT DISTINCT DATE_FORMAT(uploaded_at, '%Y-%m-01') AS month FROM Researcher_title_informations
    UNION 
    SELECT DISTINCT DATE_FORMAT(uploaded_at, '%Y-%m-01') AS month FROM ResearcherTitleInfo_NoUser
    ORDER BY month DESC ";
    $stmt = $this->pdo->prepare($query);
    if( $stmt->execute()){
        return $stmt->fetchAll();
    }
    return false;

}




}