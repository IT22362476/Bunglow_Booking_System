<?php
include("Mysqlconnection.php");

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $query = "DELETE FROM reservations WHERE invoicenumber = '$id'";
    mysqli_query($connection, $query);
    header("Location: Adminreservations.php");
}
?>
