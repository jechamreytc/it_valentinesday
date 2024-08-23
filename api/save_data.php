<?php
include "headers.php";

class DataHandler
{
    private $filePath;

    public function __construct($gender)
    {
        $this->filePath = ($gender === 'Male') ? 'male.txt' : 'female.txt';
    }

    public function saveData($json)
    {
        $json = json_decode($json, true);
        $fullName = isset($json['full_name']) ? $json['full_name'] : '';
        $idNumber = isset($json['id_number']) ? $json['id_number'] : '';
        $gender = isset($json['gender']) ? $json['gender'] : '';

        if (!empty($fullName) && !empty($idNumber) && !empty($gender)) {
            // Initialize the ID
            $id = 1;

            // Check if the file already exists
            if (file_exists($this->filePath)) {
                // Read the file content
                $fileContent = file($this->filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

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
            file_put_contents($this->filePath, $dataToSave, FILE_APPEND | LOCK_EX);
            file_put_contents('debug_log.txt', "Saving Data: " . print_r($dataToSave, true) . "\n", FILE_APPEND);

            // Return success
            return json_encode(['status' => 1, 'message' => 'Data saved successfully']);
        } else {
            // Return error
            return json_encode(['status' => 0, 'message' => 'Invalid data provided']);
        }
    }
}

// Extract the operation and json data from POST
$json = isset($_POST['json']) ? $_POST['json'] : '';
$operation = isset($_POST['operation']) ? $_POST['operation'] : '';

$decodedJson = json_decode($json, true);

// Validate the JSON and gender key
if (is_array($decodedJson) && isset($decodedJson['gender']) && is_string($decodedJson['gender'])) {
    $dataHandler = new DataHandler($decodedJson['gender']);

    switch ($operation) {
        case 'saveData':
            echo $dataHandler->saveData($json);
            break;
        default:
            echo json_encode(['status' => 0, 'message' => 'Invalid operation']);
            break;
    }
} else {
    echo json_encode(['status' => 0, 'message' => 'Invalid data or gender provided']);
}
