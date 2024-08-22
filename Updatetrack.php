<?php
// Start the session to check if the admin is logged in
session_start();

// Include database connection file
require 'Mysqlconnection.php';

// Check if the admin is logged in
if (!isset($_SESSION['EmployeeID'])) {
    header("Location: login.php");
    exit();
}

// Retrieve update logs from the database, format update_date to only show time
$query = "SELECT EmployeeID, invoicenumber, update_count, DATE_FORMAT(update_date, '%H:%i:%s') as update_time FROM update_logs";
$result = $connection->query($query);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Logs</title>
    <link rel="stylesheet" href="styles.css"> <!-- Add your stylesheet link here -->
</head>
<body>
    <div class="container">
        <h1>Update Logs</h1>
        <table border="1">
            <thead>
                <tr>
                    <th>Employee ID</th>
                    <th>Invoice Number</th>
                    <th>Update Count</th>
                    <th>Update Time</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Check if any records were found
                if ($result->num_rows > 0) {
                    // Loop through the result and display the records
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['EmployeeID'] . "</td>";
                        echo "<td>" . $row['invoicenumber'] . "</td>";
                        echo "<td>" . $row['update_count'] . "</td>";
                        echo "<td>" . $row['update_time'] . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No update logs found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html> 