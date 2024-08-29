<?php
require 'Mysqlconnection.php'; // Include your database connection file
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;

function sendBillEmail($employeeID, $invoicenumber, $linenCharge, $otherExpenses, $totalBill) {
    global $connection;

    // Fetch employee email
    $stmt = $connection->prepare("SELECT Email FROM executives WHERE EmployeeID = ?");
    $stmt->bind_param("s", $employeeID);
    $stmt->execute();
    $stmt->bind_result($email);
    $stmt->fetch();
    $stmt->close();

    if ($email) {
        $mail = new PHPMailer;
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'denuwansathsara0412@gmail.com';
        $mail->Password = 'boaa moki kmax yzyz';
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;

        $mail->setFrom('denuwansathsara0412@gmail.com', 'Denuwan');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Your Bill Details';
        $mail->Body    = "
            <h1>Bill Details</h1>
            <p>Invoice Number: $invoicenumber</p>
            <p>Linen Charge: Rs$linenCharge</p>
            <p>Other Expenses: Rs$otherExpenses</p>
            <p>Total Bill: Rs$totalBill</p>
        ";

        if (!$mail->send()) {
            echo "<h1>Email could not be sent. Mailer Error: " . $mail->ErrorInfo . "</h1>";
        } else {
            echo "<h2>Email has been sent to the employee.</h2>";
        }
    } else {
        echo "<h1>Employee email not found.</h1>";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the values from the form
    $pillowCases = $_POST['pillowCases'];
    $bedSheets = $_POST['bedSheets'];
    $towels = $_POST['towels'];
    $otherExpenses = $_POST['otherExpenses'];
    $employeeID = $_POST['EmployeeID'];
    $invoicenumber = $_POST['invoicenumber'];

    // Validate the input
    if (is_numeric($pillowCases) && is_numeric($bedSheets) && is_numeric($towels) && is_numeric($otherExpenses)) {
        // Fetch the prices for each linen item
        $priceQuery = "SELECT price FROM linencharges WHERE item = ?";
        $stmt = $connection->prepare($priceQuery);

        // Calculate the linen charge
        $linenCharge = 0;

        $stmt->bind_param("s", $item);

        $item = 'pillowCase';
        $stmt->execute();
        $stmt->bind_result($price);
        $stmt->fetch();
        $linenCharge += $pillowCases * $price;

        $item = 'bedSheet';
        $stmt->execute();
        $stmt->bind_result($price);
        $stmt->fetch();
        $linenCharge += $bedSheets * $price;

        $item = 'towel';
        $stmt->execute();
        $stmt->bind_result($price);
        $stmt->fetch();
        $linenCharge += $towels * $price;

        $stmt->close();

        // Check if invoicenumber already exists
        $checkStmt = $connection->prepare("SELECT COUNT(*) FROM bills WHERE invoicenumber = ?");
        $checkStmt->bind_param("i", $invoicenumber);
        $checkStmt->execute();
        $checkStmt->bind_result($count);
        $checkStmt->fetch();
        $checkStmt->close();

        if ($count > 0) {
            echo "<h1>Error: The invoicenumber already exists.</h1>";
        } else {
            // Calculate the total bill
            $totalBill = $linenCharge + $otherExpenses;

            // Insert the total bill into the bills table
            $stmt = $connection->prepare("INSERT INTO bills (invoicenumber, EmployeeID, linenCharge, otherExpenses, totalBill) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("isddd", $invoicenumber, $employeeID, $linenCharge, $otherExpenses, $totalBill);

            if ($stmt->execute()) {
                echo "<h1>Total Bill: Rs$totalBill</h1>";
                echo "<h2>Bill successfully stored in the database.</h2>";
                echo "<a href='Operationaldashboard.php'>Back to Dashboard</a>";

                // Send email to the employee
                sendBillEmail($employeeID, $invoicenumber, $linenCharge, $otherExpenses, $totalBill);
            } else {
                echo "<h1>Error: " . $stmt->error . "</h1>";
            }

            // Close the statement and connection
            $stmt->close();
            $connection->close();
        }
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
            <label for="pillowCases">Number of Pillow Cases:</label>
            <input type="number" id="pillowCases" name="pillowCases" required>
            <br>
            <label for="bedSheets">Number of Bed Sheets:</label>
            <input type="number" id="bedSheets" name="bedSheets" required>
            <br>
            <label for="towels">Number of Towels:</label>
            <input type="number" id="towels" name="towels" required>
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
