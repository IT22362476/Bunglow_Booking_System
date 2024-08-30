<?php
session_start();
require 'Mysqlconnection.php'; // Include the MySQL connection file

if (!isset($_SESSION['EmployeeID'])) {
    header("Location: Login.php");
    exit();
}

if (!isset($_GET['invoicenumber'])) {
    die("Invoice number is missing.");
}

$invoicenumber = $_GET['invoicenumber'];
$sql = "SELECT linenCharge, otherExpenses, totalBill FROM bills WHERE invoicenumber='$invoicenumber'";
$result = mysqli_query($connection, $sql);

if (!$result) {
    die("Query failed: " . mysqli_error($connection));
}

$row = mysqli_fetch_assoc($result);

if (!$row) {
    die("No bill details found for the given invoice number.");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bill Details</title>
    <style>
        table {
            width: 50%;
            margin: 0 auto;
            border-collapse: collapse;
            margin-top: 20px;
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

        .button {
            cursor: pointer;
            text-decoration: none;
            background-color: #007bff;
            padding: 0.4em;
            border: solid 1px black;
            border-radius: 0.5em;
            color: #f9f9f9;
        }

        .button:hover {
            background-color: #0056b3;
        }

        .container {
            text-align: center;
            margin-top: 50px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Bill Details for Invoice Number: <?php echo htmlspecialchars($invoicenumber); ?></h2>
        <table>
            <thead>
                <tr>
                    <th>Charge Type</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Linen Charge</td>
                    <td><?php echo htmlspecialchars($row['linenCharge']); ?></td>
                </tr>
                <tr>
                    <td>Other Expenses</td>
                    <td><?php echo htmlspecialchars($row['otherExpenses']); ?></td>
                </tr>
                <tr>
                    <th>Total Bill</th>
                    <th><?php echo htmlspecialchars($row['totalBill']); ?></th>
                </tr>
            </tbody>
        </table><br>
        <a href="Reservations.php" class="button">Back to Reservations</a>
    </div>
</body>

</html>
