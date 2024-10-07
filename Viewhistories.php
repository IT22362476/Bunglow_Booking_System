<?php
// Start the session
session_start();

// Include database connection and PHPMailer for email sending
include('Mysqlconnection.php');
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;

// Check if the user is logged in and is a superadmin
if (!isset($_SESSION['EmployeeID'])) {
    header("Location: Login.php");
    exit();
}

$superadminID = $_SESSION['EmployeeID']; // Store the superadmin's EmployeeID

// Initialize search variables
$searchTerm = '';
$statusFilter = '';

// Check if the status update is submitted
if (isset($_POST['update_status'])) {
    $invoiceNumber = $_POST['invoicenumber'];
    $newStatus = $_POST['status'];

    // Update the status and override 'editedby' with the superadmin's EmployeeID
    $updateQuery = "UPDATE reservationhistories SET status = ?, editedby = ? WHERE invoicenumber = ?";
    $stmt = $connection->prepare($updateQuery);
    $stmt->bind_param('ssi', $newStatus, $superadminID, $invoiceNumber);

    if ($stmt->execute()) {
        // Fetch the EmployeeID from the updated reservation
        $fetchQuery = "SELECT EmployeeID FROM reservationhistories WHERE invoicenumber = ?";
        $stmtFetch = $connection->prepare($fetchQuery);
        $stmtFetch->bind_param('i', $invoiceNumber);
        $stmtFetch->execute();
        $resultFetch = $stmtFetch->get_result();
        $rowFetch = $resultFetch->fetch_assoc();
        $EmployeeID = $rowFetch['EmployeeID'];

        // Fetch employee email and name from users table
        $userQuery = "SELECT Email, Name FROM users WHERE EmployeeID = ?";
        $stmtUser = $connection->prepare($userQuery);
        $stmtUser->bind_param('s', $EmployeeID);
        $stmtUser->execute();
        $resultUser = $stmtUser->get_result();
        $rowUser = $resultUser->fetch_assoc();
        $EmployeeEmail = $rowUser['Email'];
        $EmployeeName = $rowUser['Name'];

        // Send email based on the selected status
        if ($newStatus == 'Approved Deletion') {
            $subject = "Miriyakalle Bungalow Management - Approved Deletion";
            $body = "\nDon't make any deductions relevant to $EmployeeID($EmployeeName). This is management approved.\n\nBest regards,\nManagement";
            sendEmail($EmployeeEmail, "dewa20021030@gmail.com", $subject, $body);
        } elseif ($newStatus == 'Pay & Delete') {
            $subject = "Miriyakalle Bungalow - Fine Deduction";
            $body = "\nDeduct Rs.2500 from $EmployeeName ($EmployeeID) due to bungalow fine.\n\nBest regards,\nManagement";
            sendEmail($EmployeeEmail, "dewa20021030@gmail.com", $subject, $body);
        }

        echo "Status updated, 'editedby' updated, and email sent successfully!";
    } else {
        echo "Error updating status: " . $connection->error;
    }
}

function sendEmail($employeeEmail, $adminEmail, $subject, $body)
{
    $mail = new PHPMailer;

    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'denuwansathsara0412@gmail.com';
    $mail->Password = 'boaa moki kmax yzyz'; // Consider moving sensitive info to a secure environment
    $mail->SMTPSecure = 'ssl';
    $mail->Port = 465;

    $mail->setFrom('denuwansathsara0412@gmail.com', 'Denuwan');
    $mail->addAddress($employeeEmail);
    $mail->addAddress($adminEmail);

    $mail->Subject = $subject;
    $mail->Body = $body;

    if (!$mail->send()) {
        echo 'Mailer Error: ' . $mail->ErrorInfo;
    } else {
        echo 'Message sent successfully.';
    }
}

// Check if a search term is provided
if (isset($_POST['search'])) {
    $searchTerm = $_POST['search_term'];
}

// Check if a status filter is provided
if (isset($_POST['status_filter'])) {
    $statusFilter = $_POST['status'];
}

// Fetch all reservation history along with Employee Name, optionally filtering by search term and status
$query = "
    SELECT rh.invoicenumber, rh.EmployeeID, rh.checkin, rh.checkout, rh.persons, rh.requests, rh.status, u.Name 
    FROM reservationhistories rh
    JOIN users u ON rh.EmployeeID = u.EmployeeID
";

$conditions = [];
$params = [];

if (!empty($searchTerm)) {
    $conditions[] = "(rh.EmployeeID LIKE ? OR u.Name LIKE ?)";
    $params[] = '%' . $searchTerm . '%';
    $params[] = '%' . $searchTerm . '%';
}

if (!empty($statusFilter)) {
    $conditions[] = "rh.status = ?";
    $params[] = $statusFilter;
}

if (!empty($conditions)) {
    $query .= " WHERE " . implode(' AND ', $conditions);
}

// Prepare and execute the query
$stmtQuery = $connection->prepare($query);
if (!empty($params)) {
    $stmtQuery->bind_param(str_repeat('s', count($params)), ...$params);
}
$stmtQuery->execute();
$result = $stmtQuery->get_result();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Reservation Histories</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
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
            overflow: hidden;
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

        .main-content {
            margin-left: 0;
            padding: 20px;
            width: 100%;
            transition: margin-left 0.3s, width 0.3s;
        }

        .main-content.shrink {
            margin-left: 250px;
            width: calc(100% - 250px);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            border: 2px solid #4CAF50;
            border-radius: 8px;
            overflow: hidden;
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

        select {
            width: 200px; /* Adjusted to match search field */
            padding: 5px;
        }

        button {
            margin-top: 10px;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }

        .search-container {
            text-align: right;
            margin-bottom: 20px;
        }

        .search-container input {
            padding: 5px;
            width: 200px; /* Search field width */
        }

        .filter-container {
            text-align: right;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <!-- Menu toggle icon -->
    <div class="menu-toggle" onclick="toggleSidebar()">&#9776;</div>

    <!-- Sidebar -->
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
            </ul>
            </ul>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content" id="main-content">
        <h1>View Reservation Histories</h1>

        <!-- Search and Filter Form -->
        <div class="search-container">
            <form method="POST" action="">
                <input type="text" name="search_term" placeholder="Search..." value="<?php echo htmlspecialchars($searchTerm); ?>">
                <button type="submit" name="search">Search</button>
            </form>
        </div>

        <div class="filter-container">
            <form method="POST" action="">
                <select name="status">
                    <option value="">All Statuses</option>
                    <option value="Approved Deletion" <?php echo $statusFilter == 'Approved Deletion' ? 'selected' : ''; ?>>Approved Deletion</option>
                    <option value="Pay & Delete" <?php echo $statusFilter == 'Pay & Delete' ? 'selected' : ''; ?>>Pay & Delete</option>
                    <!-- Add more options as needed -->
                </select>
                <button type="submit" name="status_filter">Filter</button>
            </form>
        </div>

        <!-- Reservation History Table -->
        <table>
            <thead>
                <tr>
                    <th>Invoice Number</th>
                    <th>Employee ID</th>
                    <th>Check-In</th>
                    <th>Check-Out</th>
                    <th>Persons</th>
                    <th>Requests</th>
                    <th>Status</th>
                    <th>Employee Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['invoicenumber']); ?></td>
                        <td><?php echo htmlspecialchars($row['EmployeeID']); ?></td>
                        <td><?php echo htmlspecialchars($row['checkin']); ?></td>
                        <td><?php echo htmlspecialchars($row['checkout']); ?></td>
                        <td><?php echo htmlspecialchars($row['persons']); ?></td>
                        <td><?php echo htmlspecialchars($row['requests']); ?></td>
                        <td><?php echo htmlspecialchars($row['status']); ?></td>
                        <td><?php echo htmlspecialchars($row['Name']); ?></td>
                        <td>
                            <form method="POST" action="">
                                <input type="hidden" name="invoicenumber" value="<?php echo htmlspecialchars($row['invoicenumber']); ?>">
                                <select name="status">
                                <option value="Completed" <?php echo ($row['status'] == 'Completed') ? 'selected' : ''; ?>>Completed</option>
                                        <option value="deleted" <?php echo ($row['status'] == 'deleted') ? 'selected' : ''; ?>>deleted</option>
                                        <option value="Approved Deletion" <?php echo ($row['status'] == 'Approved Deletion') ? 'selected' : ''; ?>>Approved Deletion</option>
                                        <option value="Pay & Delete" <?php echo ($row['status'] == 'Pay & Delete') ? 'selected' : ''; ?>>Pay & Delete</option>
                                </select>
                                <button type="submit" name="update_status">Update Status</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script>
        function toggleSidebar() {
            var sidebar = document.getElementById("sidebar");
            var mainContent = document.getElementById("main-content");
            sidebar.classList.toggle("active");
            mainContent.classList.toggle("shrink");
        }
    </script>
</body>

</html>
