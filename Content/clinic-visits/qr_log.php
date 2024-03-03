<?php
//Set Default Timezone for Logging of Errors
date_default_timezone_set('Asia/Manila');

// Set error logging
ini_set('log_errors', 1);
ini_set('error_log', '../../assets/error/error.log');

// Turn off error reporting to the screen
ini_set('display_errors', 0);

require __DIR__. '/../../vendor/autoload.php';


use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;


$qrCodeData = 'http://localhost/ReworkingCapstone/PetAlliesWeb/Content/clinic-visits/qr_scan.php?data=logscan';
$qrCode = QrCode::create($qrCodeData);

$writer = new PngWriter();
$output = $writer->write($qrCode);

header('Content-Type: image/png');
echo $output->getString();
?>




