<?php
include 'Mysqlconnection.php';

// Initialize filter variables
$startDate = isset($_POST['startDate']) ? $_POST['startDate'] : '';
$endDate = isset($_POST['endDate']) ? $_POST['endDate'] : '';

// Fetch the price data from linencharges table
$prices = [];
$sql_price = "SELECT item, price FROM linencharges";
$res_price = mysqli_query($connection, $sql_price);

if (mysqli_num_rows($res_price) > 0) {
    while ($row_price = mysqli_fetch_assoc($res_price)) {
        $prices[$row_price['item']] = $row_price['price'];
    }
}

// Create SQL query to filter records based on date range
$sql = "SELECT * FROM bills";
if (!empty($startDate) && !empty($endDate)) {
    $sql .= " WHERE billcreatedat BETWEEN '$startDate' AND '$endDate'";
}
$res = mysqli_query($connection, $sql);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Charges Report</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }

        .container {
            padding: 20px;
            transition: margin-left 0.3s;
        }

        .menu-toggle {
            position: fixed;
            top: 10px;
            left: 10px;
            font-size: 24px;
            cursor: pointer;
            color: #235428;
            z-index: 1001;
        }

        .sidebar {
            width: 250px;
            background-color: #4CAF50;
            color: white;
            position: fixed;
            height: 100%;
            top: 0;
            left: -250px;
            overflow-y: auto;
            transition: left 0.3s;
            z-index: 1000;
        }

        .sidebar.active {
            left: 0;
        }

        .sidebar .nav-list {
            padding: 20px;
        }

        .sidebar .nav-items {
            list-style-type: none;
            margin: 20px 0;
        }

        .sidebar .nav-items a {
            text-decoration: none;
            color: white;
            display: block;
            padding: 10px;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        .sidebar .nav-items a:hover {
            background-color: #45a049;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            border: 2px solid #4CAF50;
            border-radius: 8px;
            overflow: hidden;
            margin: 20px 0;
        }

        th {
            background-color: #4CAF50;
            color: white;
            padding: 12px;
        }

        td {
            border: 1px solid #4CAF50;
            padding: 10px;
            text-align: left;
        }

        tr:nth-child(even) {
            background-color: #e8f5e9;
        }

        tr:nth-child(odd) {
            background-color: #f9f9f9;
        }

        input[type="text"], input[type="date"] {
            padding: 10px;
            border: 1px solid #4CAF50;
            border-radius: 4px;
            width: 200px;
            margin-left: 10px;
        }

        input[type="submit"], button[type="submit"] {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-left: 10px;
        }

        input[type="submit"]:hover, button[type="submit"]:hover {
            background-color: #45a049;
        }
    </style>
</head>

<body>
    <div class="menu-toggle" onclick="toggleSidebar()">&#9776;</div>

    <div class="sidebar" id="sidebar">
        <nav>
            <ul class="nav-list">
                <li class="nav-items"><a href="Superadmindashboard.php">Employee Details</a></li>
                <li class="nav-items"><a href="Superadminreservations.php">Reservation Details</a></li>
                <li class="nav-items"><a href="Supercalendaradmin.php">Calendar</a></li>
                <li class="nav-items"><a href="Superblocked.php">Blocked Days</a></li>
                <li class="nav-items"><a href="Superupdatetrack.php">Update Tracker</a></li>
                <li class="nav-items"><a href="Superexecutives.php">Executives</a></li>
                <li class="nav-items"><a href="Superlinen.php">Linen charges</a></li>
                <li class="nav-items"><a href="Viewhistories.php">View History</a></li>
                <li class="nav-items"><a href="Divisionemails.php">Emails Management</a></li>
                <li class="nav-items"><a href="Chargeshistory.php">Charges History</a></li>
            </ul>
        </nav>
    </div>

    <div class="container" id="main-container">
        <div class="card mt-2 mb-2">
            <div class="card header text-center">
                <h4>Charges Report</h4>
                <form action="Chargeshistory.php" method="post" class="form-inline">
                    <label for="startDate" class="mr-2">Start Date:</label>
                    <input type="date" name="startDate" value="<?php echo $startDate; ?>" class="form-control mr-2">
                    <label for="endDate" class="mr-2">End Date:</label>
                    <input type="date" name="endDate" value="<?php echo $endDate; ?>" class="form-control mr-2">
                    <button type="submit" class="btn btn-primary">Filter</button>
                </form>
                <form action="process.php" method="post" class="mt-2">
                    <!-- Pass the filtered dates to the download process -->
                    <input type="hidden" name="startDate" value="<?php echo $startDate; ?>">
                    <input type="hidden" name="endDate" value="<?php echo $endDate; ?>">
                    <button type="submit" name="submit" class="btn btn-primary float-right">Download</button>
                </form>
            </div>
        </div>
        <div class="card body">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th scope="col">Invoice Number</th>
                        <th scope="col">Employee ID</th>
                        <th scope="col">Created At</th>
                        <th scope="col">Pillow Cases</th>
                        <th scope="col">Bed Sheets</th>
                        <th scope="col">Towels</th>
                        <th scope="col">Hand Serviette</th>
                        <th scope="col">Duster</th>
                        <th scope="col">Bathmate</th>
                        <th scope="col">Apron</th>
                        <th scope="col">Other Expenses</th>
                        <th scope="col">Total Bill</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (mysqli_num_rows($res) > 0) {
                        while ($row = mysqli_fetch_array($res)) {
                            // Calculate total for each item
                            $pillowCasesTotal = $row['pillowCases'] * $prices['pillowCase'];
                            $bedSheetsTotal = $row['bedSheets'] * $prices['bedSheet'];
                            $towelsTotal = $row['towels'] * $prices['towel'];
                            $handservietteTotal = $row['handserviette'] * $prices['handserviette'];
                            $dusterTotal = $row['duster'] * $prices['duster'];
                            $bathmateTotal = $row['bathmate'] * $prices['bathmate'];
                            $apronTotal = $row['apron'] * $prices['apron'];

                            // Format billcreatedat in 'Y-M-D' format
                            $formattedDate = date('Y-m-d', strtotime($row['billcreatedat']));

                            // Calculate total bill
                            $totalBill = $pillowCasesTotal + $bedSheetsTotal + $towelsTotal + $handservietteTotal + $dusterTotal + $bathmateTotal + $apronTotal + $row['otherExpenses'];
                            ?>
                            <tr>
                                <td><?php echo $row['invoicenumber']; ?></td>
                                <td><?php echo $row['EmployeeID']; ?></td>
                                <td><?php echo $formattedDate; ?></td>
                                <td><?php echo $pillowCasesTotal; ?></td>
                                <td><?php echo $bedSheetsTotal; ?></td>
                                <td><?php echo $towelsTotal; ?></td>
                                <td><?php echo $handservietteTotal; ?></td>
                                <td><?php echo $dusterTotal; ?></td>
                                <td><?php echo $bathmateTotal; ?></td>
                                <td><?php echo $apronTotal; ?></td>
                                <td><?php echo $row['otherExpenses']; ?></td>
                                <td><?php echo $totalBill; ?></td>
                            </tr>
                            <?php
                        }
                    } else {
                        echo "<tr><td colspan='12'>No records found for the selected date range.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            document.getElementById("sidebar").classList.toggle("active");
            document.getElementById("main-container").style.marginLeft = document.getElementById("sidebar").classList.contains("active") ? "250px" : "0";
        }
    </script>

</body>

</html>
