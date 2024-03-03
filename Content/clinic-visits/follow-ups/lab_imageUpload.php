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

header('Content-Type: application/json'); // Set the content type to application/json

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if file was uploaded without errors
    if (isset($_FILES["labImage"]) && $_FILES["labImage"]["error"] == 0) {
        $labId = $_POST['labId']; // Retrieve the record ID from the form
        $fileName = $_FILES["labImage"]["name"];
        $fileTmpName = $_FILES["labImage"]["tmp_name"];
        $fileSize = $_FILES["labImage"]["size"];
        $fileType = $_FILES["labImage"]["type"];

        // Specify the directory where the file is going to be placed
        $uploadDirectory = "clientfiles/";

        // Create a unique filename to prevent overwriting
        $labFileName = uniqid()."_".$fileName;

        // Check file size (e.g., max 5MB)
        if ($fileSize > 5000000) {
            echo json_encode(['success' => false, 'message' => 'File size is too large.']);
            exit;
        }

        // Verify the file type (e.g., only jpg, jpeg, png)
        $allowedTypes = ['image/jpg', 'image/jpeg', 'image/png'];
        if (!in_array($fileType, $allowedTypes)) {
            echo json_encode(['success' => false, 'message' => 'Only JPG, JPEG & PNG files are allowed.']);
            exit;
        }

        // Attempt to move the uploaded file to its new place
        if (move_uploaded_file($fileTmpName, $uploadDirectory . $labFileName)) {
            // Update database with new file path
            $filePath = $uploadDirectory . $labFileName;
            
            // Prepare SQL statement to update lab image path
            $stmt = $con->prepare("UPDATE follow_ups SET lab_image = ? WHERE id = ?");
            $stmt->bind_param('si', $filePath, $labId);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                echo json_encode([
                    'success' => true, 
                    'message' => 'File uploaded successfully.', 
                    'imageName' => $labFileName, // The name of the uploaded file
                ]);
            } else {
                echo json_encode([
                    'success' => false, 
                    'message' => 'Failed to update the database.'
                ]);
            }

            $stmt->close();
        } else {
            echo json_encode([
                'success' => false, 
                'message' => 'Failed to upload file.'
            ]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $_FILES["labImage"]["error"]]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
?>
