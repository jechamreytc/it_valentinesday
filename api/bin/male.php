<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Method: GET, POST, OPTIONS");
header("Access-Control-Allow-Method: Content-Type, Authorization");
// save_data.php

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the data from the POST request
    $fullName = isset($_POST['full_name']) ? $_POST['full_name'] : '';
    $idNumber = isset($_POST['id_number']) ? $_POST['id_number'] : '';
    $gender = isset($_POST['gender']) ? $_POST['gender'] : '';

    // Validate received data
    if (!empty($fullName) && !empty($idNumber) && !empty($gender)) {
        // Specify the file where data will be saved
        $filePath = 'male.txt';

        // Initialize the ID
        $id = 1;

        // Check if the file already exists
        if (file_exists($filePath)) {
            // Read the file content
            $fileContent = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

            // Reverse the array to start checking from the end
            $fileContent = array_reverse($fileContent);

            // Find the last ID in the file
            foreach ($fileContent as $line) {
                if (strpos($line, 'ID: ') === 0) {
                    // Extract the numeric ID using regex
                    preg_match('/^ID: (\d+)/', $line, $matches);
                    if (!empty($matches[1])) {
                        $id = (int)$matches[1] + 1; // Increment ID
                        break;
                    }
                }
            }
        }

        // Prepare the content to save with ID
        $dataToSave = "ID: $id\nFull Name: $fullName\nID Number: $idNumber\nGender: $gender\n\n";

        // Save the data to the file
        file_put_contents($filePath, $dataToSave, FILE_APPEND | LOCK_EX);

        // Respond with a success message
        echo json_encode(['status' => 1, 'message' => 'Data saved successfully']);
    } else {
        // Respond with an error message if data is missing
        echo json_encode(['status' => 0, 'message' => 'Invalid data provided']);
    }
} else {
    // Respond with an error if the request method is not POST
    echo json_encode(['status' => 0, 'message' => 'Invalid request method']);
}
