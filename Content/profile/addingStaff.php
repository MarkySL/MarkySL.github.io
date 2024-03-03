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
include '../../Login_validation/validation.php';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $staffName = validate($_POST['staffName']) ?? '';
    $position = validate($_POST['staffPosition']) ?? '';
    $availability = 1; // Set default availability, you might want to modify this

    // Prepare and bind
    $stmt = $con->prepare("INSERT INTO vet_profiles (staff_name, position, availability) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $staffName, $position, $availability);

    // Execute the statement
    if ($stmt->execute()) {
        $response = ['success' => true, 'message' => "New staff added successfully"];
    } else {
        $response = ['success' => false, 'message' => "Error: " . $stmt->error];
    }

    // Close statement and connection
    $stmt->close();
    $con->close();

    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}

?>