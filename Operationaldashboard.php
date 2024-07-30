<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
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

        .update-button {
            background-color: #FFA500;
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
        <table>
            <thead>
                <tr>
                    <th>invoicenumber</th>
                    <th>EmployeeID</th>
                    <th>checkin</th>
                    <th>checkout</th>
                    <th>persons</th>
                    <th>requests</th>
                    <th>Bill</th>
                </tr>
            </thead>
            <tbody>
                <?php
                require 'Mysqlconnection.php'; // Include your database connection file

                // Query to select all data from the users table
                $sql = "SELECT r.invoicenumber, r.EmployeeID, r.checkin, r.checkout, r.persons, r.requests, b.totalBill 
                        FROM reservations r
                        LEFT JOIN bills b ON r.invoicenumber = b.invoicenumber AND r.EmployeeID = b.EmployeeID";
                $result = mysqli_query($connection, $sql);

                if ($result) {
                    // Fetch and display data
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['invoicenumber']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['EmployeeID']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['checkin']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['checkout']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['persons']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['requests']) . "</td>";
                        if (isset($row['totalBill'])) {
                            echo "<td><button class='action-button edit-button' disabled>Calculated</button>";
                            echo "<a href='Updatebill.php?invoicenumber=" . htmlspecialchars($row['invoicenumber']) . "&EmployeeID=" . htmlspecialchars($row['EmployeeID']) . "' class='action-button update-button'>Update</a></td>";
                        } else {
                            echo "<td><a href='Calculatebill.php?invoicenumber=" . htmlspecialchars($row['invoicenumber']) . "&EmployeeID=" . htmlspecialchars($row['EmployeeID']) . "' class='action-button edit-button'>Calculate</a></td>";
                        }
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
    </div>
</body>
</html>
