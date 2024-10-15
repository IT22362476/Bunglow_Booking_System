<?php
// Start the session to check if the admin is logged in
session_start();

// Include database connection file
require 'Mysqlconnection.php';

// Check if the admin is logged in
if (!isset($_SESSION['EmployeeID'])) {
    header("Location: login.php");
    exit();
}

// Retrieve update logs from the database, format update_date to YYYY-MM-DD
$query = "SELECT EmployeeID, invoicenumber, update_count, DATE_FORMAT(update_date, '%Y-%m-%d') as update_date FROM update_logs";
$result = $connection->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Logs</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f8f5; /* Light green background */
            margin: 0;
            padding: 0;
        }

        .menu-toggle {
            position: fixed;
            top: 10px;
            left: 10px;
            font-size: 24px;
            cursor: pointer;
            color: #235428; /* Green color for the menu icon */
            z-index: 1001; /* Ensure the toggle icon is above other content */
        }

        /* Sidebar styling */
        .sidebar {
            width: 250px;
            background-color: #4CAF50; /* Green background for the sidebar */
            color: white; /* White text color for the sidebar */
            position: fixed;
            height: 100%;
            top: 0;
            left: -250px; /* Initially hidden */
            overflow: hidden;
            transition: left 0.3s; /* Smooth transition for the sidebar */
            z-index: 1000; /* Ensure sidebar appears above other content */
        }

        .sidebar.active {
            left: 0; /* Show the sidebar */
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
            background-color: #45a049; /* Slightly darker green for hover effect */
        }

        /* Main content styles */
        .main-content {
            margin-left: 250px;
            padding: 20px;
            width: calc(100% - 250px);
            transition: margin-left 0.3s, width 0.3s;
        }

        .main-content.shrink {
            margin-left: 0;
            width: 100%;
        }

        /* Styling for the content inside main content area */
        .container {
            text-align: center;
        }

        h1 {
            color: #2f8f2f; /* Green heading */
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); /* Slight shadow for better visibility */
        }

        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }

        th {
            background-color: #2f8f2f; /* Green color for headers */
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9; /* Alternating row colors */
        }

        tr:nth-child(odd) {
            background-color: #ffffff;
        }

        tr:hover {
            background-color: #d0e8d0; /* Light green hover effect */
        }

        a {
            color: #2f8f2f;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
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
                <li class="nav-items"><a href="Admindashboard.php">Users list</a></li>
                <li class="nav-items"><a href="Calendaradmin.php">Calendar</a></li>
                <li class="nav-items"><a href="Blocked.php">Blocked Days</a></li>
                <li class="nav-items"><a href="Adminreservations.php">Reservations</a></li>
                <li class="nav-items"><a href="Updatetrack.php">Update Tracker</a></li>
                <li class="nav-items"><a href="Executives.php">Executives</a></li>
                <li class="nav-items"><a href="Linen.php">Linen charges</a></li>

            </ul>
        </nav>
    </div>

    <!-- Main Content Section -->
    <div class="main-content" id="main-container">
        <div class="container">
            <h1>Update Logs</h1>
            <table>
                <thead>
                    <tr>
                        <th>Employee ID</th>
                        <th>Invoice Number</th>
                        <th>Update Count</th>
                        <th>Update Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Check if any records were found
                    if ($result->num_rows > 0) {
                        // Loop through the result and display the records
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['EmployeeID']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['invoicenumber']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['update_count']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['update_date']) . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4'>No update logs found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- JavaScript for Sidebar Toggle -->
    <script>
        // Function to toggle the sidebar
        function toggleSidebar() {
            var sidebar = document.getElementById("sidebar");
            var container = document.getElementById("main-container");
            if (sidebar.classList.contains("active")) {
                sidebar.classList.remove("active");
                container.style.marginLeft = "0"; // Use full width when the sidebar is hidden
            } else {
                sidebar.classList.add("active");
                container.style.marginLeft = "250px"; // Adjust the container margin when the sidebar is shown
            }
        }

        // Initialize the page based on sidebar state
        document.addEventListener('DOMContentLoaded', function() {
            var sidebar = document.getElementById("sidebar");
            var container = document.getElementById("main-container");
            if (sidebar.classList.contains("active")) {
                container.style.marginLeft = "250px";
            } else {
                container.style.marginLeft = "0";
            }
        });
    </script>
</body>
</html>
