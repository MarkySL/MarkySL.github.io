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

/*This will check if the username is set in the session, if it's not then it redirects to login.php*/
if (!isset($_SESSION["username"])) {
    header("location:../../Login_validation/login.php");
    exit;
}

// Determine the sorting order
$allowed_orders = ["ASC", "DESC"];
$sort_order = isset($_GET['sort']) && in_array(strtoupper($_GET['sort']), $allowed_orders) ? $_GET['sort'] : 'ASC';

//Determine how many records per page
$records_per_page = 10;

// Getting the current page number
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;

// Calculate the offset
$offset = ($page - 1) * $records_per_page;

// Calculate total pages
$total_rows = $con->query("SELECT COUNT(*) FROM monitoring")->fetch_row()[0];
$total_pages = ceil($total_rows / $records_per_page);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!--CSS Code-->
    <link rel="stylesheet" href="../../assets/css/monitoring-style.css">
    <link rel="shortcut icon" href="../../assets/imgs/PetAlliesFavicon.png" type="image/x-icon">

    <title>Record Monitoring</title>

    <!--Fontawesome Scripts Icons-->
    <script src="https://kit.fontawesome.com/acd6544335.js" crossorigin="anonymous"></script>

    <!--AJAX JQuery Script-->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        $(document).ready(function(){
            var currentPage = 1; //Assuming page 1 by default
            var recordsPerPage = 10; //Change as needed to how many to display when searching

            $("#search_function").on("keyup", function() {
                var search_term = $(this).val();

                $.ajax({
                    url: "live_search.php",
                    type: "GET",
                    data: {
                        search_term: search_term
                    },
                    success: function(data) {
                        $("#search_table").html(data);
                    }
                });
            });
        });
    </script>
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

                <!--Title-->
                <div class="table_title">
                    <h2>Pet Allies Record Monitoring</h2>
                    <?php
                        if (isset($_SESSION['delete_msg'])) {
                            echo '<div class="error">' . $_SESSION['delete_msg'] . '<span class="close-alert">&times;</span></div>';
                            // Unset or clear the error message to prevent it from displaying again on refresh
                            unset($_SESSION['delete_msg']);
                        }
                    ?>
                    </div>
                <div class="user">
                    <!--Edit this to have a function of upload image-->
                    <img src="../../assets/imgs/PetAlliesLogowithBG.png" alt="No Image">
                </div>
            </div>

            <div class="monitoring">
                <div class="table_header">

                    <!--Display Pagination Links-->
                    <div class="pagination">
                        <?php
                            // Fetch data from the database
                            $sql = "SELECT * FROM monitoring ORDER BY id $sort_order LIMIT $offset, $records_per_page";
                            $result = $con->query($sql);

                           // Determine the range of pages to display
                            $start_page = max(1, $page - 2);
                            $end_page = min($total_pages, $start_page + 4);
                            $start_page = max(1, $end_page - 4);  // Adjust start_page if near the end

                            if ($page > 1) {
                                $prev_page = max(1, $start_page - 5); // Move back 5 pages
                                echo "<a href='monitoring.php?page=1&sort=$sort_order'>Prev</a>"; // Prev button to take you back to page 1
                            }
                            
                            // Page number links
                            for ($i = $start_page; $i <= $end_page; $i++) {
                                if ($i == $page) {
                                    echo "<span>$i</span>";
                                } else {
                                    echo "<a href='monitoring.php?page=$i&sort=$sort_order'>$i</a>";
                                }
                            }
                            
                           // ASC and DESC Links
                            if ($page < $total_pages) {
                                echo "<a href='monitoring.php?page=$total_pages&sort=$sort_order'>Next</a>"; // Next button to take you to the last page
                                echo "<a href='monitoring.php?page=$page&sort=asc'>&#x2191;</a>";  // Retain the pagination state
                                echo "<a href='monitoring.php?page=$page&sort=desc'>&#x2193;</a>";  // Retain the pagination state
                            } else {
                                echo "<a href='monitoring.php?page=$page&sort=asc'>&#x2191;</a>";  // Ascending order link
                                echo "<a href='monitoring.php?page=$page&sort=desc'>&#x2193;</a>";  // Descending order link

                            }
                        ?>
                    </div>
                    
                    <!-- Table Search Bar -->
                    <div class="search">
                        <label>
                            <input type="text" placeholder="Search Here" id="search_function">
                            <i class="fa-solid fa-magnifying-glass search_icon"></i>
                        </label>
                    </div>

                    <!-- Table Add Button -->
                    <button type="submit" class="add_btn" id="openModalBtn">
                            <a href="#">Add Client</a>
                    </button>

                </div>
        
                <div class="table_body">
                    <table>
                        <thead>
                            <tr>
                                <th>id</th>
                                <th>Owner</th>
                                <th>Phone</th>
                                <th>Address</th>
                                <th>Pet Name</th>
                                <th>Birthday</th>
                                <th>Gender</th>
                                <th>Species</th>
                                <th>Breed</th>
                                <th>C.Markings</th>
                                <th>Modify</th>
                            </tr>
                        </thead>
                        <tbody id="search_table">
                            <?php
                                error_reporting(E_ALL);

                                // Function to format date
                                function formatDate($dateStr) {
                                    if ($dateStr) {
                                        $date = new DateTime($dateStr);
                                        return $date->format('m/d/Y');
                                    }
                                    return 'N/A';  // Return 'N/A' or '' if dateStr is null or empty
                                }

                                // Using prepared statement for the SQL query
                                $stmt = $con->prepare("SELECT * FROM monitoring ORDER BY id $sort_order LIMIT ? OFFSET ?");
                                $stmt->bind_param("ii", $records_per_page, $offset);
                                $stmt->execute();

                                $result = $stmt->get_result();
                                if ($result && $result->num_rows > 0) {
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        $id = $row['id'];
                                        $client = $row['client'];
                                        $phone = $row['phone'];
                                        $address = $row['address'];
                                        $petname = $row['petname'];
                                        $p_bday = formatDate($row['petbday']);
                                        $p_gender = $row['gender'];
                                        $species = $row['species'];
                                        $breed = $row['breed'];
                                        $colormarkings = $row['colormarkings'];

                                        echo ' <tr>
                                        <th>'.$id.'</th>
                                        <td>'.$client.'</td>
                                        <td>'.$phone.'</td>
                                        <td>'.$address.'</td>
                                        <td>'.$petname.'</td>
                                        <td>'.$p_bday.'</td>
                                        <td>'.$p_gender.'</td>
                                        <td>'.$species.'</td>
                                        <td>'.$breed.'</td>
                                        <td>'.$colormarkings.'</td>
                                        <td>
                                        <a href="m_update.php?updateid='.$id.'"><button class="update_btn">Update</button></a>
                                        </td>
                                        </tr>';
                                    } 
                                } else {
                                    echo "<tr><td colspan='11'>No records found</td></tr>";
                                }
                                $stmt->close();
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- ================== Modal/Popup Form ============ -->
        <div class="backdrop" id="modal">
            <div class="popup">
            <div class="close-btn">
                <i class="fa-solid fa-xmark" class="x_icon" id="closeModalBtn"></i>
            </div>
                <form id="myForm" action="monitoring-check.php" method="post">
                    <div class="form">
                        <h2>Client Record</h2>
                        <?php
                            // Check if a success message is set and display it
                            if (isset($_SESSION['success_message'])) {
                                echo '<div class="success">' . $_SESSION['success_message'] .'<span class="close-alert">&times;</span></div>';
                                // Unset or clear the success message to prevent it from displaying again on refresh
                                unset($_SESSION['success_message']);
                            }

                            // Check if an error message is set and display it
                            if (isset($_SESSION['error_message'])) {
                                echo '<div class="error">' . $_SESSION['error_message'] . '<span class="close-alert">&times;</span></div>';
                                // Unset or clear the error message to prevent it from displaying again on refresh
                                unset($_SESSION['error_message']);
                            }
                        ?>

                        <div class="popup_form">
                            <label>Owner:</label>
                            <input type="text" id="client" name="client" required>
                        </div>
                        <div class="popup_form">
                            <label>Contact#:</label>
                            <input type="text" id="phone" name="phone" required>
                        </div>
                        <div class="popup_form">
                            <label>Address:</label>
                            <input type="text" id="address" name="address" required>
                        </div>
                        <div class="popup_form">
                            <label>Pet Name:</label>
                            <input type="text" id="pet" name="petname" required>
                        </div>
                        <div class="popup_form">
                            <label>Pet Birthday<br>(Optional):</label>
                            <input type="date" id="pet-birth" name="petbday">
                        </div>
                        <div class="popup_form">
                            <label>Gender:</label>
                            <label>
                                <input type="radio" name="petgender" value="Male" required> Male
                            </label>
                            <label>
                                <input type="radio" name="petgender" value="Female" required> Female
                            </label>
                        </div>
                        <div class="popup_form">
                            <label>Species:</label>
                            <input type="text" id="species" name="species" required>
                        </div>
                        <div class="popup_form">
                            <label>Breed:</label>
                            <input type="text" id="breed" name="breed" required>
                        </div>
                        <div class="popup_form">
                            <label>Color Markings:</label>
                            <input type="text" id="colormarkings" name="colormarkings" required>
                        </div>
                        <div class="popup_form">
                            <button type="submit" name="submitBtn">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- =============SCRIPTS============= -->
    <script src="../../assets/js/main.js"></script>
    <script src="monitoring_modal.js"></script>
    
    
</body>
</html>