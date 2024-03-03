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

if(isset($_POST['updateBtn'])){
    $old_user = validate($_POST['old_user']);
    $new_user = validate($_POST['new_user']);
    $old_pass = validate($_POST['old_pass']);
    $new_pass = validate($_POST['new_pass']);
    $confirm_pass = validate($_POST['confirm_pass']);

    // Check if new password and confirm password are the same
    if($new_pass != $confirm_pass){
        echo "New password and confirm password do not match.";
        exit();
    }

    // Prepare SQL statement
    $stmt = $con->prepare("SELECT * FROM admin WHERE username=?");
    $stmt->bind_param("s", $old_user);
 
    // Execute the statement
    $stmt->execute();
 
    // Get the result
    $result = $stmt->get_result();
 
    if ($result->num_rows > 0) {
        // Output data of each row
        while($row = $result->fetch_assoc()) {
            // Verify the password
            if(!password_verify($old_pass, $row["password"])){
                echo "Old password does not match the existing password.";
                exit();
            }
        }
    } else {
        echo "No user found with the old username.";
        exit();
    }
 
    // Hash the new password
    $hashed_password = password_hash($new_pass, PASSWORD_DEFAULT);
 
    // Prepare SQL statement
    $stmt = $con->prepare("UPDATE admin SET username=?, password=? WHERE username=?");
    $stmt->bind_param("sss", $new_user, $hashed_password, $old_user);
 
    // Execute the statement
    if ($stmt->execute()) {
        echo "Record updated successfully";
    } else {
        echo "Error updating record: " . $stmt->error;
    }
 
    $stmt->close();
}
 
$con->close();
?>