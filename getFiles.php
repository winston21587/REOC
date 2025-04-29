<?php
// filepath: c:\xampp\htdocs\REOC\getFiles.php
header('Content-Type: application/json'); // Set the content type to JSON

require_once 'dbConnCode.php'; // Include your database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id'])) {
        $id = intval($_POST['id']); // Sanitize the input

        // Query to fetch files related to the given ID
        $sql = "SELECT filename, file_path FROM researcher_files WHERE researcher_title_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        $files = [];
        while ($row = $result->fetch_assoc()) {
            $files[] = $row; // Add each file to the array
        }

        $stmt->close();

        // Send the files as a JSON response
        echo json_encode($files);
    } else {
        // Send an error response if 'id' is not provided
        echo json_encode(['error' => 'No ID provided.']);
    }
} else {
    // Send an error response if the request method is not POST
    echo json_encode(['error' => 'Invalid request method.']);
}
?>