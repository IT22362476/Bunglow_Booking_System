<?php
require 'Mysqlconnection.php'; // Include your database connection file
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;

function sendBillEmail($employeeID, $invoicenumber, $pillowCases, $bedSheets, $towels, $handserviette, $duster, $bathmate, $apron, $otherExpenses, $totalBill, $pillowCasesPrice, $bedSheetsPrice, $towelsPrice, $handserviettePrice, $dusterPrice, $bathmatePrice, $apronPrice) {
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
            <p>Pillow Cases: $pillowCases (Total: Rs$pillowCasesPrice)</p>
            <p>Bed Sheets: $bedSheets (Total: Rs$bedSheetsPrice)</p>
            <p>Towels: $towels (Total: Rs$towelsPrice)</p>
            <p>Handserviette: $handserviette (Total: Rs$handserviettePrice)</p>
            <p>Duster: $duster (Total: Rs$dusterPrice)</p>
            <p>Bathmate: $bathmate (Total: Rs$bathmatePrice)</p>
            <p>Aprons: $apron (Total: Rs$apronPrice)</p>
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

        // Calculate the total charge for each linen item
        $stmt->bind_param("s", $item);

        // Pillow Cases
        $item = 'pillowCase';
        $stmt->execute();
        $stmt->bind_result($price);
        $stmt->fetch();
        $pillowCasesTotal = $pillowCases * $price;
        $pillowCasesPrice = $pillowCasesTotal;

        // Bed Sheets
        $item = 'bedSheet';
        $stmt->execute();
        $stmt->bind_result($price);
        $stmt->fetch();
        $bedSheetsTotal = $bedSheets * $price;
        $bedSheetsPrice = $bedSheetsTotal;

        // Towels
        $item = 'towel';
        $stmt->execute();
        $stmt->bind_result($price);
        $stmt->fetch();
        $towelsTotal = $towels * $price;
        $towelsPrice = $towelsTotal;

        // Handserviette
        $item = 'handserviette';
        $stmt->execute();
        $stmt->bind_result($price);
        $stmt->fetch();
        $handservietteTotal = $handserviette * $price;
        $handserviettePrice = $handservietteTotal;

        // Duster
        $item = 'duster';
        $stmt->execute();
        $stmt->bind_result($price);
        $stmt->fetch();
        $dusterTotal = $duster * $price;
        $dusterPrice = $dusterTotal;

        // Bathmate
        $item = 'bathmate';
        $stmt->execute();
        $stmt->bind_result($price);
        $stmt->fetch();
        $bathmateTotal = $bathmate * $price;
        $bathmatePrice = $bathmateTotal;

        // Apron
        $item = 'apron';
        $stmt->execute();
        $stmt->bind_result($price);
        $stmt->fetch();
        $apronTotal = $apron * $price;
        $apronPrice = $apronTotal;

        $stmt->close();

        // Calculate the total bill
        $totalLinenCharge = $pillowCasesTotal + $bedSheetsTotal + $towelsTotal + $handservietteTotal + $dusterTotal + $bathmateTotal + $apronTotal;
        $totalBill = $totalLinenCharge + $otherExpenses;

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
            // Insert the bill into the database with each item stored separately
            $stmt = $connection->prepare("INSERT INTO bills (invoicenumber, EmployeeID, pillowCases, bedSheets, towels, handserviette, duster, bathmate, apron, otherExpenses, totalBill) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("isiiiiiiidd", $invoicenumber, $employeeID, $pillowCases, $bedSheets, $towels, $handserviette, $duster, $bathmate, $apron, $otherExpenses, $totalBill);

            if ($stmt->execute()) {
                echo "<h1>Total Bill: Rs$totalBill</h1>";
                echo "<h2>Bill successfully stored in the database.</h2>";
                echo "<a href='Operationaldashboard.php'>Back to Dashboard</a>";

                // Send email to the employee with the detailed bill, including item prices
                sendBillEmail($employeeID, $invoicenumber, $pillowCases, $bedSheets, $towels, $handserviette, $duster, $bathmate, $apron, $otherExpenses, $totalBill, $pillowCasesPrice, $bedSheetsPrice, $towelsPrice, $handserviettePrice, $dusterPrice, $bathmatePrice, $apronPrice);
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
            <h2>Calculate Total Bill</h2>
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
                <label for="handserviette">Number of handserviette:</label>
                <input type="number" id="handserviette" name="handserviette" required>
                <br>
                <label for="duster">Number of dusters:</label>
                <input type="number" id="duster" name="duster" required>
                <br>
                <label for="bathmate">Number of bathmates:</label>
                <input type="number" id="bathmate" name="bathmate" required>
                <br>
                <label for="apron">Number of aprons:</label>
                <input type="number" id="apron" name="apron" required>
                <br>
                <label for="otherExpenses">Other Expenses:</label>
                <input type="number" id="otherExpenses" name="otherExpenses" required>
                <br>
                <input type="submit" value="Calculate Total Bill">
            </form>
        </div>
    </body>

    </html>
    <?php
}
?>
