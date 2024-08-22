<?php
session_start();
require 'Mysqlconnection.php'; // Include the MySQL connection file

if (!isset($_SESSION['EmployeeID'])) {
    header("Location: Login.php");
    exit();
}

if (isset($_GET['id'])) {
    $invoicenumber = mysqli_real_escape_string($connection, $_GET['id']);
    
    // Step 1: Move the reservation to the history table with status 'deleted'
    $sqlMoveToHistory = "
        INSERT INTO reservationhistories (invoicenumber,EmployeeID, checkin, checkout, persons, requests, status)
        SELECT invoicenumber,EmployeeID,checkin, checkout, persons, requests, 'deleted'
        FROM reservations
        WHERE invoicenumber='$invoicenumber'
    ";
    
    if (mysqli_query($connection, $sqlMoveToHistory)) {
        // Step 2: Delete the reservation from the reservations table
        $sqlDelete = "DELETE FROM reservations WHERE invoicenumber='$invoicenumber'";
        
        if (mysqli_query($connection, $sqlDelete)) {
            header("Location: reservations.php");
            exit();
        } else {
            die("Error deleting record: " . mysqli_error($connection));
        }
    } else {
        die("Error moving record to history: " . mysqli_error($connection));
    }
} else {
    header("Location: Reservations.php");
    exit();
}
?>