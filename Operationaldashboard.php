<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        /* General styling for the table */
        table {
            width: 100%;
            border-collapse: collapse;
            border: 2px solid #4CAF50; /* Green border for the table */
            border-radius: 8px; /* Border radius for rounded corners */
            overflow: hidden; /* Ensures rounded corners are visible */
        }

        /* Styling for table headers */
        th {
            background-color: #4CAF50; /* Green background for headers */
            color: white; /* White text color for headers */
            padding: 12px; /* Padding for headers */
        }

        /* Styling for table data cells */
        td {
            border: 1px solid #4CAF50; /* Green border for cells */
            padding: 10px; /* Padding for cells */
            text-align: left; /* Align text to the left */
        }

        /* Alternating row colors for the table */
        tr:nth-child(even) {
            background-color: #e8f5e9; /* Light green for even rows */
        }

        tr:nth-child(odd) {
            background-color: #f9f9f9; /* Light grey for odd rows */
        }

        /* Styling for action buttons */
        .action-button {
            cursor: pointer;
            text-decoration: none;
            padding: 0.4em 0.8em; /* Increased padding for better spacing */
            border: solid 1px black;
            border-radius: 0.5em;
            color: white; /* White text color for buttons */
            display: inline-block; /* Ensures buttons have spacing */
            margin-top: 5px; /* Space between buttons */
        }

        /* Specific styling for different button types */
        .delete-button {
            background-color: #ed4239; /* Red for delete buttons */
        }

        .edit-button {
            background-color: #4CAF50; /* Green for edit buttons */
        }

        .update-button {
            background-color: #FFA500; /* Orange for update buttons */
        }

        /* Styling for navigation list */
        .nav-list {
            display: flex;
            gap: 3em;
        }

        /* Styling for navigation items */
        .nav-items {
            list-style-type: none;
        }

        .nav-items a {
            text-decoration: none;
            color: black;
        }

        /* Styling for member picture */
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
                            echo "<td><button class='action-button edit-button' disabled>Calculated</button><br/>";
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
