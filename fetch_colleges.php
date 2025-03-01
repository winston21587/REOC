<?php
include "dbConnCode.php";  // Ensure you have a database connection file

$sql = "SELECT college_name_and_color FROM colleges";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<option value='".$row['college_name_and_color']."'>".$row['college_name_and_color']."</option>";
    }
}
$conn->close();
?>
