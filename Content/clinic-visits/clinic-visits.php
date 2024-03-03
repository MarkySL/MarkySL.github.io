<?php
session_start();

//Set Default Timezone for Logging of Errors
date_default_timezone_set('Asia/Manila');

// Set error logging
ini_set('log_errors', 1);
ini_set('error_log', '../../assets/error/error.log');

// Turn off error reporting to the screen
ini_set('display_errors', 0);

/*This will check if the username is set in the session, if it's not then it redirects to login.php*/
if (!isset($_SESSION["username"])) {
    header("location: ../../Login_validation/login.php");
    exit;
}

include 'qr_config.php'; // Database connection

$perPage = 10;                        // Items per page
$page = $_GET['page'] ?? 1;           // Current page
$offset = ($page - 1) * $perPage;     // Offset for SQL


$sort = $_GET['sort'] ?? 'id'; // Default sorting column
$order = $_GET['order'] ?? 'ASC';     // Default sorting order

$monthFilter = $_GET['month'] ?? ''; // Default to no filter
$yearFilter = $_GET['year'] ?? '';   // Default to no filter
$limit = 10;  // Or whatever your default page limit is
$start = ($page - 1) * $limit;

$whereClause = "";
if ($monthFilter && $yearFilter) {
    $whereClause = "WHERE MONTH(scan_time) = $monthFilter AND YEAR(scan_time) = $yearFilter";
} elseif ($monthFilter) {
    $whereClause = "WHERE MONTH(scan_time) = $monthFilter";
} elseif ($yearFilter) {
    $whereClause = "WHERE YEAR(scan_time) = $yearFilter";
}

if ($monthFilter && $yearFilter) {
    // Your query without the LIMIT clause
    $query = "SELECT * FROM your_table WHERE MONTH(column) = $monthFilter AND YEAR(column) = $yearFilter";
} else {
    // Your existing query with the LIMIT clause
    $query = "SELECT * FROM your_table LIMIT $start, $limit";
}

// Bypass the pagination limit to display all the possible outcomes for monthfilter and yearfilter
$limitClause = ($monthFilter || $yearFilter) ? "" : "LIMIT $perPage OFFSET $offset";

$query = "SELECT id, DATE_FORMAT(scan_time, '%M %Y') as month_year, DATE_FORMAT(scan_time, '%d %M %Y %h:%i:%s %p') as formatted_time FROM qr_scans $whereClause ORDER BY $sort $order $limitClause";

$stmt = $pdo->prepare($query);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

#$totalRecords = $pdo->query("SELECT count(*) FROM qr_scans")->fetchColumn();
$totalRecordsQuery = "SELECT count(*) FROM qr_scans $whereClause";
$totalRecords = $pdo->query($totalRecordsQuery)->fetchColumn();
$totalPages = ceil($totalRecords / $perPage);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!--CSS Code-->
    <link rel="stylesheet" href="../../assets/css/visits-style.css">
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
                    <a href="clinic-visits.php">
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
                    <h2>Monthly Clinic Visits</h2>
                </div>

                <div class="user">
                    <img src="../../assets/imgs/PetAlliesLogowithBG.png" alt="No Image">
                </div>
            </div>

            <div class="vet_visits">
                <div class="table_header">

                    <!-- Month Dropdown -->
                    <select id="monthSelector" onchange="applyFilter()">
                        <option value="">Select Month</option>
                        <?php
                        $months = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
                        foreach ($months as $key => $month) {
                            echo "<option value='" . ($key + 1) . "'" . ($monthFilter == ($key + 1) ? " selected" : "") . ">$month</option>";
                        }
                        ?>
                    </select>

                    <!-- Year Dropdown -->
                    <select id="yearSelector" onchange="applyFilter()">
                        <option value="">Select Year</option>
                        <?php
                        for ($year = 2019; $year <= date('Y'); $year++) {
                            echo "<option value='$year'" . ($yearFilter == $year ? " selected" : "") . ">$year</option>";

                        }
                        ?>
                    </select>

                </div>
                <!-- Adding Pagination and Sorting above ID column -->
                <div class="pagination">
                    <?php
                        if ($page > 1) {
                            echo "<a href='clinic-visits.php?page=" . ($page - 1) . "&month=$monthFilter&year=$yearFilter&sort=$sort&order=$order'>Prev</a>";
                        }

                        for ($i = 1; $i <= $totalPages; $i++) {
                            if ($i == $page) {
                                echo "<span>$i</span>";
                            } else {
                                echo "<a href='clinic-visits.php?page=$i&month=$monthFilter&year=$yearFilter&sort=$sort&order=$order'>$i</a>"; // This way, the sort and order parameters are carried through when you paginate, maintaining the chosen sort order.
                            }
                        }                        
                            
                        if ($page < $totalPages) {
                            echo "<a href='clinic-visits.php?page=" . ($page + 1) . "&month=$monthFilter&year=$yearFilter&sort=$sort&order=$order'>Next</a>";
                            echo "<a href='clinic-visits.php?page=$page&month=$monthFilter&year=$yearFilter&sort=id&order=ASC'>&#x2191;</a>";
                            echo "<a href='clinic-visits.php?page=$page&month=$monthFilter&year=$yearFilter&sort=id&order=DESC'>&#x2193;</a>";
                        } else {
                            echo "<a href='clinic-visits.php?sort=id&order=ASC'>&#x2191;</a>";
                            echo "<a href='clinic-visits.php?sort=id&order=DESC'>&#x2193;</a>";
                        }
                    ?>
                            
                    <a href='clinic-visits.php'>Default View</a>
                    
                    <a href='clinic-visits.php?sort=scan_time&order=ASC'>Sort by Time Asc</a>
                    <a href='clinic-visits.php?sort=scan_time&order=DESC'>Sort by Time Desc</a>
                </div>

                <div class="table_body">
                    <table>
                        <thead>
                            <tr>
                                <th>id</th>
                                <th>Timestamp</th>
                                <th>Month</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                foreach ($results as $row) {
                                    echo "<tr>";
                                    echo "<td>" . $row['id'] . "</td>";
                                    echo "<td>" . $row['formatted_time'] . "</td>";
                                    echo "<td>" . $row['month_year'] . "</td>";
                                    echo "</tr>";
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
<script>
    function fetchFilteredData(month, year) {
    fetch(`clinic-visits.php?month=${month}&year=${year}`)
        .then(response => response.text())
        .then(data => {
            // Parse the response and replace the table's content
            let parser = new DOMParser();
            let doc = parser.parseFromString(data, 'text/html');
            let newTableBody = doc.querySelector(".table_body").innerHTML;
            document.querySelector(".table_body").innerHTML = newTableBody;
        });
}

    function applyFilter() {
        let month = document.getElementById('monthSelector').value;
        let year = document.getElementById('yearSelector').value;
        history.pushState({}, "", `clinic-visits.php?month=${month}&year=${year}`);
        fetchFilteredData(month, year);
    }
</script>
</body>
</html>