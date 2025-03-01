<?php
require "dbConnCode.php";

$id = $_POST['id'];
$question = $_POST['question'];
$answer = $_POST['answer'];

if ($id) {
    // Update existing FAQ
    $stmt = $conn->prepare("UPDATE faq SET question=?, answer=? WHERE id=?");
    $stmt->bind_param("ssi", $question, $answer, $id);
} else {
    // Insert new FAQ
    $stmt = $conn->prepare("INSERT INTO faq (question, answer) VALUES (?, ?)");
    $stmt->bind_param("ss", $question, $answer);
}

if ($stmt->execute()) {
    echo "Success";  // JavaScript listens for this
} else {
    echo "Error: " . $conn->error;
}
?>
