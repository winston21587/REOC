<?php
require "dbConnCode.php"; 

$result = $conn->query("SELECT * FROM faq ORDER BY created_at DESC");
$faqs = [];

while ($row = $result->fetch_assoc()) {
    $faqs[] = $row;
}

echo json_encode($faqs);
?>
