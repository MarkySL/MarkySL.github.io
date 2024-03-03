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
$total_rows = $con->query("SELECT COUNT(*) FROM vet_profiles")->fetch_row()[0];
$total_pages = ceil($total_rows / $records_per_page);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!--CSS Code-->
    <link rel="stylesheet" href="../../assets/css/vetstaff.css">
    <link rel="shortcut icon" href="../../assets/imgs/PetAlliesFavicon.png" type="image/x-icon">

    <title>Record Monitoring</title>

    <!--Fontawesome Scripts Icons-->
    <script src="https://kit.fontawesome.com/acd6544335.js" crossorigin="anonymous"></script>

    <!--AJAX JQuery Script-->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Sweet Alert Message -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Live search AJAX -->
    <script>
        $(document).ready(function(){

            $("#search_profileFunction").on("keyup", function() {
                var search_term = $(this).val();
                var page_number = 1; //Assume the first page if search term is cleared
                var records_per_page = 10; // Set to 10 or get from somewhere

                $.ajax({
                    url: "profile_liveSearch.php",
                    type: "GET",
                    data: {
                        search_term: search_term
                    },
                    success: function(data) {
                        $("#search_profile").html(data);
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
                    <a href="vetstaff.php">
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
                    <h2>Veterinary Staffs</h2>
                </div>
                <div class="user">
                    <!--Edit this to have a function of upload image-->
                    <img src="../../assets/imgs/PetAlliesLogowithBG.png" alt="No Image">
                </div>
            </div>

            <div class="vetstaff">
                <div class="table_header">

                    <!--Display Pagination Links-->
                    <div class="pagination">
                        <?php
                           // Determine the range of pages to display
                            $start_page = max(1, $page - 2);
                            $end_page = min($total_pages, $start_page + 4);
                            $start_page = max(1, $end_page - 4);  // Adjust start_page if near the end

                            if ($page > 1) {
                                $prev_page = max(1, $start_page - 5); // Move back 5 pages
                                echo "<a href='vetstaff.php?page=1&sort=$sort_order'>Prev</a>"; // Prev button to take you back to page 1
                            }
                            
                            // Page number links
                            for ($i = $start_page; $i <= $end_page; $i++) {
                                if ($i == $page) {
                                    echo "<span>$i</span>";
                                } else {
                                    echo "<a href='vetstaff.php?page=$i&sort=$sort_order'>$i</a>";
                                }
                            }
                            
                           // ASC and DESC Links
                            if ($page < $total_pages) {
                                echo "<a href='vetstaff.php?page=$total_pages&sort=$sort_order'>Next</a>"; // Next button to take you to the last page
                                echo "<a href='vetstaff.php?page=$page&sort=asc'>&#x2191;</a>";  // Retain the pagination state
                                echo "<a href='vetstaff.php?page=$page&sort=desc'>&#x2193;</a>";  // Retain the pagination state
                            } else {
                                echo "<a href='vetstaff.php?page=$page&sort=asc'>&#x2191;</a>";  // Ascending order link
                                echo "<a href='vetstaff.php?page=$page&sort=desc'>&#x2193;</a>";  // Descending order link
                            }
                        ?>
                    </div>
                    
                    <!-- Table Search Bar -->
                    <div class="search">
                        <label>
                            <input type="text" placeholder="Search Here" id="search_profileFunction">
                            <i class="fa-solid fa-magnifying-glass search_icon"></i>
                        </label>
                    </div>

                    <!-- Table Add Button -->
                    <button type="submit" id="addStaffbtn">
                        Add Staff
                    </button>
                </div>
        
                <div class="table_body">
                    <table>
                        <thead>
                            <tr>
                                <th>id</th>
                                <th>Staff Name</th>
                                <th>Position</th>
                                <th>Availability</th>
                                <th>Staff Images</th>
                            </tr>
                        </thead>
                        <tbody id="search_profile">
                            <?php
                                // Displaying all the datas from vet_profiles table in database
                                $stmt = $con->prepare("SELECT * FROM vet_profiles ORDER BY id $sort_order LIMIT ? OFFSET ?");
                                $stmt->bind_param('ii', $records_per_page, $offset); // binding the limit and offset parameters
                                $stmt->execute();
                                $result = $stmt->get_result();

                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        $availabilityChecked = $row['availability'] == 1 ? 'checked' : '';
                                        echo "<tr>";
                                        echo "<td>" . $row['id'] . "</td>";
                                        echo "<td>" . htmlspecialchars($row['staff_name']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['position']) . "</td>";
                                        echo "<td><label class='switch'><input type='checkbox' class='availability-toggle' data-id='" . $row['id'] . "' data-availability='" . $row['availability'] . "' " . $availabilityChecked . "><span class='slider round'></span></label></td>";                
                                        // Start of Upload Button
                                        // Check if staff_iamge exists and display accordingly
                                        if (!empty($row['staff_image'])) {
                                            $imageName = basename($row['staff_image']);
                                            // Make the image name clickable for deletion
                                            echo "<td><a href='javascript:void(0);
                                            ' class='staffImage-name' data-img-path='" . htmlspecialchars($row['staff_image']) . "'
                                            onclick='confirmDelete(" . $row['id'] . ")'>" . htmlspecialchars($imageName) . "</a></td>";
                                        } else {
                                            // Add an upload form for staff images
                                            echo "<td>
                                                    <form action='staff_imageUpload.php' method='post' enctype='multipart/form-data' class='uploadForm'>
                                                        <input type='file' name='staffImage' required>
                                                        <input type='hidden' name='staffId' value='" . $row['id'] . "'>
                                                        <button type='submit'>Upload</button>
                                                    </form>
                                            </td>";
                                        }
                                        // End of Upload Button
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='5'>No records found</td></tr>";
                                }
                                $stmt->close();
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Forms -->
    <div class="backdrop" id="addStaffModal">
        <!-- Modal content -->
        <div class="modal">
            <span class="close" id="closeModal">&times;</span>
            <form action="addingStaff.php" method="POST" id="addStaffForm">
                <div class="modal_form">
                    <h2>Add Staff</h2>
                    <div class="modal_content">
                        <label>Name:</label>
                        <input type="text" id="staffName" name="staffName" required>
                    </div>
                    <div class="modal_content">
                        <label>Position:</label>
                        <!-- You can add another option for value -->
                        <select id="staffPosition" name="staffPosition" required>
                            <option value="Doctor">Doctor</option>
                            <option value="Staff">Staff</option>
                        </select>
                    </div>
                    <div class="modal_content">
                        <button type="submit">Add Staff</button>
                    </div>
                </div>
            </form>
        </div>
    </div> 

    <!-- Delete and Download Uploaded Image Modal -->
    <div id="imageModal" class="backdrop" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Image Options</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Choose an option for the image:</p>
                    <a href="#" id="downloadImage" download>Download</a> |
                    <button id="deleteImage" class="btn btn-danger">Delete</button>
                </div>
            </div>
        </div>
    </div>
    

    <!-- =============SCRIPTS============= -->
    <script src="../../assets/js/main.js"></script>

    <!-- Script for Adding Staff Modal Form  -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Get the modal and buttons
            var modal = document.getElementById("addStaffModal");
            var openModalBtn = document.getElementById("addStaffbtn");
            var closeModalBtn = document.getElementById("closeModal");

            // Open the modal when the open button is clicked
            openModalBtn.addEventListener("click", function () {
            modal.style.display = "block";
            });

            // Close the modal when the close button is clicked
            closeModalBtn.addEventListener("click", function () {
            modal.style.display = "none";
            });

            // Close the modal if the user clicks outside the modal content
            window.addEventListener("click", function (event) {
            if (event.target === modal) {
                modal.style.display = "none";
            }
            });
        });
    </script>

    <!-- Sweet Alert Message Script for Adding Staff Modal -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            var form = document.getElementById("addStaffForm"); // Reference the form with its ID
    
            form.addEventListener("submit", function (e) {
                e.preventDefault(); // Prevent the normal form submission
                
                var formData = new FormData(form);

                fetch('addingStaff.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: 'Success!',
                            text: data.message,
                            icon: 'success',
                            timer: 2000,
                            timerProgressBar: true,
                            willClose: () => {
                                document.getElementById("addStaffModal").style.display = "none";
                            }
                        });
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: data.message,
                            icon: 'error',
                            timer: 2000,
                            timerProgressBar: true
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        title: 'Error!',
                        text: 'There was an error processing your request.',
                        icon: 'error',
                        timer: 2000,
                        timerProgressBar: true
                    });
                });
            });
        });
    </script>
    
    <!-- Toggling Availability Button -->
    <script>
        $(document).ready(function() {
            $('#search_profile').on('click', '.availability-toggle', function() {
                var button = $(this);
                var staffId = button.data('id');
                var currentAvailability = button.data('availability');
                var newAvailability = currentAvailability == 1 ? 0 : 1;

                $.ajax({
                    url: 'updateAvailability.php',
                    type: 'POST',
                    data: { id: staffId, availability: newAvailability },
                    success: function(response) {
                        response = JSON.parse(response);
                        if(response.success) {
                            button.prop('checked', newAvailability === 1);
                            // Update the button data-availability attribute
                            button.data('availability', newAvailability);
                        } else {
                            alert('Error updating availability.');
                        }
                    },
                    error: function() {
                        alert('Error updating availability.');
                    }
                });
            });
        });
    </script>

    <!-- Deletion Script for Uploaded Image with Sweet Alert Message -->
    <script>
        function confirmDelete(staffId) {
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
                        url: 'delete_image.php', // Your PHP file that will handle the deletion
                        type: 'POST',
                        data: { 'staffId': staffId },
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
    </script>

    <!-- Upload Function Button Sweet Alert Message Script -->
    <script>
        $(document).ready(function () {
            $(document).on('submit', '.uploadForm', function(e) {
                e.preventDefault();
                var formData = new FormData(this);

                $.ajax({
                    url: 'staff_imageUpload.php',
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

    <!-- Image Preview Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const imageNames = document.querySelectorAll('.staffImage-name'); //Fetch staffImage-name class in php upload button

            // Create the image preview element
            const imgPreview = document.createElement('img');
            imgPreview.className = 'staffImage-preview'; //Fecth style for staffImage-preview
            document.body.appendChild(imgPreview);

            // Using event delegation
            document.body.addEventListener('mouseover', function(e) {
                if (e.target.classList.contains('staffImage-name')) {
                    const imgPath = e.target.getAttribute('data-img-path');
                    imgPreview.src = imgPath;
                    imgPreview.style.display = 'block';
                    updatePreviewPosition(e);
                }
            });

            document.body.addEventListener('mousemove', function(e) {
                if (e.target.classList.contains('staffImage-name')) {
                    updatePreviewPosition(e);
                }
            });

            document.body.addEventListener('mouseout', function(e) {
                if (e.target.classList.contains('staffImage-name')) {
                    imgPreview.style.display = 'none';
                }
            });

            function updatePreviewPosition(e) {
                const previewOffsetX = 100; // Adjust as needed for X-axis offset
                const previewOffsetY = -100; // Adjust as needed for Y-axis offset

                // Calculate position to try and keep preview in the viewport
                let posX = e.pageX - previewOffsetX - 192; // Subtract the preview width (192px) and offset;
                let posY = e.pageY + previewOffsetY;

                // Ensure the preview does not go off the left edge of the screen
                if (posX < 0) {
                    posX = 20; // Adjustment to avoid sticking to the edge if there's no space on the left
                }

                // Ensure the preview stays within the viewport vertically
                const previewHeight = 192; // Preview height in pixels
                if (posY + previewHeight > window.innerHeight) {
                    posY = window.innerHeight - previewHeight - 20; // Adjust to not stick to the edge
                }

                imgPreview.style.top = `${posY}px`;
                imgPreview.style.left = `${posX}px`;
            }
        });
    </script>
    
</body>
</html>