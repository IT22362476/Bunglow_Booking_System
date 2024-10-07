<?php
session_start();
require 'Mysqlconnection.php'; // Include the MySQL connection file

if (!isset($_SESSION['EmployeeID'])) {
    header("Location: Login.php");
    exit();
}

$EmployeeID = $_SESSION['EmployeeID']; // Get the EmployeeID from the session

if (isset($_GET['id'])) {
    $invoicenumber = mysqli_real_escape_string($connection, $_GET['id']);
    
    // Step 1: Move the reservation to the history table with status 'deleted' and track the user in 'editedby'
    $sqlMoveToHistory = "
        INSERT INTO reservationhistories (invoicenumber, EmployeeID, checkin, checkout, persons, requests, status, editedby)
        SELECT invoicenumber, EmployeeID, checkin, checkout, persons, requests, 'deleted', '$EmployeeID'
        FROM reservations
        WHERE invoicenumber='$invoicenumber'
    ";
    
    if (mysqli_query($connection, $sqlMoveToHistory)) {
        // Step 2: Delete the reservation from the reservations table
        $sqlDelete = "DELETE FROM reservations WHERE invoicenumber='$invoicenumber'";
        
        if (mysqli_query($connection, $sqlDelete)) {
            // Redirect to reservations page after successful deletion
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
