<?php
session_start();
require 'Mysqlconnection.php'; // Include the MySQL connection file

if (!isset($_SESSION['EmployeeID'])) {
    header("Location: Login.php");
    exit();
}

$EmployeeID = $_SESSION['EmployeeID'];

// Function to move completed reservations to reservationhistories table and delete them from reservations table
function moveCompletedReservations($connection, $EmployeeID)
{
    $currentDate = date('Y-m-d');

    // Select reservations with checkout date less than the current date
    $sql = "SELECT * FROM reservations WHERE checkout < '$currentDate' AND EmployeeID='$EmployeeID'";
    $result = mysqli_query($connection, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            // Insert into reservationhistories
            $insertSql = "INSERT INTO reservationhistories (invoicenumber, EmployeeID, checkin, checkout, persons, requests, status)
                          VALUES ('{$row['invoicenumber']}', '{$row['EmployeeID']}', '{$row['checkin']}', '{$row['checkout']}', '{$row['persons']}', '{$row['requests']}', 'Completed')";
            mysqli_query($connection, $insertSql);

            // Delete from reservations
            $deleteSql = "DELETE FROM reservations WHERE invoicenumber = '{$row['invoicenumber']}'";
            mysqli_query($connection, $deleteSql);
        }
    }
}

// Call the function to move completed reservations
moveCompletedReservations($connection, $EmployeeID);

// Retrieve current reservations for the logged-in user
$sql = "SELECT * FROM reservations WHERE EmployeeID='$EmployeeID'";
$result = mysqli_query($connection, $sql);

if (!$result) {
    die("Query failed: " . mysqli_error($connection));
}

// Function to calculate the difference in days between two dates
function dateDiffInDays($date1, $date2)
{
    $datetime1 = new DateTime($date1);
    $datetime2 = new DateTime($date2);
    $interval = $datetime1->diff($datetime2);
    return $interval->days;
}

// Function to check if a reservation has reached the maximum update count
function hasReachedMaxUpdateCount($connection, $invoicenumber)
{
    $sql = "SELECT COUNT(*) as update_count FROM update_logs WHERE invoicenumber='$invoicenumber'";
    $result = mysqli_query($connection, $sql);
    $row = mysqli_fetch_assoc($result);
    return $row['update_count'] >= 1;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservation Details</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }

        .container {
            padding: 20px;
            transition: margin-left 0.3s;
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

        .menu-toggle {
            position: fixed;
            top: 10px;
            left: 10px;
            font-size: 24px;
            cursor: pointer;
            color: #235428;
            z-index: 1001;
        }

        table {
            margin-left: 1em;
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

        .action-button {
            cursor: pointer;
            text-decoration: none;
            padding: 0.4em 0.8em;
            border: solid 1px black;
            border-radius: 0.5em;
            color:black;
            display: inline-block;
            margin-top: 5px;
        }

        .delete-button {
            background-color: #ed4239;
        }

        .edit-button {
            background-color: #4CAF50;
        }

        .disabled-button {
            background-color: #cccccc;
            cursor: not-allowed;
        }

        .Addbtn {
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 4px;
            cursor: pointer;
            margin: 20px auto;
            width: fit-content;
        }
        .Homelk{
            text-decoration: none;
            color: white;
        }
        .Homebtn{
            padding: 10px;
            margin-left: 1em;
            border-radius: 10px;
            background-color: green;
        }
    </style>
</head>

<body>
    <div class="menu-toggle" onclick="toggleSidebar()">&#9776;</div>

    <div class="sidebar" id="sidebar">
        <nav>
            <ul class="nav-list">
                <li class="nav-items"> <a href="Reservationhistory.php" class="history">History</a>
                <li class="nav-items"> <a href="Reservations.php" class="history">Reservations</a>
                </li>
            </ul>
        </nav>
    </div>

    <div class="container" id="main-container">
        <table>
            <thead>
                <tr>
                    <th>Invoice Number</th>
                    <th>Check-in</th>
                    <th>Check-out</th>
                    <th>Persons</th>
                    <th>Requests</th>
                    <th>Edit</th>
                    <th>Remove</th>
                    <th>Bill Details</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $currentDate = date('Y-m-d');
                while ($row = mysqli_fetch_assoc($result)) {
                    $checkinDate = $row['checkin'];
                    $daysUntilCheckin = dateDiffInDays($currentDate, $checkinDate);
                    $isEditableOrDeletable = $daysUntilCheckin >= 14;

                    // Check if the maximum update count is reached
                    $hasMaxUpdateCount = hasReachedMaxUpdateCount($connection, $row['invoicenumber']);
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['invoicenumber']); ?></td>
                        <td><?php echo htmlspecialchars($checkinDate); ?></td>
                        <td><?php echo htmlspecialchars($row['checkout']); ?></td>
                        <td><?php echo htmlspecialchars($row['persons']); ?></td>
                        <td><?php echo htmlspecialchars($row['requests']); ?></td>
                        <td>
                            <?php if ($isEditableOrDeletable && !$hasMaxUpdateCount) { ?>
                                <a href="Reservationupdate.php?id=<?php echo $row['invoicenumber']; ?>"
                                    class="action-button edit-button">Edit</a>
                            <?php } else { ?>
                                <span class="action-button disabled-button">Edit</span>
                            <?php } ?>
                        </td>
                        <td>
                            <?php if ($isEditableOrDeletable) { ?>
                                <a href="Reservationdelete.php?id=<?php echo $row['invoicenumber']; ?>"
                                    class="action-button delete-button">Delete</a>
                            <?php } else { ?>
                                <span class="action-button disabled-button">Delete</span>
                            <?php } ?>
                        </td>
                        <td><a href="Billdetails.php?invoicenumber=<?php echo $row['invoicenumber']; ?>"
                                class="action-button">Bill Details</a></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <button class="Homebtn"><a href="index.php" class="Homelk">Back to home</a></button>

    <script>
        function toggleSidebar() {
            var sidebar = document.getElementById("sidebar");
            var container = document.getElementById("main-container");
            if (sidebar.style.left === "-250px") {
                sidebar.style.left = "0";
                container.style.marginLeft = "250px";
            } else {
                sidebar.style.left = "-250px";
                container.style.marginLeft = "0";
            }
        }
    </script>
</body>

</html>