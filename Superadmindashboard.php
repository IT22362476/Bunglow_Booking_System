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
    </style>
</head>

<body>
    <div class="container">
        <table>
            <thead>
                <tr>
                    <th>UserID</th>
                    <th>EmployeeID</th>
                    <th>Guestname</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Edit</th>
                    <th>Remove</th>
                </tr>
            </thead>
            <tbody>
                <?php
                require 'Mysqlconnection.php'; // Include your database connection file
                
                // Query to select all data from the users table
                $sql = "SELECT UserID, EmployeeID, Guestname, Email, Phone FROM users";
                $result = mysqli_query($connection, $sql);

                if ($result) {
                    // Fetch and display data
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['UserID']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['EmployeeID']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['Guestname']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['Email']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['Phone']) . "</td>";
                        echo "<td><a href='Userupdate.php?UserID=" . htmlspecialchars($row['UserID']) . "' class='action-button edit-button'>Edit</a></td>";
                        echo "<td><a href='Userdelete.php?UserID=" . htmlspecialchars($row['UserID']) . "' class='action-button delete-button'>Delete</a></td>";
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