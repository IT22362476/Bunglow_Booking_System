<?php
session_start();
require 'Mysqlconnection.php'; // Include your MySQL connection file

// if (!isset($_SESSION['AdminID'])) { // Replace 'AdminID' with the correct session variable for admin
//     header("Location: Login.php");
//     exit();
// }

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
    <style>
        table {
            border-collapse: collapse;
            margin: 0 auto;
        }

        th,
        td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
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

        .nav-list {
            display: flex;
            gap: 3em;
        }

        .nav-items {
            list-style-type: none;
        }

        .nav-items a {
            text-decoration: none;
            color: black;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- <nav>
            <ul class="nav-list">
                <li class="nav-items"><a href="/Banglow/Admindashboard.php">Users list</a></li>
                <li class="nav-items"><a href="/Banglow/Calendaradmin.php">Calendar</a></li>
                <li class="nav-items"><a href="/Banglow/Blocked.php">Blocked Days</a></li>
            </ul>
        </nav> -->
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Reason</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['date']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['reason']) . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='2'>No blocked days found</td></tr>";
                }
                // Close the database connection
                mysqli_close($connection);
                ?>
            </tbody>
        </table>
    </div>
</body>

</html>
