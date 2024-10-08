<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Reservations</title>
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

        table {
            width: 100%;
            border-collapse: collapse;
            border: 2px solid #4CAF50;
            border-radius: 8px;
            overflow: hidden;
            margin: 20px 0;
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

        .search-container {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 20px;
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
        .search-input {
            padding: 10px;
            border: 1px solid #4CAF50;
            border-radius: 4px;
            width: 200px;
            margin-left: 10px;
        }

        .date-input {
            padding: 10px;
            border: 1px solid #4CAF50;
            border-radius: 4px;
            margin-left: 10px;
        }

        .edit-btn,
        .remove-btn {
            display: inline-block;
            padding: 5px 10px;
            color: #fff;
            text-align: center;
            text-decoration: none;
            border-radius: 4px;
        }

        .edit-btn {
            background-color: #4CAF50;
        }

        .remove-btn {
            background-color: #f44336;
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
    </style>
</head>

<body>
    <div class="menu-toggle" onclick="toggleSidebar()">&#9776;</div>

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

    <div class="container" id="main-container">
        <h2>Reservations</h2>
        <div class="search-container">
            <input type="text" id="search" class="search-input" onkeyup="filterTable()" placeholder="Search by Invoice Number or Employee ID...">
            <input type="date" id="checkin-filter" class="date-input" oninput="filterTable()" placeholder="Check-in Date">
            <input type="date" id="checkout-filter" class="date-input" oninput="filterTable()" placeholder="Check-out Date">
        </div>
        <table id="userTable">
            <thead>
                <tr>
                    <th>Invoice Number</th>
                    <th>Book Date</th>
                    <th>Employee ID</th>
                    <th>Check-in</th>
                    <th>Check-out</th>
                    <th>Persons</th>
                    <th>Requests</th>
                    <th>Edit</th>
                    <th>Remove</th>
                </tr>
            </thead>
            <tbody id="reservation-list">
                <?php
                require 'Mysqlconnection.php'; // Include your database connection file

                // Query to select all data from reservations
                $sql = "SELECT * FROM reservations";
                $result = mysqli_query($connection, $sql);

                if ($result) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['invoicenumber']) . "</td>";
                        echo "<td>" . htmlspecialchars(date('Y-m-d', strtotime($row['bookdate']))) . "</td>";
                        echo "<td>" . htmlspecialchars($row['EmployeeID']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['checkin']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['checkout']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['persons']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['requests']) . "</td>";
                        echo '<td><a href="Admineditreservations.php?id=' . urlencode($row['invoicenumber']) . '" class="edit-btn">Edit</a></td>';
                        echo '<td><a href="Adminremovereservations.php?id=' . urlencode($row['invoicenumber']) . '" class="remove-btn" onclick="return confirmDelete()">Delete</a></td>';
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='9'>No data found</td></tr>";
                }

                // Close the database connection
                mysqli_close($connection);
                ?>
            </tbody>
        </table>
    </div>

    <script>
        function toggleSidebar() {
            var sidebar = document.getElementById("sidebar");
            var container = document.getElementById("main-container");
            if (sidebar.classList.contains("active")) {
                sidebar.classList.remove("active");
                container.style.marginLeft = "0";
            } else {
                sidebar.classList.add("active");
                container.style.marginLeft = "250px";
            }
        }

        function confirmDelete() {
            return confirm('Are you sure you want to delete this reservation?');
        }

        function filterTable() {
            const input = document.getElementById('search');
            const filter = input.value.toLowerCase();
            const checkinFilter = document.getElementById('checkin-filter').value;
            const checkoutFilter = document.getElementById('checkout-filter').value;

            const table = document.getElementById('userTable');
            const trs = table.getElementsByTagName('tr');

            for (let i = 1; i < trs.length; i++) { // Start from 1 to skip the header row
                const tds = trs[i].getElementsByTagName('td');
                const invoiceNumber = tds[0].textContent.toLowerCase(); // Invoice Number
                const employeeId = tds[2].textContent.toLowerCase(); // Employee ID
                const checkinDate = new Date(tds[3].textContent); // Check-in Date
                const checkoutDate = new Date(tds[4].textContent); // Check-out Date

                const matchesSearch = invoiceNumber.includes(filter) || employeeId.includes(filter);
                const matchesCheckin = checkinFilter ? checkinDate >= new Date(checkinFilter) : true;
                const matchesCheckout = checkoutFilter ? checkoutDate <= new Date(checkoutFilter) : true;

                if (matchesSearch && matchesCheckin && matchesCheckout) {
                    trs[i].style.display = ''; // Show the row
                } else {
                    trs[i].style.display = 'none'; // Hide the row
                }
            }
        }
    </script>
</body>

</html>
