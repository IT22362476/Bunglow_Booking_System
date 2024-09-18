<?php
session_start();
require 'Mysqlconnection.php'; // Include your MySQL connection file

// Check if the delete request is sent
if (isset($_POST['delete'])) {
    $dateToDelete = mysqli_real_escape_string($connection, $_POST['date']);
    $deleteQuery = "DELETE FROM maintenance WHERE date = '$dateToDelete'";
    
    if (mysqli_query($connection, $deleteQuery)) {
        echo "<script>alert('Blocked day removed successfully.'); window.location.href='Blocked.php';</script>";
    } else {
        echo "Error deleting record: " . mysqli_error($connection);
    }
}

$sql = "SELECT date, reason FROM maintenance";
$result = mysqli_query($connection, $sql);

if (!$result) {
    die("Query failed: " . mysqli_error($connection));
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blocked Days</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* General styling for the page */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5; /* Light grey background for the entire page */
            display: flex;
        }

        .container {
            padding: 20px;
            transition: margin-left 0.3s; /* Smooth transition for margin adjustment */
            flex-grow: 1;
        }

        /* Sidebar styling */
        .sidebar {
            width: 250px;
            background-color: #4CAF50; /* Green background for the sidebar */
            color: white; /* White text color for the sidebar */
            position: fixed;
            height: 100%;
            top: 0;
            left: 0; /* Initially shown */
            overflow: hidden;
            transition: width 0.3s; /* Smooth transition for the sidebar */
            z-index: 1000; /* Ensure sidebar appears above other content */
        }

        .sidebar.shrink {
            width: 0; /* Hide the sidebar */
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

        /* Toggle button styling */
        .menu-toggle {
            position: fixed;
            top: 10px;
            left: 10px;
            font-size: 24px;
            cursor: pointer;
            color: #235428; /* Green color for the menu icon */
            z-index: 1001; /* Ensure the toggle icon is above other content */
        }

        /* Main content styles */
        .main-content {
            margin-left: 250px;
            padding: 20px;
            width: calc(100% - 250px);
            transition: margin-left 0.3s, width 0.3s;
            flex-grow: 1;
        }

        .main-content.shrink {
            margin-left: 0;
            width: 100%;
        }

        h2 {
            text-align: center;
            color: #2f8f2f;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #2f8f2f;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:nth-child(odd) {
            background-color: #ffffff;
        }

        .action-button {
            cursor: pointer;
            text-decoration: none;
            padding: 0.4em;
            border: solid 1px black;
            border-radius: 0.5em;
            color: #f9f9f9;
        }

        .delete-button {
            background-color: #ed4239;
        }

        .edit-button {
            background-color: #4CAF50;
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

            </ul>
        </nav>
    </div>

    <!-- Main Content Section -->
    <div class="main-content" id="main-container">
        <div class="container">
            <h2>Blocked Days</h2>

            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Reason</th>
                        <th>Action</th> <!-- New column for actions -->
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['date']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['reason']) . "</td>";
                            echo "<td>
                                    <form method='POST' action='Blocked.php' onsubmit='return confirm(\"Are you sure you want to remove this entry?\")'>
                                        <input type='hidden' name='date' value='" . htmlspecialchars($row['date']) . "'>
                                        <button type='submit' name='delete' class='action-button delete-button'>Remove</button>
                                    </form>
                                  </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='3'>No blocked days found</td></tr>";
                    }
                    // Close the database connection
                    mysqli_close($connection);
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
            var mainContent = document.getElementById("main-container");
            sidebar.classList.toggle("shrink");
            mainContent.classList.toggle("shrink");

            // Store the sidebar state in local storage
            if (sidebar.classList.contains("shrink")) {
                localStorage.setItem('sidebarState', 'shrink');
            } else {
                localStorage.setItem('sidebarState', 'expand');
            }
        }

        // Check and apply the sidebar state on page load
        window.onload = function () {
            var sidebar = document.getElementById("sidebar");
            var mainContent = document.getElementById("main-container");
            var storedState = localStorage.getItem('sidebarState');

            if (storedState === 'shrink') {
                sidebar.classList.add('shrink');
                mainContent.classList.add('shrink');
            }
        }
    </script>
</body>

</html>
