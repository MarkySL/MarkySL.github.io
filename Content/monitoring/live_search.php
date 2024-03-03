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

$search_term = $_GET['search_term'];
$page = isset($_POST['page']) ? intval($_POST['page']) : 1;
$recordsPerPage = isset($_POST['recordsPerPage']) ? intval($_POST['recordsPerPage']) : 10;
$offset = ($page - 1) * $recordsPerPage;

//This will help us search in all columns
$sql = "SELECT * FROM monitoring WHERE id LIKE '%$search_term%' OR client LIKE '%$search_term%' OR phone LIKE '%$search_term%' OR address LIKE '%$search_term%' OR petname LIKE '%$search_term%' OR petbday LIKE '%$search_term%' OR gender LIKE '%$search_term%' OR species LIKE '%$search_term%' OR breed LIKE '%$search_term%' OR colormarkings LIKE '%$search_term%'"; 

if (empty($search_term)) {
    //Apply the LIMIT and OFFSET only if search_term or the search bar is empty
    $sql .= " LIMIT $recordsPerPage OFFSET $offset"; 
}

$result = $con->query($sql);
$output = "";

if ($result) {
    if ($result->num_rows > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            // Generate table rows for the search result
            $output .= '<tr>
                <th>'.$row['id'].'</th>
                <td>'.$row['client'].'</td>
                <td>'.$row['phone'].'</td>
                <td>'.$row['address'].'</td>
                <td>'.$row['petname'].'</td>
                <td>'.$row['petbday'].'</td>
                <td>'.$row['gender'].'</td>
                <td>'.$row['species'].'</td>
                <td>'.$row['breed'].'</td>
                <td>'.$row['colormarkings'].'</td>
                <td>
                    <button class="update_btn"><a href="m_update.php?updateid='.$row['id'].'">Update</a></button>
                </td>
            </tr>';
        }
    } else {
        $output .= '<tr class="no-result"><td colspan="11">No records found</td></tr>';
    }
} echo $output;
?>
