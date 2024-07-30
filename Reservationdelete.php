<?php
session_start();
require 'Mysqlconnection.php'; // Include the MySQL connection file

if (!isset($_SESSION['EmployeeID'])) {
    header("Location: Login.php");
    exit();
}

if (isset($_GET['id'])) {
    $invoicenumber = $_GET['id'];
    $sql = "DELETE FROM reservations WHERE invoicenumber='$invoicenumber'";
    if (mysqli_query($connection, $sql)) {
        header("Location: reservations.php");
        exit();
    } else {
        die("Error deleting record: " . mysqli_error($connection));
    }
} else {
    header("Location: reservations.php");
    exit();
}
?>
