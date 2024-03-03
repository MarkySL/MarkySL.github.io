<?php
session_start();
//Set Default Timezone for Logging of Errors
date_default_timezone_set('Asia/Manila');

// Set error logging
ini_set('log_errors', 1);
ini_set('error_log', '../../assets/error/error.log');

// Turn off error reporting to the screen
ini_set('display_errors', 0);

include 'qr_config.php'; // Database connection

$currentTime = time();
$message = '';

// Using SESSION to track the last scan time and rate limit.
if (isset($_SESSION['lastScanTime'])) {
    $timeSinceLastScan = $currentTime - $_SESSION['lastScanTime'];

    if ($timeSinceLastScan < 600) {  // e.g., 600 seconds = 10 minutes
        $message = 'wait';
    }
}

$_SESSION['lastScanTime'] = $currentTime;

// Using COOKIE to track the last scan time for longer durations.
if (isset($_COOKIE['lastScanTime'])) {
    $timeSinceLastScanCookie = $currentTime - intval($_COOKIE['lastScanTime']);
    if ($timeSinceLastScanCookie < 86400) {  // e.g., 86400 seconds = 24 hours
        $message = 'daily_limit';
    }
}
setcookie("lastScanTime", time(), time() + 86400); // Set a cookie that expires in 1 day

// QR scan logic
if (!$message) {

    if (isset($_GET['data']) && $_GET['data'] == 'logscan') {
        
        // Insert the scan record into the database
        $query = "INSERT INTO qr_scans (scan_time) VALUES (NOW())";
        $stmt = $pdo->prepare($query);
        $stmt->execute();

        $message = 'success';
    } else {
        $message = 'error';
    }

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pet Allies Animal Clinic Visit</title>
    <!-- Include SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- Include SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        <?php if ($message == 'success'): ?>
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: 'QR code scan recorded successfully',
                confirmButtonText: 'Okay'
            });
        <?php elseif ($message == 'error'): ?>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Invalid QR code data',
                confirmButtonText: 'Try Again'
            });
        <?php elseif ($message == 'wait'): ?>
            Swal.fire({
                icon: 'warning',
                title: 'Please Wait',
                text: 'Please wait before scanning again.',
                confirmButtonText: 'Okay'
            });
        <?php elseif ($message == 'daily_limit'): ?>
            Swal.fire({
                icon: 'warning',
                title: 'Limit Reached',
                text: 'You already scanned today.',
                confirmButtonText: 'Okay'
            });
        <?php endif; ?>
    });
</script>

</body>
</html>
