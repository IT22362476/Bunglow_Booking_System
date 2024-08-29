<?php
session_start();
require 'Mysqlconnection.php';

// Check if the EmployeeID is set in the session
if (!isset($_SESSION['EmployeeID'])) {
    // Redirect to login page if not logged in
    header("Location: Login.php");
    exit();
}

// Retrieve the EmployeeID from the session
$employeeID = $_SESSION['EmployeeID'];

// Prepare and execute the SQL query to fetch reservation history for the logged-in user
$sql = "SELECT invoicenumber, checkin, checkout, persons, requests, status FROM reservationhistories WHERE EmployeeID = ?";
$stmt = $connection->prepare($sql);
$stmt->bind_param("s", $employeeID); // Use 's' for string type parameter
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
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row["invoicenumber"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["checkin"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["checkout"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["persons"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["requests"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["status"]) . "</td>";
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
