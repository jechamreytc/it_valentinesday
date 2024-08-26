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
        $gender = $json['gender'] ?? '';

        if (!empty($gender)) {
            $id = 1;

            if (file_exists($this->filePath)) {
                $currentId = file_get_contents($this->filePath);
                $id = (int)$currentId + 1;
            }

            file_put_contents($this->filePath, $id, LOCK_EX);

            return json_encode(['status' => 1, 'message' => 'Data saved successfully', 'id' => $id]);
        } else {
            return json_encode(['status' => 0, 'message' => 'Invalid data provided']);
        }
    }

    public function getId()
    {
        if (file_exists($this->filePath)) {
            $id = file_get_contents($this->filePath);
            return json_encode(['status' => 1, 'gender' => ($this->filePath === 'male.txt' ? 'Male' : 'Female'), 'id' => (int)$id]);
        } else {
            return json_encode(['status' => 0, 'message' => 'No data found', 'gender' => ($this->filePath === 'male.txt' ? 'Male' : 'Female')]);
        }
    }

    public function checkMatch($userId, $scannedId)
    {
        if (file_exists($this->filePath)) {
            $fileId = file_get_contents($this->filePath);

            if ((int)$scannedId === (int)$fileId) {
                // Save match result
                file_put_contents('match_status.txt', json_encode(['match' => true]), LOCK_EX);
                return json_encode(['status' => 1, 'match' => true]);
            } else {
                file_put_contents('match_status.txt', json_encode(['match' => false]), LOCK_EX);
                return json_encode(['status' => 0, 'match' => false]);
            }
        } else {
            return json_encode(['status' => 0, 'message' => 'No data found']);
        }
    }
}

// Extract parameters from POST request
$json = isset($_POST['json']) ? $_POST['json'] : "0";
$operation = isset($_POST['operation']) ? $_POST['operation'] : "0";

$decodedJson = json_decode($json, true);

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
            $userId = $decodedJson['userId'] ?? '';
            $scannedId = $decodedJson['id'] ?? '';
            if (!empty($userId) && !empty($scannedId)) {
                echo $dataHandler->checkMatch($userId, $scannedId);
            } else {
                echo json_encode(['status' => 0, 'message' => 'Invalid IDs']);
            }
            break;
        default:
            echo json_encode(['status' => 0, 'message' => 'Invalid operation']);
            break;
    }
} else {
    echo json_encode(['status' => 0, 'message' => 'Invalid data or gender provided']);
}
