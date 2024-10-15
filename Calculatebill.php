<?php
require 'Mysqlconnection.php'; // Include your database connection file
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;

function sendBillEmail($employeeID, $invoicenumber, $pillowCases, $bedSheets, $towels, $handserviette, $duster, $bathmate, $apron, $otherExpenses, $totalBill, $pillowCasesPrice, $bedSheetsPrice, $towelsPrice, $handserviettePrice, $dusterPrice, $bathmatePrice, $apronPrice)
{
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
        $mail->Password = 'boaa moki kmax yzyz'; // Make sure to use an app password if using Gmail
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;

        $mail->setFrom('denuwansathsara0412@gmail.com', 'Denuwan');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Your Bill Details';

        // Create table for the bill details
        $mail->Body = "
            <h1>Bill Details</h1>
            <p>Invoice Number: $invoicenumber</p>
            <table border='1' cellpadding='5' cellspacing='0'>
                <tr>
                    <th>Item</th>
                    <th>Quantity</th>
                    <th>Price (Rs)</th>
                    <th>Total (Rs)</th>
                </tr>
                <tr>
                    <td>Pillow Cases</td>
                    <td>$pillowCases</td>
                    <td>$pillowCasesPrice</td>
                    <td>" . $pillowCases * $pillowCasesPrice . "</td>
                </tr>
                <tr>
                    <td>Bed Sheets</td>
                    <td>$bedSheets</td>
                    <td>$bedSheetsPrice</td>
                    <td>" . $bedSheets * $bedSheetsPrice . "</td>
                </tr>
                <tr>
                    <td>Towels</td>
                    <td>$towels</td>
                    <td>$towelsPrice</td>
                    <td>" . $towels * $towelsPrice . "</td>
                </tr>
                <tr>
                    <td>Handserviette</td>
                    <td>$handserviette</td>
                    <td>$handserviettePrice</td>
                    <td>" . $handserviette * $handserviettePrice . "</td>
                </tr>
                <tr>
                    <td>Duster</td>
                    <td>$duster</td>
                    <td>$dusterPrice</td>
                    <td>" . $duster * $dusterPrice . "</td>
                </tr>
                <tr>
                    <td>Bathmate</td>
                    <td>$bathmate</td>
                    <td>$bathmatePrice</td>
                    <td>" . $bathmate * $bathmatePrice . "</td>
                </tr>
                <tr>
                    <td>Apron</td>
                    <td>$apron</td>
                    <td>$apronPrice</td>
                    <td>" . $apron * $apronPrice . "</td>
                </tr>
                <tr>
                    <td>Other Expenses</td>
                    <td colspan='3'>Rs$otherExpenses</td>
                </tr>
                <tr>
                    <th colspan='3'>Total Bill</th>
                    <th>Rs$totalBill</th>
                </tr>
            </table>
        ";

        if (!$mail->send()) {
            echo "<h1>Email could not be sent. Mailer Error: " . $mail->ErrorInfo . "</h1>";
        } else {
            echo "<h2>Email has been sent to the employee.</h2>";
            echo "<a href='Operationaldashboard.php' style='display: inline-block; background-color: green; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Back to Dashboard</a>";
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

    // Handle image upload
    $damageImage = '';
    if (isset($_FILES['damageImage']) && $_FILES['damageImage']['error'] == UPLOAD_ERR_OK) {
        $targetDir = "uploads/"; // Directory to save uploaded images
        $targetFile = basename($_FILES['damageImage']['name']);
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        // Check if file is an actual image
        $check = getimagesize($_FILES['damageImage']['tmp_name']);
        if ($check !== false) {
            // Check file size (limit to 2MB)
            if ($_FILES['damageImage']['size'] > 2000000) {
                echo "<h1>Sorry, your file is too large.</h1>";
                exit;
            }
            // Allow certain file formats
            if (!in_array($imageFileType, ['jpg', 'png', 'jpeg', 'gif'])) {
                echo "<h1>Sorry, only JPG, JPEG, PNG & GIF files are allowed.</h1>";
                exit;
            }
            // Attempt to move the uploaded file
            if (!move_uploaded_file($_FILES['damageImage']['tmp_name'], $targetFile)) {
                echo "<h1>Sorry, there was an error uploading your file.</h1>";
                exit;
            }
            $damageImage = $targetFile; // Save the path of the uploaded image
        } else {
            echo "<h1>File is not an image.</h1>";
            exit;
        }
    }

    $damageImage1 = '';
    if (isset($_FILES['damageImage1']) && $_FILES['damageImage1']['error'] == UPLOAD_ERR_OK) {
        $targetDir = "uploads/"; // Directory to save uploaded images
        $targetFile = basename($_FILES['damageImage1']['name']);
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        // Check if file is an actual image
        $check = getimagesize($_FILES['damageImage1']['tmp_name']);
        if ($check !== false) {
            // Check file size (limit to 2MB)
            if ($_FILES['damageImage1']['size'] > 2000000) {
                echo "<h1>Sorry, your file is too large.</h1>";
                exit;
            }
            // Allow certain file formats
            if (!in_array($imageFileType, ['jpg', 'png', 'jpeg', 'gif'])) {
                echo "<h1>Sorry, only JPG, JPEG, PNG & GIF files are allowed.</h1>";
                exit;
            }
            // Attempt to move the uploaded file
            if (!move_uploaded_file($_FILES['damageImage1']['tmp_name'], $targetFile)) {
                echo "<h1>Sorry, there was an error uploading your file.</h1>";
                exit;
            }
            $damageImage1 = $targetFile; // Save the path of the uploaded image
        } else {
            echo "<h1>File is not an image.</h1>";
            exit;
        }
    }

    $damageImage2 = '';
    if (isset($_FILES['damageImage2']) && $_FILES['damageImage2']['error'] == UPLOAD_ERR_OK) {
        $targetDir = "uploads/"; // Directory to save uploaded images
        $targetFile = basename($_FILES['damageImage2']['name']);
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        // Check if file is an actual image
        $check = getimagesize($_FILES['damageImage2']['tmp_name']);
        if ($check !== false) {
            // Check file size (limit to 2MB)
            if ($_FILES['damageImage2']['size'] > 2000000) {
                echo "<h1>Sorry, your file is too large.</h1>";
                exit;
            }
            // Allow certain file formats
            if (!in_array($imageFileType, ['jpg', 'png', 'jpeg', 'gif'])) {
                echo "<h1>Sorry, only JPG, JPEG, PNG & GIF files are allowed.</h1>";
                exit;
            }
            // Attempt to move the uploaded file
            if (!move_uploaded_file($_FILES['damageImage2']['tmp_name'], $targetFile)) {
                echo "<h1>Sorry, there was an error uploading your file.</h1>";
                exit;
            }
            $damageImage2 = $targetFile; // Save the path of the uploaded image
        } else {
            echo "<h1>File is not an image.</h1>";
            exit;
        }
    }

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
            $stmt = $connection->prepare("INSERT INTO bills (invoicenumber, EmployeeID, pillowCases, bedSheets, towels, handserviette, duster, bathmate, apron, otherExpenses, totalBill, damages,damages1,damages2) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?,?)");
            $stmt->bind_param("isiiiiiiiddsss", $invoicenumber, $employeeID, $pillowCases, $bedSheets, $towels, $handserviette, $duster, $bathmate, $apron, $otherExpenses, $totalBill, $damageImage, $damageImage1, $damageImage2);

            if ($stmt->execute()) {
                echo "<h2>Bill has been successfully created.</h2>";
                sendBillEmail($employeeID, $invoicenumber, $pillowCases, $bedSheets, $towels, $handserviette, $duster, $bathmate, $apron, $otherExpenses, $totalBill, $pillowCasesPrice, $bedSheetsPrice, $towelsPrice, $handserviettePrice, $dusterPrice, $bathmatePrice, $apronPrice);
            } else {
                echo "<h1>Error: " . $stmt->error . "</h1>";
            }
            $stmt->close();
        }
    } else {
        echo "<h1>Please enter valid numeric values for the linen items.</h1>";
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
                padding: 85px;
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

            .button-container {
                text-align: center;
                /* Center the button */
                margin-top: 20px;
                /* Add some space above the button */
            }

            .subbtn {
                padding: 10px;
                border-style: solid;
                border-color: green;
                border-radius: 1em;
                background-color: green;
                color: white;
                /* Add text color */
                font-size: 16px;
                /* Adjust font size */
            }

            .imageflx {
                display: flex;
            }

            .backbtn a {
                text-decoration: none;
                color: black;
            }

            .backbtn button{
                padding: 10px 20px;
                border-radius: 4px;
                background-color:green;
                color: white;

            }
        </style>
    </head>

    <body>
        <div class="form-container">
            <h2>Calculate Total Bill</h2>
            <form action="Calculatebill.php" method="POST" enctype="multipart/form-data">
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
                <div class="imageflx">
                    <div>
                        <label for="damageImage">Upload Image of<br>Damaged Item:</label>
                        <input type="file" name="damageImage" id="damageImage" accept="image/*">
                    </div>
                    <div>
                        <label for="damageImage">Upload Image of<br>Damaged Item:</label>
                        <input type="file" name="damageImage1" id="damageImage1" accept="image/*">
                    </div>
                    <div>
                        <label for="damageImage">Upload Image of<br>Damaged Item:</label>
                        <input type="file" name="damageImage2" id="damageImage2" accept="image/*">
                    </div>
                </div>
                <br><br>
                <div class="button-container">
                    <button type="submit" class="subbtn">Calculate Total Bill</button>
                </div>
            </form>

        </div>
        <div class="backbtn">
            <a href="Operationaldashboard.php"><button>Back</button></a>
        </div>
    </body>

    </html>
    <?php
}
?>