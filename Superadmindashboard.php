<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5; /* Light grey background for the body */
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

        table {
            margin-left: 1em;
            width: 100%;
            border-collapse: collapse;
            border: 2px solid #4CAF50; /* Green border for the table */
            border-radius: 8px; /* Border radius for rounded corners */
            overflow: hidden; /* Ensures rounded corners are visible */
        }

        th {
            background-color: #4CAF50; /* Green background for headers */
            color: white; /* White text color for headers */
            padding: 12px; /* Padding for headers */
        }

        td {
            border: 1px solid #4CAF50; /* Green border for cells */
            padding: 10px; /* Padding for cells */
            text-align: left; /* Align text to the left */
        }

        tr:nth-child(even) {
            background-color: #e8f5e9; /* Light green for even rows */
        }

        tr:nth-child(odd) {
            background-color: #f9f9f9; /* Light grey for odd rows */
        }

        .action-button {
            cursor: pointer;
            text-decoration: none;
            padding: 0.4em 0.8em; /* Increased padding for better spacing */
            border: solid 1px black;
            border-radius: 0.5em;
            color: white; /* White text color for buttons */
            display: inline-block; /* Ensures buttons have spacing */
            margin-top: 5px; /* Space between buttons */
        }

        .delete-button {
            background-color: #ed4239; /* Red for delete buttons */
        }

        .edit-button {
            background-color: #4CAF50; /* Green for edit buttons */
        }

        /* Styling for the Add Member button */
        .Addbtn {
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #4CAF50; /* Green background for add button */
            color: white; /* White text color for add button */
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 4px; /* Rounded corners */
            cursor: pointer;
            margin: 20px auto; /* Centering and spacing */
            width: fit-content; /* Adjusts width to fit content */
        }

        /* Additional styling to ensure link within button behaves correctly */
        .Addbtn a {
            text-decoration: none;
            color: white; /* Ensure link text is white */
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
            </ul>
        </nav>
    </div>

    <!-- Main Content Section -->
    <div class="main-content" id="main-container">
        <table>
            <thead>
                <tr>
                    <th>UserID</th>
                    <th>EmployeeID</th>
                    <th>Guestname</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Edit</th>
                    <th>Remove</th>
                </tr>
            </thead>
            <tbody>
                <?php
                require 'Mysqlconnection.php'; // Include your database connection file
                
                // Query to select all data from the users table
                $sql = "SELECT UserID, EmployeeID, Guestname, Email, Phone FROM users";
                $result = mysqli_query($connection, $sql);

                if ($result) {
                    // Fetch and display data
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['UserID']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['EmployeeID']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['Guestname']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['Email']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['Phone']) . "</td>";
                        echo "<td><a href='Userupdate.php?UserID=" . htmlspecialchars($row['UserID']) . "' class='action-button edit-button'>Edit</a></td>";
                        echo "<td><a href='Superuserdelete.php?UserID=" . htmlspecialchars($row['UserID']) . "' class='action-button delete-button'>Delete</a></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='7'>No data found</td></tr>";
                }

                // Close the database connection
                mysqli_close($connection);
                ?>
            </tbody>
        </table>
        <button class="Addbtn"><a href="Addmembers.php">Add Member</a></button>
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
