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
    $phone = validate($_POST['phone']);
    $address = validate($_POST['address']);
    $petname = validate($_POST['petname']);
    $p_bday = validate($_POST['petbday']);
    $p_gender = validate($_POST['petgender']);
    $species = validate($_POST['species']);
    $breed = validate($_POST['breed']);
    $colormarkings = validate($_POST['colormarkings']);

    /* Checks all the input field before submitting to database */
    if (empty($client) || empty($phone) || empty($petname) || empty($species) || empty($breed) || empty($colormarkings)) {
        $_SESSION['error_message'] = "All fields are required!";
        header("Location: monitoring.php");
        exit;
    }
    
    # Prevent SQL Injection
    $stmt = $con->prepare("INSERT INTO monitoring (client, phone, address, petname, petbday, gender, species, breed, colormarkings) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

    if ($stmt === false) {
        // Prepare failed
        $_SESSION['error_message'] = "Database error: " . $con->error;
        header("Location: monitoring.php"); // Replace with your error page
        exit;
    }

    $stmt->bind_param("sssssssss", $client, $phone, $address, $petname, $p_bday, $p_gender, $species, $breed, $colormarkings);

    if ($stmt->execute()) {
        /* 
        Add a success message to the session
        $_SESSION['success_message'] = "Data Added Successfully";
        */
        header("Location: monitoring.php");
        exit();
    } else {  
        // Add an error message to the session
        $_SESSION['error_message'] = "Error: Data could not be added";
        header("Location: monitoring.php");
        exit();
    }

    $stmt->close();

}

?>

