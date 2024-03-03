<?php
//Set Default Timezone for Logging of Errors
date_default_timezone_set('Asia/Manila');

// Set error logging
ini_set('log_errors', 1);
ini_set('error_log', '../../assets/error/error.log');

// Turn off error reporting to the screen
ini_set('display_errors', 0);

include '../../Login_validation/connection.php';

$search_term = $_GET['search_term'];
$page = isset($_POST['page']) ? intval($_POST['page']) : 1;
$recordsPerPage = isset($_POST['recordsPerPage']) ? intval($_POST['recordsPerPage']) : 10;
$offset = ($page - 1) * $recordsPerPage;

//This will help us search in specific column
$sql = "SELECT * FROM vet_profiles WHERE id LIKE '%$search_term%' OR staff_name LIKE '%$search_term%'"; 

if (empty($search_term)) {
    //Apply the LIMIT and OFFSET only if search_term or the search bar is empty
    $sql .= " LIMIT $recordsPerPage OFFSET $offset"; 
}

$result = $con->query($sql);
$output = "";

if ($result) {
    if ($result->num_rows > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $availabilityChecked = $row['availability'] == 1 ? 'checked' : '';

            // Image Preview and Upload Button
            if (!empty($row['staff_image'])) {
                $imageName = basename($row['staff_image']);
                // Make the image name clickable for deletion and add data attribute for thumbnail display
                $imageLink = "<a href='javascript:void(0);' class='staffImage-name' data-img-path='" . htmlspecialchars($row['staff_image']) . "' onclick='confirmDelete(".$row['id'].")'>" . htmlspecialchars($imageName) . "</a>";
            } else {
                // If no image is available, show upload form
                $imageLink = "<form action='staff_imageUpload.php' method='post' enctype='multipart/form-data' class='uploadForm'>
                    <input type='file' name='staffImage' required>
                    <input type='hidden' name='staffId' value='" . $row['id'] . "'>
                    <button type='submit'>Upload</button>
                </form>";
            }
            
            // Generate table rows for the search result with full functionalities
            $output .= "<tr>
                <td>".$row['id']."</td>
                <td>".htmlspecialchars($row['staff_name'])."</td>
                <td>".htmlspecialchars($row['position'])."</td>
                <td><label class='switch'><input type='checkbox' class='availability-toggle' data-id='".$row['id']."' data-availability='".$row['availability']."' ".$availabilityChecked."><span class='slider round'></span></label></td>
                <td>".$imageLink."</td>
            </tr>";
        }
    } else {
        $output .= '<tr class="no-result"><td colspan="5">No records found</td></tr>';
    }
} else {
    $output .= '<tr class="no-result"><td colspan="5">Error in fetching data</td></tr>';
}

echo $output;
?>
