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

if (!isset($_GET['updateid'])) {
    die('Invalid Request');
}

$id = intval($_GET['updateid']); // Ensure it's an integer

// Fetching previous data using prepared statements
$stmt = $con->prepare("SELECT * FROM follow_ups WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();

    //Assigning the fetched data
    $client = $row['client'];
    $petname = $row['petname'];
    $weight = $row['weight'];
    $temperature = $row['temperature'];
    $complaints = $row['complaints'];
    $treatment = $row['treatment'];
    $datevisit = $row['datevisit'];
    $followupDays = $row['followup'];
}
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['updatefollowupBtn'])) {
    
    //Sanitize and validate inputs
    $client = validate($_POST['client']);
    $petname = validate($_POST['petname']);
    $weight = validate($_POST['weight']);
    $temperature = validate($_POST['temperature']);
    $complaints = validate($_POST['complaints']);
    $treatment = validate($_POST['treatment']);
    $datevisit = validate($_POST['datevisit'] ?? null); // use of null coalescing operator
    $followupDays = validate($_POST['followup'] ?? null); // use of null coalescing operator

    //Binding the input to the prepared statement to prevent SQL Injection
    $stmt = $con->prepare("UPDATE follow_ups SET client=?, petname=?, weight=?, temperature=?, complaints=?, treatment=?, datevisit=?, followup=? WHERE id=?");
    $stmt->bind_param("ssssssssi", $client, $petname, $weight, $temperature, $complaints, $treatment, $datevisit, $followupDays, $id);

    $recordUpdated = false;

    if($stmt->execute()) {
        $recordUpdated = true;
        header("Location:follow-ups.php");
    } else {
        echo "Error updating record: " . $stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!--CSS Code-->
    <link rel="stylesheet" href="../../assets/css/f_update.css">
    <link rel="shortcut icon" href="../../assets/imgs/PetAlliesFavicon.png" type="image/x-icon">

    <title>Update Records</title>

    <!--Fontawesome Scripts Icons-->
    <script src="https://kit.fontawesome.com/acd6544335.js" crossorigin="anonymous"></script>

    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>
<body>
    <?php 
    $recordUpdated = false; //Setting a default value for the variable will prevent the warning
    if($recordUpdated): ?>
        <script>
            Swal.fire({
                title: "Success!",
                text: "Records updated successfully.",
                icon: "success",
                timer: 2000,
                showConfirmButton: false
            }).then((result) => {
                if (result.dismiss === Swal.DismissReason.timer) {
                    window.location.href = "follow-ups.php";
                }
            });
        </script>
    <?php endif; ?>
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
                    <a href="follow-ups.php">
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
                    <a href="../settings/update_acct.php">
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

        <!-- ================== Update Followup Form ============ -->
        <div class="update_record">
            <div class="update">
                <!--Close (X) Button-->
                <div class="close-btn">
                    <button type="button" id="closeButton" style="background: none; border: none; font-size: 40px; cursor: pointer;">&times;</button>
                </div>
                <!-- ====== Displays Previous Form for Editing ========= -->
                <form action="" method="post">
                    <div class="form">
                        <h2>Update Record</h2>
                        <!--Take note to Surround php code embedded to value attributes with double quotes 
                        to avoid broken html if any of those values are containing spaces, special characters and so on-->
                        <div class="update_form">
                            <label>Owner:</label>
                            <input type="text" id="client" name="client" value="<?php echo htmlspecialchars($client ?? '');?>"> 
                        </div>
                        <div class="update_form">
                            <label>Petname:</label>
                            <input type="text" id="petname" name="petname" value="<?php echo htmlspecialchars($petname) ?? '';?>">
                        </div>
                        <div class="update_form">
                            <label>Weight:</label>
                            <input type="text" id="weight" name="weight" value="<?php echo htmlspecialchars($weight ?? '');?>">
                        </div>
                        <div class="update_form">
                            <label>Temperature:</label>
                            <input type="text" id="temp" name="temperature" value="<?php echo htmlspecialchars($temperature ?? '');?>">
                        </div>
                        <div class="update_form">
                            <label>Complaints/<br>Diagnosis</label>
                            <textarea rows="3" cols="26" name="complaints" id="complaints"><?php echo htmlspecialchars($complaints ?? '');?></textarea>
                        </div>
                        <div class="update_form">
                            <label>Treatments/<br>Test</label>
                            <textarea rows="3" cols="26" name="treatment" id="treatment"><?php echo htmlspecialchars($treatment ?? '');?></textarea>
                        </div>
                        <div class="update_form">
                            <label>Date Visit:</label>
                            <input type="date" id="datevisit" name="datevisit" value="<?php echo $datevisit;?>">
                        </div>
                        <div class="update_form">
                            <label>Follow Up Days:</label> 
                            <input type="date" id="followupDays" name="followup" value="<?php echo $followupDays;?>">
                        </div>
        
                        <!--Submission Button-->
                        <div class="update_form">
                            <button type="submit" name="updatefollowupBtn">Update Record</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="../../assets/js/main.js"></script>
    <script>
        document.getElementById('closeButton').addEventListener('click', function() {
            window.location.href = "follow-ups.php";
        });
    </script>

</body>
</html>
