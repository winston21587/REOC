<?php
include "Database.php";
class Login extends Database{

    function fetchUser($email){
        $query = "SELECT id, password, isActive FROM users WHERE email = :email";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':email', $email);
        if($stmt->execute()){
            if($stmt->rowCount() > 0){
                $user = $stmt->fetch();
                return $user;
            }else{
                return false;
            }
        }
    }

    function FindRole($user_id){
        $query = "SELECT roles.name FROM user_roles 
                    JOIN roles ON user_roles.role_id = roles.id 
                    WHERE user_roles.user_id = :userId";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':userId', $user_id);
        if($stmt->execute()){
            if($stmt->rowCount() > 0){
                $role = $stmt->fetch()['name'];
                return $role;
            }else{
                return false;
            }
        }
    }
}