<?php
require 'Mysqlconnection.php'; // Include your database connection file
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;

function sendUpdatedBillEmail($employeeID, $invoicenumber, $pillowCases, $bedSheets, $towels, $handserviette, $duster, $bathmate, $apron, $otherExpenses, $totalBill, $pillowCasesTotal, $bedSheetsTotal, $towelsTotal, $handservietteTotal, $dusterTotal, $bathmateTotal, $apronTotal) {
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
        $mail->Password = 'boaa moki kmax yzyz'; // Consider moving sensitive info to a secure environment
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;

        $mail->setFrom('denuwansathsara0412@gmail.com', 'Denuwan');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Your Updated Bill Details';
        $mail->Body = "
            <h1>Updated Bill Details</h1>
            <p>Invoice Number: $invoicenumber</p>
            <p>Pillow Cases: $pillowCases - Total Price: Rs$pillowCasesTotal</p>
            <p>Bed Sheets: $bedSheets - Total Price: Rs$bedSheetsTotal</p>
            <p>Towels: $towels - Total Price: Rs$towelsTotal</p>
            <p>Handserviette: $handserviette - Total Price: Rs$handservietteTotal</p>
            <p>Duster: $duster - Total Price: Rs$dusterTotal</p>
            <p>Bathmate: $bathmate - Total Price: Rs$bathmateTotal</p>
            <p>Aprons: $apron - Total Price: Rs$apronTotal</p>
            <p>Other Expenses: Rs$otherExpenses</p>
            <p>Total Bill: Rs$totalBill</p>
        ";

        if (!$mail->send()) {
            echo "<h1>Email could not be sent. Mailer Error: " . $mail->ErrorInfo . "</h1>";
        } else {
            echo "<h2>Updated bill email has been sent to the employee.</h2>";
            echo "<a href='Operationaldashboard.php'>Back to Dashboard</a>";
        }
    } else {
        echo "<h1>Employee email not found.</h1>";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the values from the form
    $pillowCases = $_POST['pillowCases'];
    $bedSheets = $_POST['bedSheets'];
    $handserviette = $_POST['handserviette'];
    $duster = $_POST['duster'];
    $bathmate = $_POST['bathmate'];
    $apron = $_POST['apron'];
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
        $pillowCasesTotal = $pillowCases * $price;
        $linenCharge += $pillowCasesTotal;

        $item = 'bedSheet';
        $stmt->execute();
        $stmt->bind_result($price);
        $stmt->fetch();
        $bedSheetsTotal = $bedSheets * $price;
        $linenCharge += $bedSheetsTotal;

        $item = 'towel';
        $stmt->execute();
        $stmt->bind_result($price);
        $stmt->fetch();
        $towelsTotal = $towels * $price;
        $linenCharge += $towelsTotal;

        $item = 'handserviette';
        $stmt->execute();
        $stmt->bind_result($price);
        $stmt->fetch();
        $handservietteTotal = $handserviette * $price;
        $linenCharge += $handservietteTotal;

        $item = 'duster';
        $stmt->execute();
        $stmt->bind_result($price);
        $stmt->fetch();
        $dusterTotal = $duster * $price;
        $linenCharge += $dusterTotal;

        $item = 'bathmate';
        $stmt->execute();
        $stmt->bind_result($price);
        $stmt->fetch();
        $bathmateTotal = $bathmate * $price;
        $linenCharge += $bathmateTotal;

        $item = 'apron';
        $stmt->execute();
        $stmt->bind_result($price);
        $stmt->fetch();
        $apronTotal = $apron * $price;
        $linenCharge += $apronTotal;

        $stmt->close();

        // Calculate the total bill
        $totalBill = $linenCharge + $otherExpenses;

        // Update the total bill in the bills table
        $stmt = $connection->prepare("UPDATE bills SET pillowCases = ?, bedSheets = ?, towels = ?, handserviette = ?, duster = ?, bathmate = ?, apron = ?, otherExpenses = ?, totalBill = ? WHERE invoicenumber = ? AND EmployeeID = ?");
        $stmt->bind_param("iiiiiiiddis", $pillowCases, $bedSheets, $towels, $handserviette, $duster, $bathmate, $apron, $otherExpenses, $totalBill, $invoicenumber, $employeeID);

        if ($stmt->execute()) {
            echo "<h1>Updated Total Bill: Rs$totalBill</h1>";
            echo "<h2>Bill successfully updated in the database.</h2>";

            // Send updated bill email to the employee
            sendUpdatedBillEmail($employeeID, $invoicenumber, $pillowCases, $bedSheets, $towels, $handserviette, $duster, $bathmate, $apron, $otherExpenses, $totalBill, $pillowCasesTotal, $bedSheetsTotal, $towelsTotal, $handservietteTotal, $dusterTotal, $bathmateTotal, $apronTotal);
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
    // Fetch existing bill data
    $employeeID = $_GET['EmployeeID'];
    $invoicenumber = $_GET['invoicenumber'];

    $stmt = $connection->prepare("SELECT pillowCases, bedSheets, towels, handserviette, duster, bathmate, apron, otherExpenses FROM bills WHERE invoicenumber = ? AND EmployeeID = ?");
    $stmt->bind_param("is", $invoicenumber, $employeeID);
    $stmt->execute();
    $stmt->bind_result($pillowCases, $bedSheets, $towels, $handserviette, $duster, $bathmate, $apron, $otherExpenses);
    $stmt->fetch();
    $stmt->close();
    ?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Update Bill</title>
        <style>
            /* Styling for the form */
            .form-container {
                max-width: 600px;
                margin: 0 auto;
                padding: 20px;
                border: 2px solid #4CAF50;
                border-radius: 8px;
                background-color: #f9f9f9;
            }

            .form-container label {
                font-weight: bold;
                margin-bottom: 10px;
                display: block;
            }

            .form-container input[type="number"] {
                width: 100%;
                padding: 10px;
                margin: 10px 0;
                border-radius: 4px;
                border: 1px solid #4CAF50;
                box-sizing: border-box;
            }

            .form-container input[type="submit"] {
                background-color: #4CAF50;
                color: white;
                border: none;
                padding: 10px 20px;
                border-radius: 4px;
                cursor: pointer;
                width: 100%;
                font-size: 16px;
            }

            .form-container input[type="submit"]:hover {
                background-color: #45a049;
            }

            h2 {
                text-align: center;
                margin-bottom: 20px;
                color: #4CAF50;
            }
        </style>
    </head>

    <body>
        <div class="form-container">
            <h2>Update Total Bill</h2>
            <form action="Updatebill.php" method="post">
                <input type="hidden" name="EmployeeID" value="<?php echo htmlspecialchars($employeeID); ?>">
                <input type="hidden" name="invoicenumber" value="<?php echo htmlspecialchars($invoicenumber); ?>">

                <label for="pillowCases">Number of Pillow Cases:</label>
                <input type="number" id="pillowCases" name="pillowCases" required value="<?php echo htmlspecialchars($pillowCases); ?>">
                <br>
                <label for="bedSheets">Number of Bed Sheets:</label>
                <input type="number" id="bedSheets" name="bedSheets" required value="<?php echo htmlspecialchars($bedSheets); ?>">
                <br>
                <label for="towels">Number of Towels:</label>
                <input type="number" id="towels" name="towels" required value="<?php echo htmlspecialchars($towels); ?>">
                <br>
                <label for="handserviette">Number of handserviette:</label>
                <input type="number" id="handserviette" name="handserviette" required value="<?php echo htmlspecialchars($handserviette); ?>">
                <br>
                <label for="duster">Number of dusters:</label>
                <input type="number" id="duster" name="duster" required value="<?php echo htmlspecialchars($duster); ?>">
                <br>
                <label for="bathmate">Number of bathmates:</label>
                <input type="number" id="bathmate" name="bathmate" required value="<?php echo htmlspecialchars($bathmate); ?>">
                <br>
                <label for="apron">Number of aprons:</label>
                <input type="number" id="apron" name="apron" required value="<?php echo htmlspecialchars($apron); ?>">
                <br>
                <label for="otherExpenses">Other Expenses:</label>
                <input type="number" id="otherExpenses" name="otherExpenses" required value="<?php echo htmlspecialchars($otherExpenses); ?>">
                <br>
                <input type="submit" value="Update Total Bill">
            </form>
        </div>
    </body>

    </html>
    <?php
}
?>
