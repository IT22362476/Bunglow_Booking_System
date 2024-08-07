<?php
include("Mysqlconnection.php");

// Fetch reservation data from the database
$query = "SELECT * FROM reservations";
$result = mysqli_query($connection, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Reservations</title>
    <link rel="stylesheet" type="text/css" href="css/Booking.css">
</head>
<body>
    <h2>Reservations</h2>
    <table border="1">
        <tr>
            <th>Invoice Number</th>
            <th>Employee ID</th>
            <th>Check-in</th>
            <th>Check-out</th>
            <th>Persons</th>
            <th>Requests</th>
            <th>Remove</th>
            <th>Edit</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td><?php echo $row['invoicenumber']; ?></td>
                <td><?php echo $row['EmployeeID']; ?></td>
                <td><?php echo $row['checkin']; ?></td>
                <td><?php echo $row['checkout']; ?></td>
                <td><?php echo $row['persons']; ?></td>
                <td><?php echo $row['requests']; ?></td>
                <td><a href="Sadminremovereservations.php?id=<?php echo $row['invoicenumber']; ?>">Remove</a></td>
                <td><a href="Sadmineditreservations.php?id=<?php echo $row['invoicenumber']; ?>">Edit</a></td>
            </tr>
        <?php } ?>
    </table>
</body>
</html>
