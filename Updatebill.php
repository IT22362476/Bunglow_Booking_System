<?php
require 'Mysqlconnection.php'; // Include your database connection file
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;

function sendBillUpdateEmail($employeeID, $invoicenumber, $roomCharge, $linenCharge, $otherExpenses, $totalBill) {
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
        $mail->Subject = 'Updated Bill Details';
        $mail->Body    = "
            <h1>Updated Bill Details</h1>
            <p>Invoice Number: $invoicenumber</p>
            <p>Room Charge: Rs$roomCharge</p>
            <p>Linen Charge: Rs$linenCharge</p>
            <p>Other Expenses: Rs$otherExpenses</p>
            <p>Total Bill: Rs$totalBill</p>
        ";

        if (!$mail->send()) {
            echo "<h1>Email could not be sent. Mailer Error: " . $mail->ErrorInfo . "</h1>";
        } else {
            echo "<h2>Email with updated bill details has been sent to the employee.</h2>";
        }
    } else {
        echo "<h1>Employee email not found.</h1>";
    }
}

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
        $stmt = $connection->prepare("UPDATE bills SET roomCharge = ?, linenCharge = ?, otherExpenses = ?, totalBill = ? WHERE EmployeeID = ? AND invoicenumber = ?");
        $stmt->bind_param("ddddss", $roomCharge, $linenCharge, $otherExpenses, $totalBill, $employeeID, $invoicenumber);

        if ($stmt->execute()) {
            echo "<h1>Total Bill: Rs$totalBill</h1>";
            echo "<h2>Bill successfully updated in the database.</h2>";

            // Send the updated bill details via email
            sendBillUpdateEmail($employeeID, $invoicenumber, $roomCharge, $linenCharge, $otherExpenses, $totalBill);
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
    $stmt = $connection->prepare("SELECT roomCharge, linenCharge, otherExpenses FROM bills WHERE EmployeeID = ? AND invoicenumber = ?");
    $stmt->bind_param("is", $employeeID, $invoicenumber);
    $stmt->execute();
    $stmt->bind_result($roomCharge, $linenCharge, $otherExpenses);
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
            <input type="number" id="roomCharge" name="roomCharge" value="<?php echo htmlspecialchars($roomCharge); ?>" required>
            <br>
            <label for="linenCharge">Linen Charge:</label>
            <input type="number" id="linenCharge" name="linenCharge" value="<?php echo htmlspecialchars($linenCharge); ?>" required>
            <br>
            <label for="otherExpenses">Other Expenses:</label>
            <input type="number" id="otherExpenses" name="otherExpenses" value="<?php echo htmlspecialchars($otherExpenses); ?>" required>
            <br>
            <input type="submit" value="Update Total Bill">
        </form>
    </body>
    </html>

    <?php
}
?>
