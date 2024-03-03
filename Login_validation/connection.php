<?php
//Declaring varable names to each data
$server = "localhost";
$username = "root";
$password = "";
$db = "new_petallies";

// Create Connection
$con = new mysqli($server,$username,$password,$db);

//Check Connection
if ($con->connect_error) {
    die("Connetion Error: ".$con->connect_error);
}
?>