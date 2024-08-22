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
        table {
            width: 100%;
            border-collapse: collapse;
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

        .not-signed-up {
            background-color: #f8d7da;
            color: #721c24;
        }

        .signed-up {
            background-color: #d4edda;
            color: #155724;
        }
    </style>
</head>

<body>
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
</body>

</html>