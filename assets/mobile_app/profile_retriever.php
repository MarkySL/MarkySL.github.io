<?php
session_start();

//Set Default Timezone for Logging of Errors
date_default_timezone_set('Asia/Manila');

// Set error logging
ini_set('log_errors', 1);
ini_set('error_log', '../../assets/error/error.log');
    
// Turn off error reporting to the screen
ini_set('display_errors', 0);
    
include '../../Login_validation/connection.php';
    
// Prepare the SQL statement
$stmt = $con->prepare("SELECT id, staff_name, position, availability, staff_image FROM vet_profiles");

// Execute the query
$stmt->execute();

// Bind the results to variables
$stmt->bind_result($id, $staff_name, $position, $availability, $staff_image);

$data = [];

// Fetch the results into the array
while ($stmt->fetch()) {
    $data[] = [
        'id' => $id, // Include the primary ID
        'staff_name' => $staff_name,
        'position' => $position,
        // Convert availability to boolean for JSON
        'availability' => (bool)$availability,
        'staff_image' => $staff_image
    ];
}

// Set header to return JSON
header('Content-Type: application/json');

// Echo data as JSON
echo json_encode($data);

// Close statement
$stmt->close();

// Close connection
$con->close();
?>
    

