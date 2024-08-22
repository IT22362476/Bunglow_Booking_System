<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Connection parameters
$hostname = "localhost";
$username = "root";
$password = "denuwanmackie";
$database = "banglow";

// Establish connection
$connection = mysqli_connect($hostname, $username, $password, $database);

// Check connection
if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}
?> 