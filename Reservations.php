<?php
session_start();
require 'Mysqlconnection.php'; // Include the MySQL connection file

if (!isset($_SESSION['EmployeeID'])) {
    header("Location: Login.php");
    exit();
}

$EmployeeID = $_SESSION['EmployeeID'];
$sql = "SELECT * FROM reservations WHERE EmployeeID='$EmployeeID'";
$result = mysqli_query($connection, $sql);

if (!$result) {
    die("Query failed: " . mysqli_error($connection));
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservation Details</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .button {
            cursor: pointer;
            text-decoration: none;
            background-color: #007bff;
            padding: 0.4em;
            border: solid 1px black;
            border-radius: 0.5em;
            color: #f9f9f9;
        }

        .delete-button {
            background-color: #ed4239;
        }

        .edit-button {
            background-color: #28a745;
        }
    </style>
</head>

<body>
    <div class="container">
        <table>
            <thead>
                <tr>
                    <th>invoicenumber</th>
                    <th>checkin</th>
                    <th>checkout</th>
                    <th>persons</th>
                    <th>requests</th>
                    <th>Edit</th>
                    <th>Remove</th>
                    <th>Bill details</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['invoicenumber']); ?></td>
                        <td><?php echo htmlspecialchars($row['checkin']); ?></td>
                        <td><?php echo htmlspecialchars($row['checkout']); ?></td>
                        <td><?php echo htmlspecialchars($row['persons']); ?></td>
                        <td><?php echo htmlspecialchars($row['requests']); ?></td>
                        <td><a href="Reservationupdate.php?id=<?php echo $row['invoicenumber']; ?>"
                                class="button edit-button">Edit</a></td>
                        <td><a href="Reservationdelete.php?id=<?php echo $row['invoicenumber']; ?>"
                                class="button delete-button">Delete</a></td>
                        <td><a href="Billdetails.php?invoicenumber=<?php echo $row['invoicenumber'];?>"
                                class="button">Bill Details</a></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>

</html>