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

        .search-container {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 20px;
        }

        .search-input,
        .date-input {
            padding: 8px;
            border: 1px solid #4CAF50;
            border-radius: 4px;
            margin-left: 10px;
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
        </nav>
    </div>

    <!-- Main Content Section -->
    <div class="main-content" id="main-container">
        <h2>Reservations</h2>
        <div class="search-container">
            <form method="GET" action="">
                <input type="text" name="search" class="search-input" placeholder="Search by Invoice Number or Employee ID" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                <input type="date" name="checkin" class="date-input" placeholder="Check-in Date" value="<?php echo isset($_GET['checkin']) ? htmlspecialchars($_GET['checkin']) : ''; ?>">
                <input type="date" name="checkout" class="date-input" placeholder="Check-out Date" value="<?php echo isset($_GET['checkout']) ? htmlspecialchars($_GET['checkout']) : ''; ?>">
                <input type="submit" value="Filter" class="date-input" style="background-color: #4CAF50; color: white;">
            </form>
        </div>
        <table>
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
            <?php
            include("Mysqlconnection.php");

            // Prepare search query if search term or date range is provided
            $searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
            $checkinDate = isset($_GET['checkin']) ? $_GET['checkin'] : '';
            $checkoutDate = isset($_GET['checkout']) ? $_GET['checkout'] : '';

            $query = "SELECT * FROM reservations WHERE (invoicenumber LIKE ? OR EmployeeID LIKE ?)";
            $params = [];
            $params[] = "%" . $searchTerm . "%";
            $params[] = "%" . $searchTerm . "%";

            // Adding date range condition
            if (!empty($checkinDate) && !empty($checkoutDate)) {
                $query .= " AND (checkin >= ? AND checkout <= ?)";
                $params[] = $checkinDate;
                $params[] = $checkoutDate;
            }

            $stmt = mysqli_prepare($connection, $query);
            mysqli_stmt_bind_param($stmt, str_repeat('s', count($params)), ...$params);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            while ($row = mysqli_fetch_assoc($result)) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['invoicenumber']); ?></td>
                    <td><?php echo htmlspecialchars(date('Y-m-d', strtotime($row['bookdate']))); ?></td>
                    <td><?php echo htmlspecialchars($row['EmployeeID']); ?></td>
                    <td><?php echo htmlspecialchars($row['checkin']); ?></td>
                    <td><?php echo htmlspecialchars($row['checkout']); ?></td>
                    <td><?php echo htmlspecialchars($row['persons']); ?></td>
                    <td><?php echo htmlspecialchars($row['requests']); ?></td>
                    <td><a href="Sadmineditreservations.php?id=<?php echo urlencode($row['invoicenumber']); ?>" class="edit-btn">Edit</a></td>
                    <td><a href="Sadminremovereservations.php?id=<?php echo urlencode($row['invoicenumber']); ?>" class="remove-btn" onclick="return confirmDeletion()">Delete</a></td>
                </tr>
            <?php } ?>
        </table>
    </div>

    <!-- JavaScript for Sidebar Toggle and Confirmation -->
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

        function confirmDeletion() {
            return confirm("Are you sure you want to delete this reservation?");
        }

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
