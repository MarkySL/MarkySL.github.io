<?php
session_start();
//Set Default Timezone for Logging of Errors
date_default_timezone_set('Asia/Manila');

// Set error logging
ini_set('log_errors', 1);
ini_set('error_log', '../../assets/error/error.log');

// Turn off error reporting to the screen
ini_set('display_errors', 0);
include 'qr_config.php';

$monthFilter = $_GET['month'] ?? '';
$yearFilter = $_GET['year'] ?? '';

$whereClause = "";
if ($monthFilter && $yearFilter) {
    $whereClause = "WHERE MONTH(scan_time) = $monthFilter AND YEAR(scan_time) = $yearFilter";
}

$query = "SELECT id, DATE_FORMAT(scan_time, '%M %Y') as month_year, DATE_FORMAT(scan_time, '%d %M %Y %h:%i:%s %p') as formatted_time FROM qr_scans $whereClause ORDER BY scan_time ASC";

$stmt = $pdo->prepare($query);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($results);
?>
