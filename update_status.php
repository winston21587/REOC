<?php
include 'dbConnCode.php'; // Adjust this to your database connection file

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["id"], $_POST["status"])) {
    $id = $_POST["id"];
    $status = $_POST["status"];

    // Use prepared statements to prevent SQL injection
    $stmt = $conn->prepare("UPDATE Researcher_title_informations SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $id);

    if ($stmt->execute()) {
        echo "success"; // This is what triggers the SweetAlert success
    } else {
        echo "error"; // This triggers the SweetAlert error
    }

    $stmt->close();
    $conn->close();
} else {
    echo "error"; // If invalid request
}
?>
