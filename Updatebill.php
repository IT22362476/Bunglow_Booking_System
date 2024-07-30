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

        // Update the total bill in the bills table
        $stmt = $connection->prepare("UPDATE bills SET totalBill = ? WHERE EmployeeID = ? AND invoicenumber = ?");
        $stmt->bind_param("dsi", $totalBill, $employeeID, $invoicenumber);

        if ($stmt->execute()) {
            echo "<h1>Total Bill: $$totalBill</h1>";
            echo "<h2>Bill successfully updated in the database.</h2>";
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

    // Fetch the existing bill details
    $stmt = $connection->prepare("SELECT totalBill FROM bills WHERE EmployeeID = ? AND invoicenumber = ?");
    $stmt->bind_param("is", $employeeID, $invoicenumber);
    $stmt->execute();
    $stmt->bind_result($totalBill);
    $stmt->fetch();
    $stmt->close();
    ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Update Bill</title>
    </head>
    <body>
        <form action="Updatebill.php" method="post">
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
            <input type="submit" value="Update Total Bill">
        </form>
    </body>
    </html>

    <?php
}
?>
