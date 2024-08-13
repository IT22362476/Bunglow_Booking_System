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
        .Addbtn {
            display: flex;
            justify-content: center;
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .Addbtn a {
            text-decoration: none;
            color: white;
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
        <nav>
            <ul class="nav-list">
                <li class="nav-items"><a href="/Banglow/Admindashboard.php">Users list</a></li>
                <li class="nav-items"><a href="/Banglow/Calendaradmin.php">Calendar</a></li>
                <li class="nav-items"><a href="/Banglow/Blocked.php">Blocked Days</a></li>
                <li class="nav-items"><a href="/Banglow/Adminreservations.php">Reservations</a></li>
            </ul>
        </nav>
        <table>
            <thead>
                <tr>
                    <th>UserID</th>
                    <th>EmployeeID</th>
                    <th>Guestname</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Picture</th> <!-- New column for the picture -->
                    <th>Remove</th>
                </tr>
            </thead>
            <tbody>
                <?php
                require 'Mysqlconnection.php'; // Include your database connection file
                
                // Query to select all data including the picture from the users table
                $sql = "SELECT UserID, EmployeeID, Guestname, Email, Phone, picture FROM users";
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

                        // Display picture with a link to open in a new tab
                        if (!empty($row['picture'])) {
                            echo "<td><a href='uploads/" . htmlspecialchars($row['picture']) . "' target='_blank'>";
                            echo "<img src='uploads/" . htmlspecialchars($row['picture']) . "' alt='Member Picture' class='member-picture'>";
                            echo "</a></td>";
                        } else {
                            echo "<td>No picture available</td>";
                        }

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
        <button class="Addbtn"><a href="Addmembers.php">Add Member</a></button>
    </div>
</body>
</html>
