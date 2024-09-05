<?php
session_start();
require 'Mysqlconnection.php';

// Check if the EmployeeID is set in the session
if (!isset($_SESSION['EmployeeID'])) {
    // Redirect to login page if not logged in
    header("Location: Login.php");
    exit();
}

// Retrieve the EmployeeID from the session
$employeeID = $_SESSION['EmployeeID'];

// Prepare and execute the SQL query to fetch reservation history for the logged-in user
$sql = "SELECT invoicenumber, checkin, checkout, persons, requests, status FROM reservationhistories WHERE EmployeeID = ?";
$stmt = $connection->prepare($sql);
$stmt->bind_param("s", $employeeID); // Use 's' for string type parameter
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservation History</title>
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

        h2 {
            color: #235428;
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            border: 2px solid #4CAF50;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 20px;
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

        tr:hover {
            background-color: #d4edda;
        }

        .no-reservations {
            text-align: center;
            font-weight: bold;
            color: #555;
            padding: 20px;
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
        <h2>Reservation History</h2>
        <table>
            <tr>
                <th>Invoice Number</th>
                <th>Check-in</th>
                <th>Check-out</th>
                <th>Persons</th>
                <th>Requests</th>
                <th>Status</th>
            </tr>
            <?php
            // Fetch data and display it in the table
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row["invoicenumber"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["checkin"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["checkout"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["persons"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["requests"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["status"]) . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='6' class='no-reservations'>No reservations found</td></tr>";
            }
            ?>
        </table>
    </div>

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

<?php
$stmt->close();
$connection->close();
?>
