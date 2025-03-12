<?php
// Database configuration
// $servername = "localhost";
// $username = "admin";
// $password = "admin";
// $database = "ReocWebDB";

// Create connection
// $conn = new mysqli($servername, $username, $password, $database);

// Check connection
// if ($conn->connect_error) {
//     die("Connection failed: " . $conn->connect_error);
// }


class Database {
    private $servername = "localhost";
    private $username = "admin";
    private $password = "admin";
    private $database = "ReocWebDB";

    protected $pdo = null;

    public function __construct() {
        if (!isset($this->pdo)) {
            try {
                $this->pdo = new PDO('mysql:host='.$this->servername.';dbname='.$this->database,
                $this->username,$this->password); 
            } catch (PDOException $e) {
                die("Failed to connect with MySQL: " . $e->getMessage());
            }
        }
    }


}
// Test if working (working)
// class user extends Database {

//     public function getAllUsers() {
//         $query = "SELECT * FROM users";
//         $stmt = $this->pdo->prepare($query);
//         $stmt->execute();
//         return $stmt->fetchAll();
//     }
// }


// $UserList = new user();
// $users = $UserList->getAllUsers();

// foreach ($users as $user) {
//     echo "<h1>";
//     echo $user['email'] . "<br>";
//     echo "</h1>";
// }


?>

