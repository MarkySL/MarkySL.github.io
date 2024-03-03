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

// Initialize an array for the query parameters
$query_params = [];

// Check if a specific category has been requested and prepare the SQL statement accordingly
if (isset($_GET['category'])) {
    // Prepare SQL statement with a category filter
    $stmt = $con->prepare("SELECT id, product_name, category, productPrice, productStocks, prod_image FROM inventory WHERE category = ?");
    // Add the category to the query parameters
    $query_params[] = $_GET['category'];
} else {
    // Prepare SQL statement without a category filter
    $stmt = $con->prepare("SELECT id, product_name, category, productPrice, productStocks, prod_image FROM inventory");
}

// Bind parameters if query_params is not empty
if (!empty($query_params)) {
    // Assuming 's' as a type for a string. Adjust the type based on the actual data type of 'category' in your database.
    $stmt->bind_param('s', $query_params[0]);
}

// Execute the query
$stmt->execute();

// Bind the results to variables
$stmt->bind_result($id, $prod_name, $category, $prod_price, $prod_stocks, $prod_image);

$data = [];

// Fetch the results into the array
while ($stmt->fetch()) {
    $data[] = [
        'id' => $id,
        'product_name' => $prod_name,
        'category' => $category,
        'productPrice' => $prod_price,
        'productStocks' => $prod_stocks,
        'prod_image' => $prod_image
    ];
}

// Set header to return JSON
header('Content-Type: application/json');

// Echo data as JSON
echo json_encode($data);

// Close statement
$stmt->close();

// Close connection
$con->close();
?>
    

