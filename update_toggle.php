<?php
require 'dbConnCode.php'; // Ensure this path is correct

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["id"], $_POST["toggle"])) {
    $id = intval($_POST["id"]);
    $toggle = intval($_POST["toggle"]); // Convert to integer (1 or 0)

    // Debugging: Print received values
    error_log("Received ID: $id, Toggle: $toggle");

    // Prepare the update query
    $query = "UPDATE researcher_title_informations SET Toggle = ? WHERE id = ?";
    $stmt = $conn->prepare($query);

    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("ii", $toggle, $id);

    if ($stmt->execute()) {
        echo "success";
    } else {
        die("Error updating: " . $stmt->error);
    }

    $stmt->close();
    $conn->close();
} else {
    die("Invalid request.");
}
?>
