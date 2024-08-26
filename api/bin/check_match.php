<?php
include "headers.php";

class MatchChecker
{
    private $filePath;

    public function __construct($gender)
    {
        // Determine the file path for the opposite gender
        $this->filePath = ($gender === 'Male') ? 'female.txt' : 'male.txt';
    }

    public function checkMatch($scannedId)
    {
        // Check if the file exists
        if (file_exists($this->filePath)) {
            // Get the current ID from the file
            $id = file_get_contents($this->filePath);
            if ((int)$scannedId === (int)$id) {
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

// Extract JSON data from POST request
$input = file_get_contents('php://input');
$decodedJson = json_decode($input, true);
$scannedId = isset($decodedJson['scannedId']) ? $decodedJson['scannedId'] : '';
$userGender = isset($decodedJson['userGender']) ? $decodedJson['userGender'] : '';

if (!empty($scannedId) && !empty($userGender)) {
    $matchChecker = new MatchChecker($userGender);
    echo $matchChecker->checkMatch($scannedId);
} else {
    echo json_encode(['status' => 0, 'message' => 'Invalid data provided']);
}
