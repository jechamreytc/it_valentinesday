<?php
include "headers.php";

class DataHandler
{
    private $filePath;

    public function __construct($gender)
    {
        // Determine the file path for the current gender
        $this->filePath = ($gender === 'Male') ? 'male.txt' : 'female.txt';
    }

    public function saveData($json)
    {
        $json = json_decode($json, true);
        $gender = isset($json['gender']) ? $json['gender'] : '';

        if (!empty($gender)) {
            // Initialize the ID
            $id = 1;

            // Check if the file already exists
            if (file_exists($this->filePath)) {
                // Read the current ID in the file
                $currentId = file_get_contents($this->filePath);
                $id = (int)$currentId + 1; // Increment ID
            }

            // Save the new ID, overwriting the file
            file_put_contents($this->filePath, $id, LOCK_EX);

            // Return success
            return json_encode(['status' => 1, 'message' => 'Data saved successfully', 'id' => $id]);
        } else {
            // Return error
            return json_encode(['status' => 0, 'message' => 'Invalid data provided']);
        }
    }

    public function getId()
    {
        // Check if the file exists
        if (file_exists($this->filePath)) {
            // Get the current ID from the file
            $id = file_get_contents($this->filePath);
            return json_encode(['status' => 1, 'gender' => ($this->filePath === 'male.txt' ? 'Male' : 'Female'), 'id' => (int)$id]);
        } else {
            // Return error if file does not exist
            return json_encode(['status' => 0, 'message' => 'No data found', 'gender' => ($this->filePath === 'male.txt' ? 'Male' : 'Female')]);
        }
    }

    public function checkMatch($id)
    {
        // Assume gender is handled elsewhere and file path is set correctly
        if (file_exists($this->filePath)) {
            // Get the current ID from the file
            $fileId = file_get_contents($this->filePath);
            if ((int)$id === (int)$fileId) {
                return json_encode(['status' => 1, 'match' => true]);
            } else {
                return json_encode(['status' => 0, 'match' => false]);
            }
        } else {
            // Return error if file does not exist
            return json_encode(['status' => 0, 'message' => 'No data found']);
        }
    }
}

// Extract parameters from POST request
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
        case 'getId':
            echo $dataHandler->getId();
            break;
        case 'checkMatch':
            $id = isset($decodedJson['id']) ? $decodedJson['id'] : '';
            if (!empty($id)) {
                echo $dataHandler->checkMatch($id);
            } else {
                echo json_encode(['status' => 0, 'message' => 'Invalid ID']);
            }
            break;
        default:
            echo json_encode(['status' => 0, 'message' => 'Invalid operation']);
            break;
    }
} else {
    echo json_encode(['status' => 0, 'message' => 'Invalid data or gender provided']);
}
