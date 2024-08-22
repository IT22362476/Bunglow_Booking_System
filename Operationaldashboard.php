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

        .member-picture {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 50%;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">
        <table>
            <thead>
                <tr>
                    <th>Invoice Number</th>
                    <th>Employee ID</th>
                    <th>Check-In</th>
                    <th>Check-Out</th>
                    <th>Persons</th>
                    <th>Requests</th>
                    <th>Bill</th>
                    <th>Picture</th>
                </tr>
            </thead>
            <tbody>
                <?php
                require 'Mysqlconnection.php'; // Include your database connection file

                // Query to select all data including the picture from the users table
                $sql = "SELECT r.invoicenumber, r.EmployeeID, r.checkin, r.checkout, r.persons, r.requests, b.totalBill, u.picture 
                        FROM reservations r
                        LEFT JOIN bills b ON r.invoicenumber = b.invoicenumber AND r.EmployeeID = b.EmployeeID
                        LEFT JOIN users u ON r.EmployeeID = u.EmployeeID"; // Join with users table to get the picture
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

                        // Display bill buttons
                        if (isset($row['totalBill'])) {
                            echo "<td><button class='action-button edit-button' disabled>Calculated</button>";
                            echo "<a href='Updatebill.php?invoicenumber=" . htmlspecialchars($row['invoicenumber']) . "&EmployeeID=" . htmlspecialchars($row['EmployeeID']) . "' class='action-button update-button'>Update</a></td>";
                        } else {
                            echo "<td><a href='Calculatebill.php?invoicenumber=" . htmlspecialchars($row['invoicenumber']) . "&EmployeeID=" . htmlspecialchars($row['EmployeeID']) . "' class='action-button edit-button'>Calculate</a></td>";
                        }

                        // Display picture with a link to open in a new tab
                        if (!empty($row['picture'])) {
                            echo "<td><a href='uploads/" . htmlspecialchars($row['picture']) . "' target='_blank'>";
                            echo "<img src='uploads/" . htmlspecialchars($row['picture']) . "' alt='Member Picture' class='member-picture'>";
                            echo "</a></td>";
                        } else {
                            echo "<td>No picture available</td>";
                        }

                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='8'>No data found</td></tr>";
                }

                // Close the database connection
                mysqli_close($connection);
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
