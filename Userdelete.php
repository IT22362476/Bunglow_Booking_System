<?php
session_start();
require 'Mysqlconnection.php'; // Include your database connection file

if (isset($_GET['UserID'])) {
    $UserID = $_GET['UserID'];

    // Validate the input to prevent SQL injection
    $UserID = mysqli_real_escape_string($connection, $UserID);

    // Prepare and execute the delete query
    $sql = "DELETE FROM users WHERE UserID='$UserID'";
    if (mysqli_query($connection, $sql)) {
        // Redirect to the page with the table
        header("Location: Admindashboard.php?success=Record deleted successfully");
    } else {
        echo "Error: " . mysqli_error($connection);
    }
} else {
    header("Location: Admindashboard.php?error=No UserID specified");
}

// Close the database connection
mysqli_close($connection);
?>
