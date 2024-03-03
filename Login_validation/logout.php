<?php
session_start();

//Set Default Timezone for Logging of Errors
date_default_timezone_set('Asia/Manila');

// Set error logging
ini_set('log_errors', 1);
ini_set('error_log', '../../assets/error/error.log');

// Turn off error reporting to the screen
ini_set('display_errors', 0);

// Unset all of session variables
$_SESSION = array();

// Introduce a delay
sleep(3); // Delay for 3 seconds

//Destroy the session
session_destroy();

//Redirect to login page or homepage
header("location:login.php");
exit;
?>