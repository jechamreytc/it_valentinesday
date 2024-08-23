<?php
class DataHandler
{
    private $filePath; // Path to the data file

    public function __construct($gender = 'Male')
    {
        // Set the file path based on gender
        $this->filePath = ($gender === 'Male') ? 'male.txt' : 'female.txt';
    }

    public function getData($id = null)
    {
        if (!file_exists($this->filePath)) {
            return json_encode(['status' => 0, 'message' => 'File not found']);
        }

        // Read file content
        $fileContent = file($this->filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        if ($fileContent === false) {
            return json_encode(['status' => 0, 'message' => 'Failed to read file']);
        }

        // Initialize variables for parsing
        $data = [];
        $record = [];
        $isInRecord = false;

        // Parse the file content into an array of records
        foreach ($fileContent as $line) {
            $line = trim($line);  // Trim any extra spaces
            if (empty($line)) {
                if ($isInRecord) {
                    if (!empty($record)) {
                        $data[] = $record;
                        $record = [];
                    }
                    $isInRecord = false;
                }
                continue;
            }

            // Use `explode` to split key and value
            if (strpos($line, ': ') !== false) {
                list($key, $value) = explode(": ", $line, 2);
                $record[$key] = $value;
                $isInRecord = true;
            } else {
                // Log unexpected line format
                file_put_contents('debug_log.txt', "Unexpected line format: $line\n", FILE_APPEND);
            }
        }

        // Add the last record if the file doesn't end with a blank line
        if ($isInRecord && !empty($record)) {
            $data[] = $record;
        }

        // Filter data by ID if provided
        if ($id !== null) {
            $filteredData = array_filter($data, function ($record) use ($id) {
                return isset($record['ID']) && (int)$record['ID'] === (int)$id;
            });

            if (empty($filteredData)) {
                return json_encode(['status' => 0, 'message' => 'Record not found']);
            }

            return json_encode(['status' => 1, 'data' => array_values($filteredData)]);
        }

        return json_encode(['status' => 1, 'data' => $data]);
    }
}

// Example usage
$gender = isset($_POST['gender']) ? $_POST['gender'] : 'Male'; // Default to Male if not set
$id = isset($_POST['ID']) ? $_POST['ID'] : null;

$dataHandler = new DataHandler($gender);
echo $dataHandler->getData($id);
