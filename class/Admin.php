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

    public function fetchAppData(){
        $query = "
    SELECT rti.id, 
            rti.user_id, 
            rti.uploaded_at, 
            rti.study_protocol_title, 
            rti.research_category, 
            rti.college,
            rti.adviser_name, 
            rti.payment,    
            rti.status,            
            rti.type_of_review,         
            rti.Toggle, 
            a.appointment_date,
            rp.mobile_number,
            u.email
            FROM Researcher_title_informations AS rti
            LEFT JOIN appointments AS a ON rti.id = a.researcher_title_id  -- Change user_id to researcher_title_id
            LEFT JOIN researcher_profiles AS rp ON rti.user_id = rp.user_id
            LEFT JOIN users AS u ON rti.user_id = u.id
            ORDER BY rti.uploaded_at DESC
    ";

    $stmt = $this->pdo->prepare($query);
    if($stmt->execute()){
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    return false;

    }


    public function getTotalUsers(){
        $query = "SELECT COUNT(*) as total_users FROM users WHERE email != '' AND email IS NOT NULL";
        $stmt = $this->pdo->prepare($query);
        if($stmt->execute()){
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return false;
    }
    public function getTotalResearch(){
        $query = "SELECT COUNT(*) as total_research FROM Researcher_title_informations";
        $stmt = $this->pdo->prepare($query);
        if($stmt->execute()){
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return false;
    }
    public function getTotalTitleCompleted(){
        $query = "SELECT COUNT(*) as total_completed FROM Researcher_title_informations WHERE status = 'Complete Submission'";
        $stmt = $this->pdo->prepare($query);
        if($stmt->execute()){
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return false;
    }

    public function getResearchtitle($id){
        $query = "SELECT a.*,b.email FROM Researcher_title_informations a 
        LEFT JOIN users b ON a.user_id = b.id
        WHERE a.id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        if($stmt->execute()){
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return false;
    }

    public function setStatus($id, $status){
        $query = "UPDATE Researcher_title_informations SET status = :status WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}