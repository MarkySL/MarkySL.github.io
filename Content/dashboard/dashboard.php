<?php
session_start();
//Set Default Timezone for Logging of Errors
date_default_timezone_set('Asia/Manila');

// Set error logging
ini_set('log_errors', 1);
ini_set('error_log', '../../assets/error/error.log');

// Turn off error reporting to the screen
ini_set('display_errors', 0);

// Database Connection
include '../../Login_validation/connection.php';

// Set the timeout duration (2 minutes = 120 seconds)
$timeout_duration = 120;

// Check if the last activity is set in the session
if (isset($_SESSION['last_activity'])) {
    // Check if the user has been inactive for more than the timeout duration
    if (time() - $_SESSION['last_activity'] > $timeout_duration) {
        // Destroy the session and redirect to login
        session_unset();
        session_destroy();
        header("Location: ../../Login_validation/login.php");
        exit;
    }
}

// Update or set the last activity time
$_SESSION['last_activity'] = time();

/*This will check if the username is set in the session, if it's not then it redirects to login.php*/
if (!isset($_SESSION["username"])) {
    header("location: ../../Login_validation/login.php");
    exit;
}

//Count Total Scanned QR in clinic visits
include '../clinic-visits/qr_config.php'; // Database connection

function getTotalVisitsForCurrentMonth($pdo) {
    $query = "SELECT COUNT(*) as count FROM qr_scans WHERE MONTH(scan_time) = MONTH(CURRENT_DATE) AND YEAR(scan_time) = YEAR(CURRENT_DATE)";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['count'];
}

//Counts Total input by id's in monitoring.php
$sql_count = "SELECT COUNT(id) as total_clients FROM monitoring";
$result_count = $con->query($sql_count);
$row_count = $result_count->fetch_assoc();
$total_clients = $row_count['total_clients'];

//Counts Total input by id's in inventory.php
$product_count = "SELECT COUNT(id) as total_products FROM inventory";
$result_product_count = $con->query($product_count);
$product_row_count = $result_product_count->fetch_assoc();
$total_products = $product_row_count['total_products'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!--CSS Code-->
    <link rel="stylesheet" href="../../assets/css/main-style.css">
    <link rel="shortcut icon" href="../../assets/imgs/PetAlliesFavicon.png" type="image/x-icon">
    <title>Admin Dashboard</title>

    <!--Fontawesome Scripts Icons-->
    <script src="https://kit.fontawesome.com/acd6544335.js" crossorigin="anonymous"></script>

</head>
<body>
    <!--Global-->
    <div class="container">
        <!--Navigation Bar-->
        <div class="nav_bar">
            <ul>
                <li>
                    <a href="dashboard.php">
                        <span class="icon"><img src="../../assets/imgs/PetAlliesFavicon.png" alt="No Image"></span>
                        <span class="main_title">Pet Allies <br> Animal Clinic</span>
                    </a>
                </li>
                <li>
                    <a href="dashboard.php">
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
                    <img src="../../assets/imgs/PetAlliesLogowithBG.png" alt="No Image">
                </div>
            </div>
        
            <!-- ============= CARDS ================ -->
            <div class="cardBox">
                <a href="../clinic-visits/clinic-visits.php" class="card">
                    <div>
                        <div class="numbers"><?php echo getTotalVisitsForCurrentMonth($pdo); ?></div>
                        <div class="cardName">Visits</div>
                    </div>
                    <div class="iconBx">
                        <i class="fa-solid fa-qrcode"></i>
                    </div>
                </a>

                <a href="../monitoring/monitoring.php" class="card">
                    <div>
                        <div class="numbers"><?php echo $total_clients; ?></div>
                        <div class="cardName">Clients Monitoring</div>
                    </div>
                    <div class="iconBx">
                        <i class="fa-solid fa-paw"></i>
                    </div>
                </a>

                <a href="../inventory/inventory.php" class="card">
                    <div>
                        <div class="numbers"><?php echo $total_products; ?></div>
                        <div class="cardName">Products</div>
                    </div>
                    <div class="iconBx">
                        <i class="fa-solid fa-warehouse"></i>
                    </div>
                </a>
            </div> 

            <!-- ================ Clients list ================ -->
            <div class="details">
                <div class="recentClient">
                    <div class="cardHeader">
                        <h2>Updated List of Clients</h2>
                        <a href="../monitoring/monitoring.php" class="btn">View All</a>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <td>Owner</td>
                                <td>Pet Name</td>
                                <td>Species</td>
                                <td>Breed</td>
                                <td>Birthday</td>
                            </tr>
                        </thead>

                        <tbody> 
                            <?php
                                //Declaring specific datas to be fetch from the database
                                $sql = "SELECT client, petname, species, breed, petbday FROM monitoring ORDER BY id DESC LIMIT 10";

                                //Running the query to fetch the specific datas
                                $result = $con->query($sql);

                                //Dynamically generate table rows by looping through the fetched data
                                if ($result && $result->num_rows > 0) {
                                    while($row = $result->fetch_assoc()) {
                                        echo "<tr>";
                                        echo "<td>" . $row['client'] . "</td>";
                                        echo "<td>" . $row['petname'] . "</td>";
                                        echo "<td>" . $row['species'] . "</td>";
                                        echo "<td>" . $row['breed'] . "</td>";
                                        echo "<td>" . $row['petbday'] . "</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='5'>No clients found</td></tr>";
                                }
                            ?>
                        </tbody>
                    </table>
                </div>
                
                <!--============= Available Staff =============== -->
                <div class="availableStaff">
                    <div class="cardHeader">
                        <h2>Available Staff</h2>
                        <a href="../profile/vetstaff.php" class="btn">View All</a>
                    </div>

                    <table>
                        <thead>
                            <tr>
                                <td>Staff Name</td>
                                <td>Position</td>
                            </tr>
                        </thead>
                        
                        <tbody>
                            <?php
                                // Modify the SQL to select only available staff and order them by availability
                                $sql = "SELECT staff_name, position, availability FROM vet_profiles WHERE availability = 1  ORDER BY staff_name ASC LIMIT 10";
                                            
                                // Execute the query
                                $result = $con->query($sql);
                                // Check if there are any available staff
                                if ($result && $result->num_rows > 0) {
                                    while($row = $result->fetch_assoc()) {
                                        echo "<tr>";
                                        echo "<td>" . htmlspecialchars($row['staff_name']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['position']) . "</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='2'>No available staff found</td></tr>";
                                }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- =============SCRIPTS============= -->
    <script type="text/javascript" src="../../assets/js/main.js" charset="utf-8"></script>
    
    <!-- Automatic Logout Script -->
    <script type="text/javascript">  
        var timeoutDuration = 120000; // 2 minutes in milliseconds

        var logoutTimer = setTimeout(logoutUser, timeoutDuration);

        document.onload = resetTimer;
        document.onmousemove = resetTimer;
        document.onmousedown = resetTimer;
        document.ontouchstart = resetTimer;
        document.onclick = resetTimer;
        document.onkeypress = resetTimer;

        function logoutUser() {
            window.location.href = '../../Login_validation/logout.php';
        }

        function resetTimer() {
            clearTimeout(logoutTimer);
            logoutTimer = setTimeout(logoutUser, timeoutDuration);
        } 
    </script>
</body>
</html>