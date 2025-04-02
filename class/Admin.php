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

public function fetchFAQ(){
    $query = "SELECT * FROM faq";
    $stmt = $this->pdo->prepare($query);
    if( $stmt->execute()){
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    return false;
}

public function addFAQ($question,$answer){
    $query = "INSERT INTO faq (question, answer) VALUES (:question, :answer)";
    $stmt = $this->pdo->prepare($query);
    $stmt->bindParam(":question", $question);
    $stmt->bindParam(":answer", $answer);
    return $stmt->execute();
}

public function updateFAQ($faq_id, $question, $answer) {
    $query = "UPDATE faq SET question = :question, answer = :answer WHERE id = :faq_id";
    $stmt = $this->pdo->prepare($query);
    $stmt->bindParam(":question", $question);
    $stmt->bindParam(":answer", $answer);
    $stmt->bindParam(":faq_id", $faq_id);
    return $stmt->execute();
    }

public function deleteFAQ($faq_id){
    $query = "DELETE FROM faq WHERE id = :faq_id";
    $stmt = $this->pdo->prepare($query);
    $stmt->bindParam(":faq_id", $faq_id);
    return $stmt->execute();
}

public function showVM(){
    $query = "SELECT * FROM vision_mission";
    $stmt = $this->pdo->prepare($query);
    if($stmt->execute())
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    return false;

}

public function updateVM($content, $id) {
    $query = "UPDATE vision_mission SET content = :content WHERE id = :id";
    $stmt = $this->pdo->prepare($query);
    $stmt->bindParam(":content", $content);
    $stmt->bindParam(":id", $id);
    return $stmt->execute();
}

public function getUserInfo(){
    
    $query = "    SELECT u.id, u.email, u.isActive, rp.mobile_number
    FROM users AS u
    LEFT JOIN researcher_profiles AS rp ON u.id = rp.user_id
    LEFT JOIN user_roles AS ur ON u.id = ur.user_id
    LEFT JOIN roles AS r ON ur.role_id = r.id
    WHERE u.email != '' AND u.email IS NOT NULL
    AND r.name = 'Researcher';";
    $stmt = $this->pdo->prepare($query);
    if($stmt->execute()){
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    return false;
}
}