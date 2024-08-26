<?php
include "headers.php"; // Include necessary files

class MatchStatusHandler
{
    private $maleFilePath = 'male.txt';
    private $femaleFilePath = 'female.txt';
    private $matchStatusFilePath = 'match_status.txt';

    // Retrieves the current match status
    public function getMatchStatus()
    {
        if (file_exists($this->matchStatusFilePath)) {
            $status = file_get_contents($this->matchStatusFilePath);
            return $status;
        } else {
            return json_encode(['status' => 0, 'message' => 'No match status found']);
        }
    }

    // Reads an ID from a specified file path
    private function getIdFromFile($filePath)
    {
        if (file_exists($filePath)) {
            $id = file_get_contents($filePath);
            return (int)$id; // Ensure the ID is returned as an integer
        }
        return null; // Return null if the file does not exist
    }

    // Checks if there is a match between male and female IDs
    public function checkMatch()
    {
        $maleId = $this->getIdFromFile($this->maleFilePath);
        $femaleId = $this->getIdFromFile($this->femaleFilePath);

        if ($maleId !== null && $femaleId !== null) {
            if ($maleId === $femaleId) {
                // Save match result
                file_put_contents($this->matchStatusFilePath, json_encode(['match' => true, 'id' => $maleId]), LOCK_EX);
                return json_encode(['status' => 1, 'match' => true, 'id' => $maleId]);
            } else {
                // Save non-match result
                file_put_contents($this->matchStatusFilePath, json_encode(['match' => false]), LOCK_EX);
                return json_encode(['status' => 0, 'match' => false]);
            }
        } else {
            return json_encode(['status' => 0, 'message' => 'IDs not found in one or both files']);
        }
    }
}

$operation = isset($_POST['operation']) ? $_POST['operation'] : "0";

if ($operation === 'getMatchStatus') {
    $matchStatusHandler = new MatchStatusHandler();
    echo $matchStatusHandler->checkMatch();
} else {
    echo json_encode(['status' => 0, 'message' => 'Invalid operation']);
}
