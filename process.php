<?php
include 'Mysqlconnection.php';

// Fetch the price data from linencharges table
$prices = [];
$sql_price = "SELECT item, price FROM linencharges";
$res_price = mysqli_query($connection, $sql_price);

if (mysqli_num_rows($res_price) > 0) {
    while ($row_price = mysqli_fetch_assoc($res_price)) {
        $prices[$row_price['item']] = $row_price['price'];
    }
}

$output = '';

if (isset($_POST['submit'])) {
    // Get the filtered dates
    $startDate = isset($_POST['startDate']) ? $_POST['startDate'] : '';
    $endDate = isset($_POST['endDate']) ? $_POST['endDate'] : '';

    // Create SQL query to filter records based on date range
    $sql = "SELECT * FROM bills";
    if (!empty($startDate) && !empty($endDate)) {
        $sql .= " WHERE billcreatedat BETWEEN '$startDate' AND '$endDate'";
    }
    $res = mysqli_query($connection, $sql);

    if (mysqli_num_rows($res) > 0) {
        // Start building the output (table) before any headers are sent
        $output .= '
                    <table class="table" bordered="1">
                        <tr>
                            <th>invoicenumber</th>
                            <th>EmployeeID</th>
                            <th>pillowCases (Total)</th>
                            <th>bedSheets (Total)</th>
                            <th>towels (Total)</th>
                            <th>handserviette (Total)</th>
                            <th>duster (Total)</th>
                            <th>bathmate (Total)</th>
                            <th>apron (Total)</th>
                            <th>otherExpenses</th>
                            <th>totalBill</th>
                        </tr>
        ';

        // Add table rows
        while ($row = mysqli_fetch_array($res)) {
            // Calculate total for each item
            $pillowCasesTotal = $row['pillowCases'] * $prices['pillowCase'];
            $bedSheetsTotal = $row['bedSheets'] * $prices['bedSheet'];
            $towelsTotal = $row['towels'] * $prices['towel'];
            $handservietteTotal = $row['handserviette'] * $prices['handserviette'];
            $dusterTotal = $row['duster'] * $prices['duster'];
            $bathmateTotal = $row['bathmate'] * $prices['bathmate'];
            $apronTotal = $row['apron'] * $prices['apron'];

            // Calculate total bill
            $totalBill = $pillowCasesTotal + $bedSheetsTotal + $towelsTotal + $handservietteTotal + $dusterTotal + $bathmateTotal + $apronTotal + $row['otherExpenses'];

            $output .= '
                        <tr>
                            <td>' . $row['invoicenumber'] . '</td>
                            <td>' . $row['EmployeeID'] . '</td>
                            <td>' . $pillowCasesTotal . '</td>
                            <td>' . $bedSheetsTotal . '</td>
                            <td>' . $towelsTotal . '</td>
                            <td>' . $handservietteTotal . '</td>
                            <td>' . $dusterTotal . '</td>
                            <td>' . $bathmateTotal . '</td>
                            <td>' . $apronTotal . '</td>
                            <td>' . $row['otherExpenses'] . '</td>
                            <td>' . $totalBill . '</td>
                        </tr>
            ';
        }

        // End table
        $output .= '</table>';

        // Set headers after the table content has been prepared but before any output is sent
        header('Content-Type: application/xls');
        header('Content-Disposition: attachment; filename=bills.xls');

        // Output the complete table
        echo $output;
    } else {
        echo 'No data found';
    }
}
?>
