<?php
session_start();
require 'Mysqlconnection.php'; // Include the MySQL connection file

if (!isset($_SESSION['EmployeeID'])) {
    header("Location: Login.php");
    exit();
}

// Query to retrieve all executives from the executives table
$sql = "SELECT executives.EmployeeID, executives.Email, executives.picture, 
        CASE WHEN users.EmployeeID IS NOT NULL THEN 'signed_up' ELSE 'not_signed_up' END AS status
        FROM executives
        LEFT JOIN users ON executives.EmployeeID = users.EmployeeID";

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
    <title>Executives</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5; /* Light grey background for the entire page */
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

        /* .container {
            background-color: #ffffff; /* White background for the container */
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); /* Adding a subtle shadow for depth */
            border-radius: 8px; /* Rounded corners for the container */
            padding: 20px;
        } */

        h1 {
            text-align: center;
            color: #333; /* Dark grey heading */
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #2f8f2f; /* Green background for headers */
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9; /* Alternating row colors */
        }

        .not-signed-up {
            background-color: #f8d7da; /* Light red background for not signed up */
            color: #721c24; /* Dark red text */
        }

        .signed-up {
            background-color: #d4edda; /* Light green background for signed up */
            color: #155724; /* Dark green text */
        }

        /* Adding hover effect for table rows */
        tr:hover {
            background-color: #e2e3e5; /* Light grey hover effect */
        }

        /* Adding basic styling for links (if any) */
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
            </ul>
        </nav>
    </div>

    <!-- Main Content Section -->
    <div class="main-content" id="main-container">
        <div class="container">
            <h1>Executive List</h1>
            <table>
                <thead>
                    <tr>
                        <th>Employee ID</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                        <tr class="<?php echo $row['status'] === 'signed_up' ? 'signed-up' : 'not-signed-up'; ?>">
                            <td><?php echo htmlspecialchars($row['EmployeeID']); ?></td>
                            <td><?php echo $row['status'] === 'signed_up' ? 'Signed Up' : 'Not Signed Up'; ?></td>
                        </tr>
                    <?php } ?>
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
