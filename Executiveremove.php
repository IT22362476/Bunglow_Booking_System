<?php
session_start();
require 'Mysqlconnection.php'; // Include the MySQL connection file

if (!isset($_SESSION['EmployeeID'])) {
    header("Location: Login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $employeeID = $_POST['EmployeeID'];

    // Delete the executive from the executives table
    $sql = "DELETE FROM executives WHERE EmployeeID = '$employeeID'";
    if (mysqli_query($connection, $sql)) {
        echo '<script>
            alert("Executive removed successfully.");
            window.location.href = "Executives.php";
        </script>';
    } else {
        die("Error removing executive: " . mysqli_error($connection));
    }
} else {
    header("Location: Executives.php");
    exit();
}
?>