<?php
require "dbConnCode.php";

$id = $_GET['id'];
$stmt = $conn->prepare("DELETE FROM faq WHERE id=?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo "Deleted";  
} else {
    echo "Error: " . $conn->error;
}
?>