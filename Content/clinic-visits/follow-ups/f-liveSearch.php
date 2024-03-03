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

$search_term = $_GET['search_term'] ?? '';
//Variable for reverting the table back to its original count
$page_number = $_GET['page_number'] ?? 1;
$records_per_page = $_GET['records_per_page'] ?? 10;

$offset = ($page_number - 1) * $records_per_page;

// Use prepared statements for secure queries
if ($search_term != '') {
    $search_term_like = "%{$search_term}%";
    $sql = "SELECT * FROM follow_ups WHERE 
    id LIKE ? OR 
    client LIKE ? OR 
    petname LIKE ? OR 
    weight LIKE ? OR 
    temperature LIKE ? OR 
    complaints LIKE ? OR 
    treatment LIKE ? OR 
    datevisit LIKE ? OR 
    followup LIKE ? OR 
    lab_image LIKE ?";

    $stmt = $con->prepare($sql);
    // Bind the search term for each condition in the WHERE clause
    $stmt->bind_param('ssssssssss', $search_term_like, $search_term_like, $search_term_like, $search_term_like, $search_term_like, $search_term_like, $search_term_like, $search_term_like, $search_term_like, $search_term_like);
} else {
    $sql = "SELECT * FROM follow_ups LIMIT ?, ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param('ii', $offset, $records_per_page);
}

$stmt->execute();
$result = $stmt->get_result();
$output = "";

if ($result) {
    if ($result->num_rows > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            // Start of Upload Button
            // Check if lab_image exists and display accordingly
            if (!empty($row['lab_image'])) {
                $imageName = basename($row['lab_image']);
                // Make the image name clickable for deletion or download 
                $imageLink = "<a href='javascript:void(0);'
                class='labImage-name'
                data-img-path='" . htmlspecialchars($row['lab_image']) . "'
                data-lab-id='" . htmlspecialchars($row['id']) . "'
                onclick='handleImageAction(\"" . htmlspecialchars($row['lab_image']) . "\", \"" . htmlspecialchars($row['id']) . "\")'>" . htmlspecialchars($imageName) . "</a>";
            } else {
                // If no image is available, show upload form 
                $imageLink = "<form action='lab_imageUpload.php' method='post' enctype='multipart/form-data' class='uploadForm'>
                        <input type='file' name='labImage' id='fileInput_" . $row['id'] . "' required style='display: none;'>
                        <label for='fileInput_" . $row['id'] . "' class='upload-button'>Upload Lab Image</label>
                        <input type='hidden' name='labId' value='" . $row['id'] . "'>
                    </form>";
                
            }
            // End of Upload Button
            // Generate table rows for the search result
            $output .= '<tr>
                <td>'.$row['id'].'</td>
                <td>'.$row['client'].'</td>
                <td>'.$row['petname'].'</td>
                <td>'.$row['weight'].'</td>
                <td>'.$row['temperature'].'</td>
                <td>'.$row['complaints'].'</td>
                <td>'.$row['treatment'].'</td>
                <td>'.$row['datevisit'].'</td>
                <td>'.$row['followup'].'</td>
                <td>
                    <a href="f_update.php?updateid='.$row['id'].'"><button class="update_btn">Update</button></a>
                </td>
                <td>'.$imageLink.'</td>
            </tr>';
        }
    } else {
        $output .= '<tr class="no-result"><td colspan="11">No records found</td></tr>';
    }
} 
echo $output;
?>
