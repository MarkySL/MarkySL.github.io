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
    header("location: ../../Login_validation/login.php");
    exit;
}

//Determine how many records per page
$records_per_page = 10;

// Getting the current page number
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;

// Calculate the offset
$offset = ($page - 1) * $records_per_page;

// Calculate total pages
$total_rows = $con->query("SELECT COUNT(*) FROM follow_ups")->fetch_row()[0];
$total_pages = ceil($total_rows / $records_per_page);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!--CSS Code-->
    <link rel="stylesheet" href="../../assets/css/follow-style.css">
    <link rel="shortcut icon" href="../../assets/imgs/PetAlliesFavicon.png" type="image/x-icon">
    <title>Admin Dashboard</title>

    <!--Fontawesome Scripts Icons-->
    <script src="https://kit.fontawesome.com/acd6544335.js" crossorigin="anonymous"></script>

    <!--AJAX JQuery Script-->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Sweet Alert Message -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function(){
            // Delegate the click event for dynamic '.labImage-name' links
            $('#search_followupTable').on('click', '.labImage-name', function() {
                var imagePath = $(this).data('img-path');
                var labId = $(this).data('lab-id'); // Make sure this data attribute is set in your PHP that generates the rows.
                handleImageAction(imagePath, labId);
            });

            $("#search_followup").on("keyup", function() {
                var search_term = $(this).val();
                var page_number = 1; //Assume the first page if search term is cleared
                var records_per_page = 10; // Set to 10 or get from somewhere

                $.ajax({
                    url: "f-liveSearch.php",
                    type: "GET",
                    data: {
                        search_term: search_term
                    },
                    success: function(data) {
                        $("#search_followupTable").html(data);
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

                <!--Title-->
                <div class="table_title">
                    <h2>Pet Treatment and Diagnosis</h2>
                    <?php
                        if (isset($_SESSION['update_success'])) {
                            echo '<div class="success">' . $_SESSION['update_success'] .'<span class="close-alert">&times;</span></div>';
                            // Unset or clear the success message to prevent it from displaying again on refresh
                            unset($_SESSION['update_success']);
                        }

                        if (isset($_SESSION['error_message'])) {
                            echo '<div class="error">' . $_SESSION['error_message'] . '<span class="close-alert">&times;</span></div>';
                            // Unset or clear the error message to prevent it from displaying again on refresh
                            unset($_SESSION['error_message']);
                        }
                    ?>
                </div>

                <div class="user">
                    <!--Edit this to have a function of upload image-->
                    <img src="../../assets/imgs/PetAlliesLogowithBG.png" alt="No Image">
                </div>
            </div>

            <div class="follow_up">
                <div class="table_header">
                    <!--Display Pagination Links-->
                    <div class="pagination">
                        <?php
                            // Determine the sorting column
                            $allowed_columns = ["id", "client", "petname", "weight", "temperature", "complaints", "treatment", "datevisit", "followup"];  // Add other column names as needed
                            $sort_column = isset($_GET['column']) && in_array(strtolower($_GET['column']), $allowed_columns) ? $_GET['column'] : 'id';

                            // Determine the sorting order
                            $allowed_orders = ["asc", "desc"];
                            $sort_order = isset($_GET['order']) && in_array(strtolower($_GET['order']), $allowed_orders) ? $_GET['order'] : 'asc';

                            // Determine the range of pages to display
                            $start_page = max(1, $page - 2);
                            $end_page = min($total_pages, $start_page + 4);
                            $start_page = max(1, $end_page - 4);  // Adjust start_page if near the end

                            // "Prev" Link
                            if ($page > 1) {
                                $prev_page = max(1, $start_page - 5); // Move back 5 pages
                                echo "<a href='follow-ups.php?page=$prev_page&column=$sort_column&order=$sort_order'>Prev</a>";
                            }

                            // Page number links
                            for ($i = $start_page; $i <= $end_page; $i++) {
                                if ($i == $page) {
                                    echo "<span>$i</span>";
                                } else {
                                    echo "<a href='follow-ups.php?page=$i&column=$sort_column&order=$sort_order'>$i</a>";
                                }
                            }

                            // "Next" link
                            if ($page < $total_pages) {
                                $next_page = min($total_pages, $end_page + 1);  // Move forward 5 pages
                                echo "<a href='follow-ups.php?page=$next_page&column=$sort_column&order=$sort_order'>Next</a>";
                            }

                            //ASC and DESC Links
                            if ($page < $total_pages) {
                                echo "<a href='follow-ups.php?page=$page&column=$sort_column&order=asc'>&#x2191;</a>";
                                echo "<a href='follow-ups.php?page=$page&column=$sort_column&order=desc'>&#x2193;</a>";
                            } else {
                                echo "<a href='follow-ups.php?page=$page&column=$sort_column&order=asc'>&#x2191;</a>";
                                echo "<a href='follow-ups.php?page=$page&column=$sort_column&order=desc'>&#x2193;</a>";
                            }
                        ?>
                    </div>
                    <!-- Table Search Bar -->
                    <div class="search">
                        <label>
                            <input type="text" placeholder="Search Here" id="search_followup">
                            <i class="fa-solid fa-magnifying-glass search_icon"></i>
                        </label>
                    </div>
                    
                    <!-- Table Add Button-->
                    <button class="add_btn" id="followup_openModalBtn">
                        <a href="#">Add Informations</a>
                    </button>
                </div>
                <div class="table_body">
                    <table>
                        <thead>
                            <tr>
                                <th>id</th>
                                <th>Owner</th>
                                <th>PetName</th>
                                <th>Weight</th>
                                <th>Temperature</th>
                                <th>Complaints/<br>Diagnosis</th>
                                <th>Treatments/<br>Test</th>
                                <th>Date Visit</th>
                                <th>Follow up</th>
                                <th>Modify</th>
                                <th>Lab Images</th>    
                            </tr>
                        </thead>
                        <tbody id="search_followupTable">
                            <?php
                                error_reporting(E_ALL);
                                // Modify the query to include an ORDER BY clause
                                $query = "SELECT * FROM follow_ups ORDER BY $sort_column $sort_order LIMIT $offset, $records_per_page";
                                $result = $con->query($query);
                                
                                //Loop through the result from the database and format the date fields
                                if ($result->num_rows > 0) {
                                    while($row = $result->fetch_assoc()) {
                                        $dateVisitFormatted = $row['datevisit'] ? date('m-d-Y', strtotime($row['datevisit'])) : 'N/A';
                                        $followUpFormatted = $row['followup'] ? date('m-d-Y', strtotime($row['followup'])) : 'N/A';

                                        echo "<tr>";
                                        echo "<td>{$row['id']}</td>"; 
                                        echo "<td>{$row['client']}</td>";
                                        echo "<td>{$row['petname']}</td>";
                                        echo "<td>{$row['weight']}</td>";
                                        echo "<td>{$row['temperature']}</td>";
                                        echo "<td>{$row['complaints']}</td>";
                                        echo "<td>{$row['treatment']}</td>";
                                        echo "<td>{$dateVisitFormatted}</td>";
                                        echo "<td>{$followUpFormatted}</td>";

                                        // Update Button
                                        echo '<td>
                                        <a href="f_update.php?updateid='.$row['id'].'"><button class="update_btn">Update</button></a>'; 
                                        
                                        // Start of Upload Button
                                        // Check if staff_iamge exists and display accordingly
                                        if (!empty($row['lab_image'])) {
                                            $imageName = basename($row['lab_image']);
                                            // Make the image name clickable for deletion or download
                                            echo "<td><a href='javascript:void(0);'
                                            class='labImage-name'
                                            data-img-path='" . htmlspecialchars($row['lab_image']) . "'
                                            data-lab-id='" . htmlspecialchars($row['id']) . "'
                                            onclick='handleImageAction(\"" . htmlspecialchars($row['lab_image']) . "\", \"" . htmlspecialchars($row['id']) . "\")'>" . htmlspecialchars($imageName) . "</a></td>";
                                        } else {
                                            // Add an upload form for lab images
                                            echo "<td>
                                                <form action='lab_imageUpload.php' method='post' enctype='multipart/form-data' class='uploadForm'>
                                                    <input type='file' name='labImage' id='fileInput_" . $row['id'] . "' required style='display: none;'>
                                                    <label for='fileInput_" . $row['id'] . "' class='upload-button'>Upload Lab Image</label>
                                                    <input type='hidden' name='labId' value='" . $row['id'] . "'>
                                                </form>
                                            </td>";
                                        }
                                        // End of Upload Button
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='11'>No records found</td></tr>";
                                }
                            ?>
                        </tbody>
                    </table>     
                </div>
            </div>
        </div>

        <!-- ============== Modal/Popup Form ================ -->
        <div class="backdrop" id="followup_modal">
            <div class="popup">
            <div class="close-btn">
                <i class="fa-solid fa-xmark" class="x_icon" id="followup_closeModalBtn"></i>
            </div>
                <form id="myForm" action="followup_check.php" method="post">
                    <div class="form">
                        <h2>Treatment and Diagnosis Record</h2>
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
                            <label>Petname:</label>
                            <input type="text" id="petname" name="petname" required>
                        </div>
                        <div class="popup_form">
                            <label>Weight:</label>
                            <input type="text" id="weight" name="weight" required>
                        </div>
                        <div class="popup_form">
                            <label>Temperature:</label>
                            <input type="text" id="temp" name="temperature" required>
                        </div>
                        <div class="popup_form">
                            <label>Complaints/<br>Diagnosis</label>
                            <textarea rows="3" cols="26" name="complaints">Complaints and Diagnosis....</textarea>
                        </div>
                        <div class="popup_form">
                            <label>Treatment/<br>Test</label>
                            <textarea rows="3" cols="26" name="treatment">Treatment and Test....</textarea>
                        </div>
                        <div class="popup_form">
                            <label>Date Visit:</label>
                            <input type="date" id="datevisit" name="datevisit" placeholder="" required>
                        </div>
                        <div class="popup_form">
                            <label>Days until Follow-Up:</label>
                            <input type="number" id="followupDays" name="followupDays" value="7" min="1" required> <!-- default value is 7 -->
                        </div>
                        <div class="popup_form">
                            <button type="submit" name="submitBtn">Add Details</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>        
    </div>

    <!-- ============= Delete Modal ============= -->
    <div id="deleteModal" class="backdrop">
        <div class="modal-content">
            <span class="close-btn" id="deleteOption_close">&times;</span>
            <p>What would you like to do?</p>
            <button id="deleteRowBtn">Delete Record</button> 
            <button id="deleteImgBtn">Delete Image</button>
        </div>
    </div>


    <!-- =============SCRIPTS============= -->
    <script type="text/javascript" src="../../assets/js/main.js" charset="utf-8"></script>
    <script src="followup_modal.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dateVisitField = document.getElementById('datevisit');
            const followupDaysField = document.getElementById('followupDays');
            const followupDateField = document.getElementById('followupDate');

            function isValidDate(dateString) {
                const regEx = /^\d{4}-\d{2}-\d{2}$/;
                if (!dateString.match(regEx)) return false;
                const d = new Date(dateString);
                const dNum = d.getTime();
                if (!dNum && dNum !== 0) return false;
                return d.toISOString().slice(0,10) === dateString;
            }

            function updateFollowupDate() {
                // Validate the date and the days before proceeding
                if (!isValidDate(dateVisitField.value) || isNaN(followupDaysField.value)) {
                    return;
                }

                let dateVisit = new Date(dateVisitField.value);
                const followupDays = Number(followupDaysField.value);

                dateVisit.setDate(dateVisit.getDate() + followupDays);
                const followupDate = dateVisit.toISOString().split('T')[0];

                followupDateField.value = followupDate;
            }

            // Call the function initially to set the default value
            if (dateVisitField.value && followupDaysField.value) {
                updateFollowupDate();
            }

            // Update the follow-up date whenever the other fields change
            dateVisitField.addEventListener('change', updateFollowupDate);
            followupDaysField.addEventListener('change', updateFollowupDate);
        });

    </script>

    <!-- Upload Function Button Sweet Alert Message Script -->
    <script>
        $(document).ready(function () {
            // Listen for the file input change event
            $(document).on('change', 'input[type="file"]', function() {
                // Auto-submit the form containing this file input
                $(this).closest('form').submit();
            });
            // Ajax for Submission Logic
            $(document).on('submit', '.uploadForm', function(e) {
                e.preventDefault();
                var formData = new FormData(this);

                $.ajax({
                    url: 'lab_imageUpload.php',
                    type: 'POST',
                    data: formData,
                    success: function(data) {
                        if (data.success) {
                            Swal.fire('Success', data.message, 'success').then(function() {
                                location.reload(); //Reload the page to reflect changes
                            });
                        } else {
                            Swal.fire('Error', data.message, 'error');
                        }
                    },
                    cache: false,
                    contentType: false,
                    processData: false,
                    error: function() {
                        Swal.fire('Error', 'An unexpected error occurred.', 'error');
                    }
                });
            });
        });
    </script>

    <!-- Deletion Script for Uploaded Image with Sweet Alert Message -->
    <script>
        // Handling the Image Action upon clicking
        function handleImageAction(imagePath, labId) {
            Swal.fire({
                title: 'Select Action',
                text: "Do you want to delete or download this image?",
                icon: 'question',
                showCancelButton: true,
                showDenyButton: true,
                confirmButtonText: 'Delete',
                denyButtonText: 'Download',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#d33',
                denyButtonColor: '#3085d6'
            }).then((result) => {
                if (result.isConfirmed) { // User clicked on 'Delete'
                    confirmDelete(labId);
                } else if (result.isDenied) { // User clicked on 'Download'
                    downloadImage(imagePath);
                }
            });
        }

        // Handling the Deletion of Lab File Image
        function confirmDelete(labId) {
            Swal.fire({
                title: 'Are you sure?',
                text: "Once deleted, you will not be able to recover this image!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: 'delete_img.php', // Your PHP file that will handle the deletion
                        type: 'POST',
                        data: { 'labId': labId },
                        success: function(response) {
                            Swal.fire(
                                'Deleted!',
                                'Image has been deleted successfully.',
                                'success'
                            ).then(() => {
                                location.reload(); // Reload the page to reflect changes
                            });
                        },
                        error: function() {
                            Swal.fire(
                                'Error!',
                                'Error deleting image.',
                                'error'
                            );
                        }
                    });
                }
            });
        }

        // Handling the Download of Lab File Image
        function downloadImage(imagePath) {
            // Trigger the download
            window.location.href = 'download_lab_img.php?file=' + imagePath;
        }
    </script>
</body>
</html
