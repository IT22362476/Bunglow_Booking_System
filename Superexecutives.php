<?php
session_start();
require 'Mysqlconnection.php'; // Include the MySQL connection file

if (!isset($_SESSION['EmployeeID'])) {
    header("Location: Login.php");
    exit();
}

// Initialize the search term variable
$searchTerm = isset($_POST['search']) ? trim($_POST['search']) : '';

// Query to retrieve all executives from the executives table
$sql = "SELECT executives.EmployeeID, executives.Email, executives.Name,
        CASE WHEN users.EmployeeID IS NOT NULL THEN 'signed_up' ELSE 'not_signed_up' END AS status
        FROM executives
        LEFT JOIN users ON executives.EmployeeID = users.EmployeeID";

// Add search condition if a search term is provided
if ($searchTerm !== '') {
    $sql .= " WHERE executives.EmployeeID = '" . mysqli_real_escape_string($connection, $searchTerm) . "' 
               OR executives.Name LIKE '%" . mysqli_real_escape_string($connection, $searchTerm) . "%'";
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
            margin-left: 0; /* Start with no margin */
            padding: 20px;
            transition: margin-left 0.3s; /* Smooth transition */
        }

        .main-content.active {
            margin-left: 250px; /* Adjust for the sidebar width */
        }

        h1 {
            text-align: center;
            color: #333; /* Dark grey heading */
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
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

        /* Styling for the remove button */
        .remove-btn {
            background-color: #e74c3c; /* Red background for the button */
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .remove-btn:hover {
            background-color: #c0392b; /* Darker red on hover */
        }

        /* Search bar styling */
        .search-container {
            display: flex;
            justify-content: flex-end; /* Align to the right */
            margin-bottom: 20px;
        }

        .search-container input[type="text"] {
            padding: 10px;
            width: 200px; /* Fixed width for search input */
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .search-container button {
            padding: 10px;
            margin-left: 5px;
            background-color: #4CAF50; /* Green background for search button */
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .search-container button:hover {
            background-color: #45a049; /* Darker green on hover */
        }

        .addbtn {
            background-color: #2f8f2f;
            padding: 0.5em;
            margin-top: 1em;
            border-radius: 1em;
        }

        .addbtn a {
            color: white;
            text-decoration: none;
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
                <li class="nav-items"><a href="Divisionemails.php">Emails Management</a></li>


            </ul>
        </nav>
    </div>

    <!-- Main Content Section -->
    <div class="main-content" id="main-container">
        <div class="container">
            <h1>Executive List</h1>
            <button class="addbtn"><a href="Addmembers.php">Add member</a></button>

            <!-- Search Bar -->
            <div class="search-container">
                <form method="post" action="">
                    <input type="text" name="search" placeholder="Search by Employee ID or Name" value="<?php echo htmlspecialchars($searchTerm); ?>">
                    <button type="submit">Search</button>
                </form>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Employee ID</th>
                        <th>Name</th>
                        <th>Status</th>
                        <th>Remove</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                        <tr class="<?php echo $row['status'] === 'signed_up' ? 'signed-up' : 'not-signed-up'; ?>">
                            <td><?php echo htmlspecialchars($row['EmployeeID']); ?></td>
                            <td><?php echo htmlspecialchars($row['Name']); ?></td>
                            <td><?php echo $row['status'] === 'signed_up' ? 'Signed Up' : 'Not Signed Up'; ?></td>
                            <td>
                                <?php if (!in_array($row['EmployeeID'], ['Admin', 'SuperAdmin', 'Operational'])): ?>
                                    <form method="post" action="Executiveremove.php" onsubmit="return confirmRemove();">
                                        <input type="hidden" name="EmployeeID" value="<?php echo $row['EmployeeID']; ?>">
                                        <button type="submit" class="remove-btn">Remove</button>
                                    </form>
                                <?php else: ?>
                                    <span>N/A</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- JavaScript for Sidebar Toggle and Confirm Remove -->
    <script>
        // Function to toggle the sidebar
        function toggleSidebar() {
            var sidebar = document.getElementById('sidebar');
            var mainContainer = document.getElementById('main-container');
            sidebar.classList.toggle('active');
            mainContainer.classList.toggle('active');
        }

        // Function to confirm removal
        function confirmRemove() {
            return confirm("Are you sure you want to remove this executive?");
        }
    </script>
</body>

</html>
