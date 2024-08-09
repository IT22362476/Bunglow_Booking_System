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
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        
        h2 {
            text-align: center;
        }

        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }

        th {
            background-color: #f2f2f2;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .edit-btn, .remove-btn {
            display: inline-block;
            padding: 5px 10px;
            color: #fff;
            text-align: center;
            text-decoration: none;
            border-radius: 4px;
        }

        .edit-btn {
            background-color: #4CAF50; /* Green */
        }

        .remove-btn {
            background-color: #f44336; /* Red */
        }
    </style>
</head>
<body>
    <h2>Reservations</h2>
    <table>
        <tr>
            <th>Invoice Number</th>
            <th>Employee ID</th>
            <th>Check-in</th>
            <th>Check-out</th>
            <th>Persons</th>
            <th>Requests</th>
            <th>Edit</th>
            <th>Remove</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td><?php echo $row['invoicenumber']; ?></td>
                <td><?php echo $row['EmployeeID']; ?></td>
                <td><?php echo $row['checkin']; ?></td>
                <td><?php echo $row['checkout']; ?></td>
                <td><?php echo $row['persons']; ?></td>
                <td><?php echo $row['requests']; ?></td>
                <td><a href="Sadmineditreservations.php?id=<?php echo $row['invoicenumber']; ?>" class="edit-btn">Edit</a></td>
                <td><a href="Sadminremovereservations.php?id=<?php echo $row['invoicenumber']; ?>" class="remove-btn">Delete</a></td>
            </tr>
        <?php } ?>
    </table>
</body>
</html>
