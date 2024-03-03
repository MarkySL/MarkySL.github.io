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
    if (isset($_FILES["staffImage"]) && $_FILES["staffImage"]["error"] == 0) {
        $staffId = $_POST['staffId']; // Retrieve the staff ID
        $fileName = $_FILES["staffImage"]["name"];
        $fileTmpName = $_FILES["staffImage"]["tmp_name"];
        $fileSize = $_FILES["staffImage"]["size"];
        $fileType = $_FILES["staffImage"]["type"];

        // Specify the directory where the file is going to be placed
        $uploadDirectory = "uploads/";

        // Create a unique filename to prevent overwriting
        $newFileName =/* $staffId . "_" . time() . "_" .*/ $fileName;

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
        if (move_uploaded_file($fileTmpName, $uploadDirectory . $newFileName)) {
            // Update database with new file path
            $filePath = $uploadDirectory . $newFileName;
            
            // Prepare SQL statement to update staff image path
            // Assuming you have a column for image path in vet_profiles table
            $stmt = $con->prepare("UPDATE vet_profiles SET staff_image = ? WHERE id = ?");
            $stmt->bind_param('si', $filePath, $staffId);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                echo json_encode([
                    'success' => true, 
                    'message' => 'File uploaded successfully.', 
                    'imageName' => $newFileName, // Ensure this is just the file name or a format you wish to display
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
        echo "Error: " . $_FILES["staffImage"]["error"];
    }
} else {
    echo "Invalid request.";
}
?>
