<?php
session_start();
require 'Mysqlconnection.php';

// Assuming EmployeeID is stored in session after user logs in
$employeeID = $_SESSION['EmployeeID'];

$sql = "SELECT invoicenumber,EmployeeID, checkin, checkout, persons, requests, status FROM reservationhistories WHERE EmployeeID = ?";
$stmt = $connection->prepare($sql);
$stmt->bind_param("i", $employeeID);
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html>
<head>
    <title>Reservation History</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
    </style>
</head>
<body>

<h2>Reservation History</h2>

<table>
    <tr>
        <th>Invoice Number</th>
        <th>Check-in</th>
        <th>Check-out</th>
        <th>Persons</th>
        <th>Requests</th>
        <th>Status</th>
    </tr>
    <?php
    // Fetch data and display it in the table
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row["invoicenumber"] . "</td>";
            echo "<td>" . $row["checkin"] . "</td>";
            echo "<td>" . $row["checkout"] . "</td>";
            echo "<td>" . $row["persons"] . "</td>";
            echo "<td>" . $row["requests"] . "</td>";
            echo "<td>" . $row["status"] . "</td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='6'>No reservations found</td></tr>";
    }
    ?>
</table>

</body>
</html>

<?php
$stmt->close();
$connection->close();
?>
