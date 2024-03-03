<?php
//Set Default Timezone for Logging of Errors
date_default_timezone_set('Asia/Manila');

// Set error logging
ini_set('log_errors', 1);
ini_set('error_log', '../../assets/error/error.log');

// Turn off error reporting to the screen
ini_set('display_errors', 0);

include '../../Login_validation/connection.php';

if (isset($_POST['labId'])) {
    $labId = $_POST['labId'];

    // Fetch the image path before deletion for file removal
    $query = "SELECT lab_image FROM follow_ups WHERE id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param('i', $labId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $imagePath = $row['lab_image'];

        // Attempt to delete the file
        if (!empty($imagePath) && file_exists($imagePath)) {
            unlink($imagePath);
        }

        // Now, remove the image path from the database
        $query = "UPDATE follow_ups SET lab_image = NULL WHERE id = ?";
        $stmt = $con->prepare($query);
        $stmt->bind_param('i', $labId);
        $stmt->execute();

        echo "Image deleted successfully";
    } else {
        echo "Image not found";
    }
    $stmt->close();
} else {
    echo "Invalid request";
}
?>
