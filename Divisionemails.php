<?php
// Start session and check if admin is logged in
session_start();

if (!isset($_SESSION['EmployeeID'])) {
    header("Location: Login.php");
    exit();
}

// Include the database connection
include('Mysqlconnection.php');

// Handle the form submission to update division email
if (isset($_POST['update_email'])) {
    $division = $_POST['division'];
    $email = $_POST['email'];

    // Update query for the division email
    $updateQuery = "UPDATE division_emails SET email = ? WHERE division = ?";
    $stmt = $connection->prepare($updateQuery);
    $stmt->bind_param('ss', $email, $division);

    if ($stmt->execute()) {
        echo "Email updated successfully for $division!";
    } else {
        echo "Error updating email: " . $connection->error;
    }
}

// Fetch all divisions and their emails from the database
$query = "SELECT division, email FROM division_emails";
$result = $connection->query($query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Division Emails</title>
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

        input[type="text"] {
            padding: 10px;
            border: 1px solid #4CAF50;
            border-radius: 4px;
            width: 200px;
            margin-left: 10px;
        }

        input[type="submit"] {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-left: 10px;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }
    </style>
</head>

<body>
    <div class="menu-toggle" onclick="toggleSidebar()">&#9776;</div>

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

    <div class="container" id="main-container">
        <h2>Division Emails Management</h2>

        <table>
            <thead>
                <tr>
                    <th>Division</th>
                    <th>Email</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['division']); ?></td>
                        <td>
                            <form method="POST" action="">
                                <input type="hidden" name="division" value="<?php echo htmlspecialchars($row['division']); ?>">
                                <input type="text" name="email" value="<?php echo htmlspecialchars($row['email']); ?>">
                        </td>
                        <td>
                                <input type="submit" name="update_email" value="Update">
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
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
    </script>
</body>

</html>
