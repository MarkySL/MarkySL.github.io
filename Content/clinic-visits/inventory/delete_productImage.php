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

if (isset($_POST['productId'])) {
    $productId = $_POST['productId'];

    // Fetch the image path before deletion for file removal
    $query = "SELECT prod_image FROM inventory WHERE id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param('i', $productId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $imagePath = $row['prod_image'];

        // Attempt to delete the file
        if (!empty($imagePath) && file_exists($imagePath)) {
            unlink($imagePath);
        }

        // Now, remove the image path from the database
        $query = "UPDATE inventory SET prod_image = NULL WHERE id = ?";
        $stmt = $con->prepare($query);
        $stmt->bind_param('i', $productId);
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
