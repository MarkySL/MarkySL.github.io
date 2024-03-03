<?php
session_start();
//Set Default Timezone for Logging of Errors
date_default_timezone_set('Asia/Manila');

// Set error logging
ini_set('log_errors', 1);
ini_set('error_log', '../../assets/error/error.log');

// Turn off error reporting to the screen
ini_set('display_errors', 0);

include 'connection.php';
include 'validation.php';

//Limit the users that can register 
$registrationLimit = 3;

//Check the number of registered users
$result = $con->query("SELECT COUNT(*) AS userCount FROM admin");
$row = $result->fetch_assoc();
$currentRegistrations = $row['userCount'];

//Running the condition
if ($currentRegistrations >= $registrationLimit) {
    $_SESSION['msg'] = "Sorry, Registration Limit reached!";
    header("location: register.php");
    exit;
}

//Registering input to the Database
if (isset($_POST['registerbtn'])) {
    $username = validate($_POST['username']);
    $email = validate($_POST['email']);
    $password = validate($_POST['password']); 

    /* 
    *================ For Future Reference =================*

    // Check if username exists
    $checkUser = $con->prepare("SELECT username FROM admin WHERE username=?");
    $checkUser->bind_param("s", $username);
    $checkUser->execute();
    $checkUser->store_result();

    if ($checkUser->num_rows > 0) {
        $_SESSION['msg'] = "Username is already in use!";
        header("location: register.php");
        exit;
    }

    $checkUser->close();

    // Check if email exists
    $checkEmail = $con->prepare("SELECT email FROM admin WHERE email=?");
    $checkEmail->bind_param("s", $email);
    $checkEmail->execute();
    $checkEmail->store_result();

    if ($checkEmail->num_rows > 0) {
        $_SESSION['msg'] = "Email is already registered!";
        header("location: register.php");
        exit;
    }

    $checkEmail->close();
    */

    /* Checks all the input field before submitting to database */
    if (empty($username) || empty($email) || empty($password)) {
        $_SESSION['msg'] = "All fields are required!";
        header("location: register.php");
        exit;
    }
    // Validates the email if correct format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['msg'] = "Invalid email format!";
        header("location: register.php");
        exit;
    }
    

    // Hash the password using the PASSWORD_DEFAULT algorithm
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    # Prevent SQL Injection
    $stmt = $con->prepare("INSERT INTO admin (username, email, password) VALUES (?, ?, ?)");

    $stmt->bind_param("sss", $username, $email, $hashedPassword);

    if ($stmt->execute()) {
        # Modify the redirections to use '$_SESSIONS'
        $_SESSION['msg'] = "Registration Successfully!";
        header("location: register.php");
        exit;
    } else {
        # Modify the redirections to use '$_SESSIONS'
        $_SESSION['msg'] = "Registration Failed!";
        header("location: register.php");
        exit;
    }

    $stmt->close();  // Close the statement
}

$con->close(); // Close the database connection (you might want to place this at the end of your script)

?>
