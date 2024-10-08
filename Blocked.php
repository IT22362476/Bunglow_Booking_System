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

// Check if a date filter is applied
$dateFilter = isset($_GET['date_filter']) ? mysqli_real_escape_string($connection, $_GET['date_filter']) : '';

$sql = "SELECT date, reason FROM maintenance";
if (!empty($dateFilter)) {
    $sql .= " WHERE date = '$dateFilter'";
}
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
            background-color: #f5f5f5;
            display: flex;
        }

        .container {
            padding: 20px;
            transition: margin-left 0.3s;
            flex-grow: 1;
        }

        /* Sidebar styling */
        .sidebar {
            width: 250px;
            background-color: #4CAF50;
            color: white;
            position: fixed;
            height: 100%;
            top: 0;
            left: 0;
            overflow: hidden;
            transition: width 0.3s;
            z-index: 1000;
        }

        .sidebar.shrink {
            width: 0;
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

        /* Toggle button styling */
        .menu-toggle {
            position: fixed;
            top: 10px;
            left: 10px;
            font-size: 24px;
            cursor: pointer;
            color: #235428;
            z-index: 1001;
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

        /* Date filter input styling */
        .date-filter {
            position: absolute;
            top: 20px;
            right: 30px;
        }

        .date-filter label {
            margin-right: 10px;
            font-weight: bold;
        }

        .date-filter input[type="date"] {
            padding: 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .date-filter button {
            padding: 5px 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .date-filter button:hover {
            background-color: #45a049;
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
            <h2>Blocked Days</h2>

            <!-- Date filter form -->
            <div class="date-filter">
                <form method="GET" action="Blocked.php">
                    <label for="date_filter">Filter by Date:</label>
                    <input type="date" id="date_filter" name="date_filter" value="<?php echo htmlspecialchars($dateFilter); ?>">
                    <button type="submit">Filter</button>
                </form>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Reason</th>
                        <th>Action</th>
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
                                    <form method='POST' action='Blocked.php' onsubmit='return confirmDeletion(\"" . htmlspecialchars($row['date']) . "\")'>
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

    <!-- JavaScript for Sidebar Toggle and Confirmation -->
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

        // Function to confirm deletion
        function confirmDeletion(date) {
            return confirm("Are you sure you want to remove the blocked day: " + date + "?");
        }
    </script>
</body>

</html>
