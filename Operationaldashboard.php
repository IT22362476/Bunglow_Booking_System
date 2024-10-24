<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script>
                function toggleDropdown() {
            document.getElementById("profileDropdown").classList.toggle("show");
        }

        // Close the dropdown if the user clicks outside of it
        window.onclick = function (event) {
            if (!event.target.matches('.profile-img')) {
                var dropdowns = document.getElementsByClassName("dropdown-content");
                for (var i = 0; i < dropdowns.length; i++) {
                    var openDropdown = dropdowns[i];
                    if (openDropdown.classList.contains('show')) {
                        openDropdown.classList.remove('show');
                    }
                }
            }
        }
    </script>
    <style>
        /* General styling for the table */
        table {
            width: 100%;
            border-collapse: collapse;
            border: 2px solid #4CAF50;
            /* Green border for the table */
            border-radius: 8px;
            /* Border radius for rounded corners */
            overflow: hidden;
            /* Ensures rounded corners are visible */
        }
        .toparea {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
            margin-bottom: 20px;
            padding: 10px 20px;
            /* Adjust spacing around the area */
           // background-color: white;
            /* Optionally add a background for better separation */
            //box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            /* Add subtle shadow for depth */
        }

        h2 {
            color: #235428;
            /* Green color for the header */
            margin: 0;
        }

        .profile-img {
            cursor: pointer;
            height: 40px;
            width: 40px;
            border-radius: 50%;
            /* Make the profile image circular */
        }

                .dropdown {
            position: relative;
            display: inline-block;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
            border-radius: 10px;
            z-index: 1;
            right: 0;
            margin-top: 10px;
        }

        .dropdown-content a {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }

        .dropdown-content a:hover {
            background-color: #f1f1f1;
        }

        .dropdown .show {
            display: block;
        }
        /* Styling for table headers */
        th {
            background-color: #4CAF50;
            /* Green background for headers */
            color: white;
            /* White text color for headers */
            padding: 12px;
            /* Padding for headers */
        }

        /* Styling for table data cells */
        td {
            border: 1px solid #4CAF50;
            /* Green border for cells */
            padding: 10px;
            /* Padding for cells */
            text-align: left;
            /* Align text to the left */
        }

        /* Alternating row colors for the table */
        tr:nth-child(even) {
            background-color: #e8f5e9;
            /* Light green for even rows */
        }

        tr:nth-child(odd) {
            background-color: #f9f9f9;
            /* Light grey for odd rows */
        }

        /* Styling for action buttons */
        .action-button {
            cursor: pointer;
            text-decoration: none;
            padding: 0.4em 0.8em;
            /* Increased padding for better spacing */
            border: solid 1px black;
            border-radius: 0.5em;
            color: white;
            /* White text color for buttons */
            display: inline-block;
            /* Ensures buttons have spacing */
            margin-top: 5px;
            /* Space between buttons */
        }

        /* Specific styling for different button types */
        .delete-button {
            background-color: #ed4239;
            /* Red for delete buttons */
        }

        .edit-button {
            background-color: #4CAF50;
            /* Green for edit buttons */
        }

        .update-button {
            background-color: #FFA500;
            /* Orange for update buttons */
        }

        /* Styling for navigation list */
        .nav-list {
            display: flex;
            gap: 3em;
        }

        /* Styling for navigation items */
        .nav-items {
            list-style-type: none;
        }

        .nav-items a {
            text-decoration: none;
            color: black;
        }

        /* Styling for member picture */
        .member-picture {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 50%;
            cursor: pointer;
        }
    </style>
    <script>
        // JavaScript function to handle 'Not Arrived' button click
        function notArrived(employeeID, name) {
            // Show a confirmation alert
            const confirmation = confirm(`Confirm that Employee ${name} (ID: ${employeeID}) has not arrived.`);
            if (confirmation) {
                // If confirmed, send an AJAX request to the server to trigger email sending
                const xhr = new XMLHttpRequest();
                xhr.open('POST', 'Operationaldashboard.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onreadystatechange = function () {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        alert('Email sent successfully.');
                    }
                };
                xhr.send(`employeeID=${employeeID}&name=${name}`);
            }
        }
    </script>
</head>

<body>
    <div class="toparea">
        <h2>Super Admin Dashboard</h2>
        <li class="nav-list dropdown">
            <img src="./Images/image.png" class="profile-img" onclick="toggleDropdown()" />
            <div id="profileDropdown" class="dropdown-content">
                <a href="Logout.php">Logout</a>
            </div>
        </li>
    </div>
    <div class="container">
        <table>
            <thead>
                <tr>
                    <th>Invoice Number</th>
                    <th>Employee ID</th>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Check-In</th>
                    <th>Check-Out</th>
                    <th>Persons</th>
                    <th>Requests</th>
                    <th>Bill</th>
                    <th>Picture</th>
                    <th>Arrival Status</th> <!-- New column for Arrival Status -->
                </tr>
            </thead>
            <tbody>
                <?php
                require 'Mysqlconnection.php'; // Include your database connection file
                
                // Query to select all data including the name, phone, and picture from the users table
                $sql = "SELECT r.invoicenumber, r.EmployeeID, r.checkin, r.checkout, r.persons, r.requests, b.totalBill, u.Name, u.Phone, u.picture 
                        FROM reservations r
                        LEFT JOIN bills b ON r.invoicenumber = b.invoicenumber AND r.EmployeeID = b.EmployeeID
                        LEFT JOIN users u ON r.EmployeeID = u.EmployeeID"; // Join with users table to get name, phone, and picture
                $result = mysqli_query($connection, $sql);

                if ($result) {
                    // Fetch and display data
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['invoicenumber']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['EmployeeID']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['Name']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['Phone']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['checkin']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['checkout']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['persons']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['requests']) . "</td>";

                        // Display bill buttons
                        if (isset($row['totalBill'])) {
                            echo "<td><button class='action-button edit-button' disabled>Calculated</button><br/>";
                            echo "<a href='Updatebill.php?invoicenumber=" . htmlspecialchars($row['invoicenumber']) . "&EmployeeID=" . htmlspecialchars($row['EmployeeID']) . "' class='action-button update-button'>Update</a></td>";
                        } else {
                            echo "<td><a href='Calculatebill.php?invoicenumber=" . htmlspecialchars($row['invoicenumber']) . "&EmployeeID=" . htmlspecialchars($row['EmployeeID']) . "' class='action-button edit-button'>Calculate</a></td>";
                        }

                        // Display picture with a link to open in a new tab
                        if (!empty($row['picture'])) {
                            echo "<td><a href='uploads/" . htmlspecialchars($row['picture']) . "' target='_blank'>";
                            echo "<img src='uploads/" . htmlspecialchars($row['picture']) . "' alt='Member Picture' class='member-picture'>";
                            echo "</a></td>";
                        } else {
                            echo "<td>No picture available</td>";
                        }

                        // Arrival status button
                        echo "<td><button class='action-button delete-button' onclick=\"notArrived('" . htmlspecialchars($row['EmployeeID']) . "', '" . htmlspecialchars($row['Name']) . "')\">Not Arrived</button></td>";

                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='11'>No data found</td></tr>";
                }

                // Close the database connection
                mysqli_close($connection);
                ?>
            </tbody>
        </table>
    </div>
</body>

</html>

<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Include PHPMailer files
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

// Load PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// sendEmail.php file
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve the form data
    $employeeID = $_POST['employeeID'];  // Ensure these match your form data keys
    $name = $_POST['name'];

    // Create a new PHPMailer instance
    $mail = new PHPMailer(true);

    try {
        //Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'denuwansathsara0412@gmail.com'; // Your SMTP username
        $mail->Password = 'boaa moki kmax yzyz'; // Your SMTP password
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;

        //Recipients
        $mail->setFrom('denuwansathsara0412@gmail.com', 'Denuwan');
        $mail->addAddress('dewa20021030@gmail.com'); // Recipient's email address

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Employee Not Arrived';
        $mail->Body = "The employee with Employee ID: <strong>$employeeID</strong> and Name: <strong>$name</strong> has not arrived.";

        // Send email
        $mail->send();
        echo 'Email has been sent successfully.';
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
?>