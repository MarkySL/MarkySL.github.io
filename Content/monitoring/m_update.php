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

if (!isset($_GET['updateid'])) {
    // Handle the error or redirect
    die('Invalid request');
}

$id = intval($_GET['updateid']); // Ensure it's an integer

// Fetching previous data using prepared statements
$stmt = $con->prepare("SELECT * FROM monitoring WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    
    // Assigning the fetched data
    $client = $row['client'];
    $phone = $row['phone'];
    $address = $row['address'];
    $petname = $row['petname'];
    $p_bday = $row['petbday'];
    $p_gender = $row['gender'];
    $species = $row['species'];
    $breed = $row['breed'];
    $colormarkings = $row['colormarkings'];
}
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['updateBtn'])) {
    // Binding the input to the prepared statement to prevent SQL injection
    $stmt = $con->prepare("UPDATE monitoring SET client=?, phone=?, address=?, petname=?, petbday=?, gender=?, species=?, breed=?, colormarkings=? WHERE id=?");
    $stmt->bind_param("sssssssssi", $_POST['client'], $_POST['phone'], $_POST['address'], $_POST['petname'], $_POST['petbday'], $_POST['gender'], $_POST['species'], $_POST['breed'], $_POST['colormarkings'], $id);
    
    if ($stmt->execute()) {
        header("Location:monitoring.php");
        exit;
    } else {
        die($stmt->error);
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
    <link rel="stylesheet" href="../../assets/css/monitoring_update.css">
    <link rel="shortcut icon" href="../../assets/imgs/PetAlliesFavicon.png" type="image/x-icon">

    <title>Update Records</title>

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
                    <a href="monitoring.php">
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

            
        <!-- ================== Update Form ============ -->
        <div class="update_record">
            <div class="update">
                <!--Close (X) Button-->
                <div class="close-btn">
                    <button type="button" id="closeButton" style="background: none; border: none; font-size: 40px; cursor: pointer;">&times;</button>
                </div>
                <!-- ====== Displays Previous Form for Editing ========= -->
                <form id="myForm" action="" method="post">
                    <div class="form">
                        <h2>Update Record</h2>
                        <!--Take note to Surround php code embedded to value attributes with double quotes 
                        to avoid broken html if any of those values are containing spaces, special characters and so on-->
                        <div class="update_form">
                            <label>Owner:</label>
                            <input type="text" id="client" name="client" value="<?php echo htmlspecialchars($client ?? '');?>">
                        </div>
                        <div class="update_form">
                            <label>Contact#:</label>
                            <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($phone ?? '');?>">
                        </div>
                        <div class="update_form">
                            <label>Address:</label>
                            <input type="text" id="address" name="address" value="<?php echo htmlspecialchars ($address ?? '');?>">
                        </div>
                        <div class="update_form">
                            <label>Pet Name:</label>
                            <input type="text" id="pet" name="petname" value="<?php echo htmlspecialchars ($petname ?? '');?>">
                        </div>
                        <div class="update_form">
                            <label>Pet Birthday:</label>
                            <input type="date" id="pet-birth" name="petbday" value="<?php echo $p_bday;?>">
                        </div>
                        <div class="update_form">
                            <label>Gender:</label>
                            <label>
                                <input type="radio" name="gender" value="Male" <?php echo ($p_gender == "Male") ? "checked" : ""; ?>> Male
                            </label>
                            <label>
                                <input type="radio" name="gender" value="Female" <?php echo ($p_gender == "Female") ? "checked" : ""; ?>> Female
                            </label>
                        </div>
                        <div class="update_form">
                            <label>Species:</label>
                            <input type="text" id="species" name="species" value="<?php echo htmlspecialchars ($species ?? '');?>">
                        </div>
                        <div class="update_form">
                            <label>Breed:</label>
                            <input type="text" id="breed" name="breed" value="<?php echo htmlspecialchars ($breed ?? '');?>">
                        </div>
                        <div class="update_form">
                            <label>Color Markings:</label>
                            <input type="text" id="colormarkings" name="colormarkings" value="<?php echo htmlspecialchars ($colormarkings ?? '');?>">
                        </div>
                        <div class="update_form">
                            <button type="submit" name="updateBtn">Update Record</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- =============SCRIPTS============= -->
    <script src="../../assets/js/main.js"></script>
    <script>
        document.getElementById('closeButton').addEventListener('click', function() {
            window.location.href = "monitoring.php";
        });
    </script>
</body>
</html>
