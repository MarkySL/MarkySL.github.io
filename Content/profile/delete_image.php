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

if (isset($_POST['staffId'])) {
    $staffId = $_POST['staffId'];

    // Fetch the image path before deletion for file removal
    $query = "SELECT staff_image FROM vet_profiles WHERE id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param('i', $staffId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $imagePath = $row['staff_image'];

        // Attempt to delete the file
        if (!empty($imagePath) && file_exists($imagePath)) {
            unlink($imagePath);
        }

        // Now, remove the image path from the database
        $query = "UPDATE vet_profiles SET staff_image = NULL WHERE id = ?";
        $stmt = $con->prepare($query);
        $stmt->bind_param('i', $staffId);
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
