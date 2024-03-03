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

if (isset($_POST['loginbtn'])) {
    $login_user = validate($_POST['login-user']);
    $login_pass = validate($_POST['login-pass']);

    #This code prepares the SQL query first, then binds the $login_user parameter to it, and finally executes the query. This way, you're no longer placing user input directly into your SQL query, making it safer against SQL injection attacks. 

    $stmt = $con->prepare("SELECT * FROM admin WHERE username = ?");
    $stmt->bind_param("s", $login_user);

    if ($stmt->execute()) { 
        $login_result = $stmt->get_result();
        $login_row = $login_result->fetch_assoc();

        if ($login_row) {
            $storedHashedPassword = $login_row['password'];

            // Verify the entered password against the stored hashed password
            if (password_verify($login_pass, $storedHashedPassword)) {
                // Introduce a delay
                sleep(3); // Delay for 3 seconds
                
                $_SESSION["username"] = $login_user;
                header("location:../Content/dashboard/dashboard.php");
                exit;
            } else {
                # Modify the redirections to use '$_SESSIONS'
                $_SESSION['error'] = "Invalid Username or Password";
                header("location: login.php");
                exit;
            }
        } else {
            # Modify the redirections to use '$_SESSIONS'
            $_SESSION['error'] = "Invalid Username or Password";
            header("location: login.php");
            exit;
        }
    } else {
        /*echo "Error in database query: " . mysqli_error($con);*/
        echo "Error in database query" . $stmt->error;
    }

    $stmt->close();  // Close the statement

}

$con->close(); // Close the database connection (you might want to place this at the end of your script)

?>
