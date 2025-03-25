<?php

function clean($input) {
    // Trim the input to remove extra spaces
    $input = trim($input);
    
    // Remove slashes added by magic quotes
    $input = stripslashes($input);
    
    
    // Convert special characters to HTML entities
    $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    
    // Strip HTML and PHP tags from the input
    $input = strip_tags($input);
    
    return $input;
}

function time_format($time) {
    $dateTime = DateTime::createFromFormat('H:i:s', $time);
    $time = $dateTime->format('h:i A');
    return $time;
}   