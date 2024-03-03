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

//The data inside the POST bracket should match the name in the form html
if (isset($_POST['submitBtn'])) {
    $client = validate($_POST['client']);
    $petname = validate($_POST['petname']);
    $weight = validate($_POST['weight']);
    $temp = validate($_POST['temperature']);
    $complaints = validate($_POST['complaints']);
    $treatment = validate($_POST['treatment']);
    $datevisit = new DateTime($_POST['datevisit']);
    $followupDays = intval($_POST['followupDays']);

    // Calculate the follow-up date
    $followupDate = clone $datevisit; // Create a copy of the $datevisit object
    $followupDate->modify('+' . $followupDays . ' days');
    $followupDateFormatted = $followupDate->format('Y-m-d');

   
    
    # Prevent SQL Injection
    $stmt = $con->prepare("INSERT INTO follow_ups (client, petname, weight, temperature, complaints, treatment, datevisit, followup) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

    if ($stmt === false) {
        // Prepare failed
        $_SESSION['error_message'] = "Database error: " . $con->error;
        header("Location: follow-ups.php"); // Replace with your error page
        exit;
    }

    $stmt->bind_param("ssssssss", $client, $petname, $weight, $temp, $complaints, $treatment, $datevisit->format('Y-m-d'), $followupDateFormatted);

    if ($stmt->execute()) {
        
        header("Location: follow-ups.php");
        exit();
    } else {  
        // Add an error message to the session
        $_SESSION['error_message'] = "Error: Data could not be added";
        header("Location: follow-ups.php");
        exit();
    }

    $stmt->close();

}

?>

