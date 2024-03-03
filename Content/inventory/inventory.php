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
$total_rows = $con->query("SELECT COUNT(*) FROM inventory")->fetch_row()[0];
$total_pages = ceil($total_rows / $records_per_page);

/* ========== For Fetching and Updating Data ===========*/
// Fetching previous data using prepared statements
$stmt = $con->prepare("SELECT * FROM inventory WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    // Assigning the fetched data
    $productPrice = $row['productPrice'];
    $productStocks = $row['productStocks'];
}
// Close statement
$stmt->close();

//Updating the fetch data here
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submitBtn'])) {
    // Get the id from the form data
    $id = $_POST['id'];
    // Retain the page where the data is edited
    $page = $_POST['page'];
    // Binding the input to the prepared statement to prevent SQL injection
    $stmt = $con->prepare("UPDATE inventory SET productPrice=?, productStocks=? WHERE id=?");
    $stmt->bind_param("iii", $_POST['productPrice'], $_POST['productStocks'], $id);
     
    if ($stmt->execute()) {
        header("Location:inventory.php?page=". $page);
        exit;
    } else {
        die($stmt->error);
    }
    exit; // Exit here so that the rest of the HTML is not included in the response
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!--CSS Code-->
    <link rel="stylesheet" href="../../assets/css/inventory.css">
    <link rel="shortcut icon" href="../../assets/imgs/PetAlliesFavicon.png" type="image/x-icon">
    <title>Admin Dashboard</title>

    <!--Fontawesome Scripts Icons-->
    <script src="https://kit.fontawesome.com/acd6544335.js" crossorigin="anonymous"></script>

    <!--AJAX JQuery Script-->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Sweet Alert Message -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Live Search Script -->
    <script>
        $(document).ready(function(){
            var currentPage = 1; //Assuming page 1 by default
            var recordsPerPage = 10; //Change as needed to how many to display when searching

             // Function to open the modal and populate it with data
             function openEditForm(id, price, stocks) {
                // Populate the modal fields with data
                $("#editPrice").val(price);
                $("#editStocks").val(stocks);
                $("#productId").val(id);

                // Show the modal
                $("#myModal").css("display", "block");
            }

            $("#search_function").on("keyup", function() {
                var search_term = $(this).val();

                $.ajax({
                    url: "live_search.php",
                    type: "GET",
                    data: {
                        search_term: search_term,
                        page: currentPage 
                    },
                    success: function(data) {
                        $("#search_table").html(data);

                        // Add a click event listener to the Edit buttons
                        $(".edit_btn").click(function() {
                            // Get the data attributes
                            var id = $(this).data("id");
                            var price = $(this).data("price");
                            var stocks = $(this).data("stocks");
                                
                            // Call the function to open the modal with data
                            openEditForm(id, price, stocks);
                        });
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
                    <a href="inventory.php">
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
                    <h2>Products</h2>
                </div>

                <div class="user">
                    <!--Edit this to have a function of upload image-->
                    <img src="../../assets/imgs/PetAlliesLogowithBG.png" alt="No Image">
                </div>
            </div>

            <div class="inventory">
                <div class="table_header">

                    <!-- Pagination -->
                    <!--Display Pagination Links-->
                    <div class="pagination">
                        <?php
                           // Determine the range of pages to display
                            $start_page = max(1, $page - 2);
                            $end_page = min($total_pages, $start_page + 4);
                            $start_page = max(1, $end_page - 4);  // Adjust start_page if near the end

                            if ($page > 1) {
                                $prev_page = max(1, $start_page - 5); // Move back 5 pages
                                echo "<a href='inventory.php?page=1&sort=$sort_order'>Prev</a>"; // Prev button to take you back to page 1
                            }
                            
                            // Page number links
                            for ($i = $start_page; $i <= $end_page; $i++) {
                                if ($i == $page) {
                                    echo "<span>$i</span>";
                                } else {
                                    echo "<a href='inventory.php?page=$i&sort=$sort_order'>$i</a>";
                                }
                            }
                            
                           // ASC and DESC Links
                            if ($page < $total_pages) {
                                echo "<a href='inventory.php?page=$total_pages&sort=$sort_order'>Next</a>"; // Next button to take you to the last page
                                echo "<a href='inventory.php?page=$page&sort=asc'>&#x2191;</a>";  // Retain the pagination state
                                echo "<a href='inventory.php?page=$page&sort=desc'>&#x2193;</a>";  // Retain the pagination state
                            } else {
                                echo "<a href='inventory.php?page=$page&sort=asc'>&#x2191;</a>";  // Ascending order link
                                echo "<a href='inventory.php?page=$page&sort=desc'>&#x2193;</a>";  // Descending order link
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
                </div>

                <div class="table_body">
                    <table>
                        <thead>
                            <tr>
                                <th>id</th>
                                <th>Product Name</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Stocks</th>
                                <th>Modify</th>
                                <th>Product Images</th>
                            </tr>
                        </thead>
                        <tbody id="search_table">
                            <!-- Display the data from database to the Table -->
                            <?php
                                // Using prepared statement for fetching data
                                $stmt = $con->prepare("SELECT * FROM inventory ORDER BY id $sort_order LIMIT ? OFFSET ?");
                                $stmt->bind_param("ii", $records_per_page, $offset);
                                $stmt->execute();
                                $result = $stmt->get_result();

                                if ($result && $result->num_rows > 0) {
                                    // Output data of each row
                                    while($row = $result->fetch_assoc()) {
                                        echo "<tr>";
                                        echo "<td>" . $row['id'] . "</td>";
                                        echo "<td>" . htmlspecialchars($row['product_name']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['category']) . "</td>";
                                        echo "<td>₱" . htmlspecialchars($row['productPrice']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['productStocks']) . "</td>";
                                        echo "<td><button class='edit_btn' data-id='".$row['id']."' data-price='".$row['productPrice']."' data-stocks='".$row['productStocks']."'>Edit</button>
                                        </td>";
                                        // Start of Upload Button
                                        // Check if prod_image exists and display accordingly
                                        if (!empty($row['prod_image'])) {
                                            $imageName = basename($row['prod_image']);
                                            // Make the image name clickable for deletion and add data attribute for thumbnail display (class=prodImage-name and data-img-path)
                                            echo "<td><a href='javascript:void(0);
                                            ' class='prodImage-name' data-img-path='" . htmlspecialchars($row['prod_image']) . "' 
                                            onclick='confirmDelete(" . $row['id'] . ")'>" . htmlspecialchars($imageName) . "</a></td>";
                                        } else {
                                            // Add an upload form for product images
                                            echo "<td>
                                                    <form action='prod_imageUpload.php' method='post' enctype='multipart/form-data' class='uploadForm'>
                                                        <input type='file' name='productImage' required>
                                                        <input type='hidden' name='productId' value='" . $row['id'] . "'>
                                                        <button type='submit'>Upload</button>
                                                    </form>
                                            </td>";
                                        }
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='7'>No results found</td></tr>";
                                }
                                $stmt->close();
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Form -->
    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <form id="editForm" action="#" method="POST">
                <label for="editPrice">Price:</label><br>
                <input type="number" id="editPrice" name="productPrice" value="<?php echo htmlspecialchars ($price ?? '');?>"><br>
                <label for="editStocks">Stocks:</label><br>
                <input type="number" id="editStocks" name="productStocks" value="<?php echo htmlspecialchars ($stocks ?? '');?>"><br>
                <input type="hidden" id="productId" name="id" value="<?php echo htmlspecialchars($id ?? ''); ?>"> <!-- Include hidden input for id -->
                <input type="hidden" name="page" value="<?php echo htmlspecialchars($page ?? ''); ?>"> <!-- Include hidden input for page -->
                <button type="submit" name="submitBtn">Submit</button>
            </form>
        </div>
    </div>
    <!-- =============SCRIPTS============= -->
    <script type="text/javascript" src="../../assets/js/main.js" charset="utf-8"></script>
    
    <!-- Modal Form Script for Product PRice and Product Stocks-->
    <script> 
        $(document).ready(function() {
            // Function to open the modal and populate it with data
            function openEditForm(id, price, stocks) {
                // Populate the modal fields with data
                $("#editPrice").val(price);
                $("#editStocks").val(stocks);
                $("#productId").val(id);

                // Show the modal
                $("#myModal").css("display", "block");
            }

            // Add a click event listener to the Edit buttons
                $(".edit_btn").click(function() {
                    // Get the data attributes
                    var id = $(this).data("id");
                    var price = $(this).data("price");
                    var stocks = $(this).data("stocks");
                    
                    // Call the function to open the modal with data
                    openEditForm(id, price, stocks);
            });

            // Close the modal when the close button is clicked
            $(".close").click(function() {
                $("#myModal").css("display", "none");
            });
        });
    </script>

    <!-- Upload Function Button and Sweet Alert Message Script -->
    <script>
        $(document).ready(function () {
            $(document).on('submit', '.uploadForm', function(e) {
                e.preventDefault();
                var formData = new FormData(this);

                $.ajax({
                    url: 'prod_imageUpload.php',
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
        function confirmDelete(productId) {
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
                        url: 'delete_productImage.php', // Your PHP file that will handle the deletion
                        type: 'POST',
                        data: { 'productId': productId },
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

    <!-- Image Preview Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const imageNames = document.querySelectorAll('.prodImage-name'); //Fetch image-name class in

            // Create the image preview element
            const imgPreview = document.createElement('img');
            imgPreview.className = 'prodImage-preview'; //Fecth style for image-preview
            document.body.appendChild(imgPreview);
            
            //Using event delegation
            document.addEventListener('mouseover', function(e) {
                if (e.target.classList.contains('prodImage-name')) {
                    const imgPath = e.target.getAttribute('data-img-path');
                    imgPreview.src = imgPath;
                    imgPreview.style.display = 'block';
                    updatePreviewPosition(e);
                }
            });

            document.addEventListener('mousemove', function(e) {
                if (e.target.classList.contains('prodImage-name')) {
                    updatePreviewPosition(e);
                }
            });

            document.addEventListener('mouseout', function(e) {
                if (e.target.classList.contains('prodImage-name')) {
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
</html