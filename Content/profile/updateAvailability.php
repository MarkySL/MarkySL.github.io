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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $staffId = $_POST['id'] ?? null;
    $newAvailability = $_POST['availability'] ?? null;

    if ($staffId !== null && $newAvailability !== null) {
        $stmt = $con->prepare("UPDATE vet_profiles SET availability = ? WHERE id = ?");
        $stmt->bind_param("ii", $newAvailability, $staffId);

        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => $stmt->error]);
        }

        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid request']);
    }
    $con->close();
}
?>


