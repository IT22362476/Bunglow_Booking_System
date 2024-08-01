<?php
require 'Mysqlconnection.php'; // Include your database connection file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the values from the form
    $roomCharge = $_POST['roomCharge'];
    $linenCharge = $_POST['linenCharge'];
    $otherExpenses = $_POST['otherExpenses'];
    $employeeID = $_POST['EmployeeID'];
    $invoicenumber = $_POST['invoicenumber'];

    // Validate the input
    if (is_numeric($roomCharge) && is_numeric($linenCharge) && is_numeric($otherExpenses)) {
        // Calculate the total bill
        $totalBill = $roomCharge + $linenCharge + $otherExpenses;

        // Insert the total bill into the bills table
        $stmt = $connection->prepare("INSERT INTO bills (invoicenumber, EmployeeID,roomCharge,linenCharge,otherExpenses,totalBill) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isdddd", $invoicenumber, $employeeID,$roomCharge,$linenCharge,$otherExpenses,$totalBill);

        if ($stmt->execute()) {
            echo "<h1>Total Bill: Rs$totalBill</h1>";
            echo "<h2>Bill successfully stored in the database.</h2>";
        } else {
            echo "<h1>Error: " . $stmt->error . "</h1>";
        }

        // Close the statement and connection
        $stmt->close();
        $connection->close();
    } else {
        echo "<h1>Please enter valid numbers for all charges.</h1>";
    }
} else {
    $employeeID = $_GET['EmployeeID'];
    $invoicenumber = $_GET['invoicenumber'];
    ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Calculate Bill</title>
    </head>
    <body>
        <form action="Calculatebill.php" method="post">
            <input type="hidden" name="EmployeeID" value="<?php echo htmlspecialchars($employeeID); ?>">
            <input type="hidden" name="invoicenumber" value="<?php echo htmlspecialchars($invoicenumber); ?>">
            <label for="roomCharge">Room Charge:</label>
            <input type="number" id="roomCharge" name="roomCharge" required>
            <br>
            <label for="linenCharge">Linen Charge:</label>
            <input type="number" id="linenCharge" name="linenCharge" required>
            <br>
            <label for="otherExpenses">Other Expenses:</label>
            <input type="number" id="otherExpenses" name="otherExpenses" required>
            <br>
            <input type="submit" value="Calculate Total Bill">
        </form>
    </body>
    </html>

    <?php
}
?>
