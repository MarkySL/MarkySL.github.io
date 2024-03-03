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
        $_SESSION['message'] = "New password and confirm password do not match.";
        $_SESSION['message_type'] = "error";
        header("location:update_acct.php");
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
                $_SESSION['message'] = "Old password does not match the existing password";
                $_SESSION['message_type'] = "error";
                header("location:update_acct.php");
                exit();
            }
        }
    } else {
        $_SESSION['message'] = "No user found with the old username";
        $_SESSION['message_type'] = "error";
        header("location:update_acct.php");
        exit();
    }
 
    // Hash the new password
    $hashed_password = password_hash($new_pass, PASSWORD_DEFAULT);

    // If new username is empty, keep the old username
    if(empty($new_user)){
        $new_user = $old_user;
    }
 
    // Prepare SQL statement
    $stmt = $con->prepare("UPDATE admin SET username=?, password=? WHERE username=?");
    $stmt->bind_param("sss", $new_user, $hashed_password, $old_user);
 
    // Execute the statement
    if ($stmt->execute()) {
        $_SESSION['message'] = "Record updated successfully";
        $_SESSION['message_type'] = "success";
        header("location:update_acct.php");
        exit();
    } else {
        $_SESSION['message'] = "Error updating record " . $stmt->error;
        $_SESSION['message_type'] = "error";
        header("location:update_acct.php");
        exit();
    }
 
    $stmt->close();
}
 
$con->close();

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!--CSS Code-->
    <link rel="stylesheet" href="../../assets/css/update_user.css">
    <link rel="shortcut icon" href="../../assets/imgs/PetAlliesFavicon.png" type="image/x-icon">

    <title>Update Account</title>

    <!--Fontawesome Scripts Icons-->
    <script src="https://kit.fontawesome.com/acd6544335.js" crossorigin="anonymous"></script>

    <!--AJAX JQuery Script-->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

</head>
<body>
     <!--Global-->
     <div class="container">
        <!--Navigation Bar-->
        <div class="nav_bar">
            <ul>
                <li>
                    <a href="../dashboard/dashboard.php">
                        <span class="icon"><img src="../../assets/imgs/PetAlliesFavicon.png" alt="No Image"></span>
                        <span class="main_title">Pet Allies <br> Animal Clinic</span>
                    </a>
                </li>
                <li>
                    <a href="../dashboard/dashboard.php">
                        <span class="icon"><i class="fa-solid fa-house all_icon"></i></span>
                        <span class="title">Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="../monitoring/monitoring.php">
                        <span class="icon"><i class="fa-solid fa-heart all_icon"></i></span>
                        <span class="title">Monitoring</span>
                    </a>
                </li>
                <li>
                    <a href="../follow-ups/follow-ups.php">
                        <span class="icon"><i class="fa-solid fa-file-medical all_icon"></i></span>
                        <span class="title">Follow-ups</span>
                    </a>
                </li>
                <li>
                    <a href="../clinic-visits/clinic-visits.php">
                        <span class="icon"><i class="fa-solid fa-house-medical-circle-check all_icon"></i></span>
                        <span class="title">Clinic Visits</span>
                    </a>
                </li>
                <li>
                    <a href="../inventory/inventory.php">
                        <span class="icon"><i class="fa-solid fa-warehouse all_icon"></i></span>
                        <span class="title">Products</span>
                    </a>
                </li>
                <li>
                    <a href="../profile/vetstaff.php">
                        <span class="icon"><i class="fa-solid fa-user all_icon"></i></span>
                        <span class="title">Profiles</span>
                    </a>
                </li>
                <li>
                    <a href="update_acct.php">
                        <span class="icon"><i class="fa-solid fa-gear all_icon"></i></span>
                        <span class="title">Settings</span>
                    </a>
                </li>
                <li>
                    <a href="../../Login_validation/logout.php">
                        <span class="icon"><i class="fa-solid fa-right-from-bracket all_icon"></i></span>
                        <span class="title">Logout</span>
                    </a>
                </li>
            </ul>
        </div>
        <!-- ====================== Main =================== -->
        <div class="main_bar">
            <div class="topbar">
                <div class="toggle">
                    <i class="fa-solid fa-bars"></i>
                </div>
                <div class="user">
                    <!--Edit this to have a function of upload image-->
                    <img src="../../assets/imgs/PetAlliesLogowithBG.png" alt="No Image">
                </div>
            </div>
        </div>

        <!-- ================== Update Admin Account ==================== -->
        <div class="update_admin">
            <div class="admin_form">
                <form action="#" method="POST">
                    <div class="admin">
                        <h2>Update Admin</h2>
                        <?php
                            if (isset($_SESSION['message'])) {
                                $class = ($_SESSION['message_type'] == "success") ? "message-success" : "message-error";
                                echo '<p class="' . $class . '">' . htmlspecialchars($_SESSION['message'], ENT_QUOTES, 'UTF-8') . '</p>';
                                unset($_SESSION['message'], $_SESSION['message_type']);  // Unset the message and type after displaying it
                            }
                        ?>
                        <div class="setting">
                            <label>Current username:</label>
                            <input type="text" name="old_user" value="<?php echo htmlspecialchars ($_SESSION['username']);?>" readonly>
                        </div>
                        <div class="setting">
                            <label>New username (Optional):</label>
                            <input type="text" name="new_user">
                        </div>
                        <div class="setting">
                            <label>Current Password:</label>
                            <input type="password" name="old_pass" required>
                        </div>
                        <div class="setting">
                            <label>New Password:</label>
                            <input type="password" name="new_pass" required>
                        </div>
                        <div class="setting">
                            <label>Confirm Password:</label>
                            <input type="password" name="confirm_pass" required>
                        </div>
                        <div class="setting">
                            <button type="submit" name="updateBtn">Update</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- =============SCRIPTS============= -->
    <script src="../../assets/js/main.js"></script>
</body>
</html>