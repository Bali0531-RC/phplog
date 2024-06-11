<?php
// Get the JSON data from the request
$data = json_decode(file_get_contents('php://input'), true);

// Define the file to store the data
$data_file = 'user_data.json';

// Append user info to the file
file_put_contents($data_file, json_encode($data) . PHP_EOL, FILE_APPEND);

// Send a response back to the client
header('Content-Type: application/json');
echo json_encode(array('status' => 'success'));
?>
