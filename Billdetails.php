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

// Fetch the bill details
$sql = "SELECT pillowCases, bedSheets, towels, handserviette, duster, bathmate, apron, otherExpenses, totalBill 
        FROM bills WHERE invoicenumber = ?";
$stmt = $connection->prepare($sql);
$stmt->bind_param("i", $invoicenumber);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("No bill details found for the given invoice number.");
}

$row = $result->fetch_assoc();

// Fetch the prices for each linen item
function getLinenPrice($connection, $item) {
    $price = 0; // Initialize $price to avoid the unassigned variable error
    $priceQuery = "SELECT price FROM linencharges WHERE item = ?";
    $stmt = $connection->prepare($priceQuery);
    $stmt->bind_param("s", $item);
    $stmt->execute();
    $stmt->bind_result($price);
    $stmt->fetch();
    $stmt->close();
    return $price;
}

// Calculate the total price for each linen item, using isset to check if the value exists
$pillowCasesPrice = getLinenPrice($connection, 'pillowCase') * (isset($row['pillowCases']) ? $row['pillowCases'] : 0);
$bedSheetsPrice = getLinenPrice($connection, 'bedSheet') * (isset($row['bedSheets']) ? $row['bedSheets'] : 0);
$towelsPrice = getLinenPrice($connection, 'towel') * (isset($row['towels']) ? $row['towels'] : 0);
$handserviettePrice = getLinenPrice($connection, 'handserviette') * (isset($row['handserviette']) ? $row['handserviette'] : 0);
$dusterPrice = getLinenPrice($connection, 'duster') * (isset($row['duster']) ? $row['duster'] : 0);
$bathmatePrice = getLinenPrice($connection, 'bathmate') * (isset($row['bathmate']) ? $row['bathmate'] : 0);
$apronPrice = getLinenPrice($connection, 'apron') * (isset($row['apron']) ? $row['apron'] : 0);
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
                    <th>Quantity</th>
                    <th>Price Per Unit</th>
                    <th>Total Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Pillow Cases</td>
                    <td><?php echo htmlspecialchars(isset($row['pillowCases']) ? $row['pillowCases'] : 0); ?></td>
                    <td><?php echo htmlspecialchars(getLinenPrice($connection, 'pillowCase')); ?></td>
                    <td><?php echo htmlspecialchars($pillowCasesPrice); ?></td>
                </tr>
                <tr>
                    <td>Bed Sheets</td>
                    <td><?php echo htmlspecialchars(isset($row['bedSheets']) ? $row['bedSheets'] : 0); ?></td>
                    <td><?php echo htmlspecialchars(getLinenPrice($connection, 'bedSheet')); ?></td>
                    <td><?php echo htmlspecialchars($bedSheetsPrice); ?></td>
                </tr>
                <tr>
                    <td>Towels</td>
                    <td><?php echo htmlspecialchars(isset($row['towels']) ? $row['towels'] : 0); ?></td>
                    <td><?php echo htmlspecialchars(getLinenPrice($connection, 'towel')); ?></td>
                    <td><?php echo htmlspecialchars($towelsPrice); ?></td>
                </tr>
                <tr>
                    <td>Handserviette</td>
                    <td><?php echo htmlspecialchars(isset($row['handserviette']) ? $row['handserviette'] : 0); ?></td>
                    <td><?php echo htmlspecialchars(getLinenPrice($connection, 'handserviette')); ?></td>
                    <td><?php echo htmlspecialchars($handserviettePrice); ?></td>
                </tr>
                <tr>
                    <td>Duster</td>
                    <td><?php echo htmlspecialchars(isset($row['duster']) ? $row['duster'] : 0); ?></td>
                    <td><?php echo htmlspecialchars(getLinenPrice($connection, 'duster')); ?></td>
                    <td><?php echo htmlspecialchars($dusterPrice); ?></td>
                </tr>
                <tr>
                    <td>Bathmate</td>
                    <td><?php echo htmlspecialchars(isset($row['bathmate']) ? $row['bathmate'] : 0); ?></td>
                    <td><?php echo htmlspecialchars(getLinenPrice($connection, 'bathmate')); ?></td>
                    <td><?php echo htmlspecialchars($bathmatePrice); ?></td>
                </tr>
                <tr>
                    <td>Apron</td>
                    <td><?php echo htmlspecialchars(isset($row['apron']) ? $row['apron'] : 0); ?></td>
                    <td><?php echo htmlspecialchars(getLinenPrice($connection, 'apron')); ?></td>
                    <td><?php echo htmlspecialchars($apronPrice); ?></td>
                </tr>
                <tr>
                    <td>Other Expenses</td>
                    <td colspan="2"></td>
                    <td><?php echo htmlspecialchars($row['otherExpenses']); ?></td>
                </tr>
                <tr>
                    <th>Total Bill</th>
                    <th colspan="3"><?php echo htmlspecialchars($row['totalBill']); ?></th>
                </tr>
            </tbody>
        </table><br>
        <a href="Reservations.php" class="button">Back to Reservations</a>
    </div>
</body>

</html>

<?php
$stmt->close();
$connection->close();
?>
