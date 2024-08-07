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

// Function to calculate the difference in days between two dates
function dateDiffInDays($date1, $date2) {
    $datetime1 = new DateTime($date1);
    $datetime2 = new DateTime($date2);
    $interval = $datetime1->diff($datetime2);
    return $interval->days;
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

        .disabled-button {
            background-color: #cccccc;
            cursor: not-allowed;
        }
    </style>
</head>

<body>
    <div class="container">
        <table>
            <thead>
                <tr>
                    <th>Invoice Number</th>
                    <th>Check-in</th>
                    <th>Check-out</th>
                    <th>Persons</th>
                    <th>Requests</th>
                    <th>Edit</th>
                    <th>Remove</th>
                    <th>Bill Details</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $currentDate = date('Y-m-d');
                while ($row = mysqli_fetch_assoc($result)) {
                    $checkinDate = $row['checkin'];
                    $daysUntilCheckin = dateDiffInDays($currentDate, $checkinDate);
                    $isEditableOrDeletable = $daysUntilCheckin >= 14;
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['invoicenumber']); ?></td>
                        <td><?php echo htmlspecialchars($checkinDate); ?></td>
                        <td><?php echo htmlspecialchars($row['checkout']); ?></td>
                        <td><?php echo htmlspecialchars($row['persons']); ?></td>
                        <td><?php echo htmlspecialchars($row['requests']); ?></td>
                        <td>
                            <?php if ($isEditableOrDeletable) { ?>
                                <a href="Reservationupdate.php?id=<?php echo $row['invoicenumber']; ?>" class="button edit-button">Edit</a>
                            <?php } else { ?>
                                <span class="button disabled-button">Edit</span>
                            <?php } ?>
                        </td>
                        <td>
                            <?php if ($isEditableOrDeletable) { ?>
                                <a href="Reservationdelete.php?id=<?php echo $row['invoicenumber']; ?>" class="button delete-button">Delete</a>
                            <?php } else { ?>
                                <span class="button disabled-button">Delete</span>
                            <?php } ?>
                        </td>
                        <td><a href="Billdetails.php?invoicenumber=<?php echo $row['invoicenumber']; ?>" class="button">Bill Details</a></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>

</html>
