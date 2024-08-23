<?php
// readData.php

// Specify the file path
$file = 'data.txt';

// Check if the file exists
if (file_exists($file)) {
    // Read the file content
    $content = file_get_contents($file);

    // Send the content as a JSON response
    echo json_encode(['status' => 'success', 'data' => $content]);
} else {
    // Send an error response if the file doesn't exist
    echo json_encode(['status' => 'error', 'message' => 'File not found']);
}
?>
