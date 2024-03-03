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

// Store the search term and current page in the session
if (isset($_GET['search_term'])) {
    $_SESSION['search_term'] = $_GET['search_term'];
}
if (isset($_POST['page'])) {
    $_SESSION['current_page'] = $_POST['page'];
}

$search_term = $_GET['search_term'];
$page = isset($_POST['page']) ? intval($_POST['page']) : 1;
$recordsPerPage = isset($_POST['recordsPerPage']) ? intval($_POST['recordsPerPage']) : 10;
$offset = ($page - 1) * $recordsPerPage;

//This will help us search in all columns
$sql = "SELECT * FROM inventory WHERE id LIKE '%$search_term%' OR product_name LIKE '%$search_term%' OR category LIKE '%$search_term%' OR productPrice LIKE '%$search_term%' OR productStocks LIKE '%$search_term%'"; 

if (empty($search_term)) {
    //Apply the LIMIT and OFFSET only if search_term or the search bar is empty
    $sql .= " LIMIT $recordsPerPage OFFSET $offset"; 
}

$result = $con->query($sql);
$output = "";

if ($result) {
    if ($result->num_rows > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            // Check if prod_image exists and display accordingly
            if (!empty($row['prod_image'])) {
                $imageName = basename($row['prod_image']);
                // Make the image name clickable for deletion and add data attribute for thumbnail display (class=prodImage-name and data-img-path)
                $imageLink = "<a href='javascript:void(0);' class='prodImage-name' data-img-path='" . htmlspecialchars($row['prod_image']) . "' onclick='confirmDelete(" . $row['id'] . ")'>" . htmlspecialchars($imageName) . "</a>";
            } else {
                // If no image is available, show upload form
                $imageLink = "<form action='prod_imageUpload.php' method='post' enctype='multipart/form-data' class='uploadForm'>
                <input type='file' name='productImage' required>
                <input type='hidden' name='productId' value='" . $row['id'] . "'>
                <button type='submit'>Upload</button>
                </form>";
            }
            
            // Generate table rows for the search result
            $output .= '<tr>
                <th>'.$row['id'].'</th>
                <td>'.$row['product_name'].'</td>
                <td>'.$row['category'].'</td>
                <td>'.$row['productPrice'].'</td>
                <td>'.$row['productStocks'].'</td>
                <td>
                    <button class="edit_btn" data-id="'.$row['id'].'" data-price="'.$row['productPrice'].'" data-stocks="'.$row['productStocks'].'">Edit</button>
                </td>
                <td>'.$imageLink.'</td>
            </tr>';
        }
    } else {
        $output .= '<tr class="no-result"><td colspan="7">No records found</td></tr>';
    }
} echo $output;
?>
