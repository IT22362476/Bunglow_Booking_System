<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
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
        /* General styling for the page */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5; /* Light grey background for the entire page */
        }

        .container {
            padding: 20px;
            transition: margin-left 0.3s; /* Smooth transition for margin adjustment */
        }
        .menu-toggle {
            position: fixed;
            top: 10px;
            left: 10px;
            font-size: 24px;
            cursor: pointer;
            color: #235428;
            /* Green color for the menu icon */
            z-index: 1001;
            /* Ensure the toggle icon is above other content */
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

        /* Search bar styling */
        .search-container {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 20px;
        }

        .search-input {
            padding: 10px;
            border: 1px solid #4CAF50;
            border-radius: 4px;
            width: 200px; /* Adjust width as necessary */
            margin-left: 10px; /* Space between search label and input */
        }

        /* General styling for the table */
        table {
            margin-left: 1em;
            width: 100%;
            border-collapse: collapse;
            border: 2px solid #4CAF50; /* Green border for the table */
            border-radius: 8px; /* Border radius for rounded corners */
            overflow: hidden; /* Ensures rounded corners are visible */
        }

        /* Styling for table headers */
        th {
            background-color: #4CAF50; /* Green background for headers */
            color: white; /* White text color for headers */
            padding: 12px; /* Padding for headers */
        }

        /* Styling for table data cells */
        td {
            border: 1px solid #4CAF50; /* Green border for cells */
            padding: 10px; /* Padding for cells */
            text-align: left; /* Align text to the left */
        }

        /* Alternating row colors for the table */
        tr:nth-child(even) {
            background-color: #e8f5e9; /* Light green for even rows */
        }

        tr:nth-child(odd) {
            background-color: #f9f9f9; /* Light grey for odd rows */
        }

        /* Styling for action buttons */
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

        /* Specific styling for different button types */
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

        /* Styling for member picture */
        .member-picture {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 50%; /* Circular picture */
            cursor: pointer;
        }
        .profile-img {
            cursor: pointer;
            height: 40px;
            width: 40px;
            border-radius: 50%;
            /* Make the profile image circular */
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
                <li class="nav-items"><a href="Chargeshistory.php">Charges history</a></li>

            </ul>
        </nav>
    </div>

    <!-- Main content area -->
    <div class="container" id="main-container">
    <div class="toparea">
            <h2>Admin Dashboard</h2>
            <li class="nav-list dropdown">
                <img src="./Images/image.png" class="profile-img" onclick="toggleDropdown()" />
                <div id="profileDropdown" class="dropdown-content">
                    <a href="Logout.php">Logout</a>
                </div>
            </li>
        </div>
        <div class="search-container">
            <input type="text" id="search" class="search-input" onkeyup="filterTable()" placeholder="Search by EmployeeID or Name...">
        </div>

        <table id="userTable">
            <thead>
                <tr>
                    <th>UserID</th>
                    <th>EmployeeID</th>
                    <th>Guestname</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Picture</th>
                    <th>Remove</th>
                </tr>
            </thead>
            <tbody>
                <?php
                require 'Mysqlconnection.php'; // Include your database connection file

                // Query to select all data including the picture from the users table
                $sql = "SELECT UserID, EmployeeID, Name, Email, Phone, picture FROM users";
                $result = mysqli_query($connection, $sql);

                if ($result) {
                    // Fetch and display data
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['UserID']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['EmployeeID']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['Name']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['Email']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['Phone']) . "</td>";

                        // Display picture with a link to open in a new tab
                        if (!empty($row['picture'])) {
                            echo "<td><a href='uploads/" . htmlspecialchars($row['picture']) . "' target='_blank'>";
                            echo "<img src='uploads/" . htmlspecialchars($row['picture']) . "' alt='Member Picture' class='member-picture'>";
                            echo "</a></td>";
                        } else {
                            echo "<td>No picture available</td>";
                        }

                        // Add delete confirmation alert
                        echo "<td><a href='Userdelete.php?UserID=" . htmlspecialchars($row['UserID']) . "' class='action-button delete-button' onclick='return confirmDelete()'>Delete</a></td>";
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

        // Function to confirm user deletion
        function confirmDelete() {
            return confirm('Are you sure you want to delete this user?');
        }

        // Function to filter the table based on search input
        function filterTable() {
            const input = document.getElementById('search');
            const filter = input.value.toLowerCase();
            const table = document.getElementById('userTable');
            const trs = table.getElementsByTagName('tr');

            for (let i = 1; i < trs.length; i++) { // Start from 1 to skip the header row
                const tds = trs[i].getElementsByTagName('td');
                const employeeId = tds[1].textContent.toLowerCase(); // EmployeeID
                const name = tds[2].textContent.toLowerCase(); // Name
                if (employeeId.includes(filter) || name.includes(filter)) {
                    trs[i].style.display = ''; // Show the row
                } else {
                    trs[i].style.display = 'none'; // Hide the row
                }
            }
        }
    </script>
</body>
</html>
