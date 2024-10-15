<?php
session_start();
require 'Mysqlconnection.php';

// Redirect to login page if EmployeeID is not set in the session
if (!isset($_SESSION['EmployeeID'])) {
    header("Location: Login.php");
    exit();
}

// Set the session timeout limit (1 hour = 3600 seconds)
$timeout_duration = 3600;

if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $timeout_duration) {
    // Last activity was over an hour ago
    session_unset();     // Unset $_SESSION variable
    session_destroy();   // Destroy session data
    header("Location: Login.php?timeout=true");
    exit();
}

$_SESSION['LAST_ACTIVITY'] = time(); // Update last activity time stamp

// Retrieve the EmployeeID from the session
$employeeID = $_SESSION['EmployeeID'];

// Fetch user name from the database
$query = "SELECT Name FROM users WHERE EmployeeID = ?";
$stmt = $connection->prepare($query);
$stmt->bind_param("s", $employeeID);
$stmt->execute();
$stmt->bind_result($userName);
$stmt->fetch();
$stmt->close();

// Close the database connection
$connection->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coverpage</title>
    <link rel="stylesheet" type="text/css" href="css/Coverpage.css">
    <script>

        function toggleDropdown() {
            document.getElementById("profileDropdown").classList.toggle("show");
        }

        // Close the dropdown if the user clicks outside of it
        window.onclick = function (event) {
            if (!event.target.matches('.profile-img')) {
                var dropdowns = document.getElementsByClassName("dropdown-content");
                for (var i = 0; i < dropdowns.length; i++) {
                    var openDropdown = dropdowns[i];
                    if (openDropdown.classList.contains('show')) {
                        openDropdown.classList.remove('show');
                    }
                }
            }
        }
    </script>
    <style>
        /* Dropdown Styles */
        .dropdown {
            position: relative;
            display: inline-block;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
            border-radius: 10px;
            z-index: 1;
            right: 0;
        }

        .dropdown-content a {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }

        .dropdown-content a:hover {
            background-color: #f1f1f1;
        }

        .dropdown .show {
            display: block;
        }

        .profile-img {
            cursor: pointer;
            height: 30px;
            width: 30px;
        }

        /* Flexbox for center alignment */
        .user-info {
            display: flex;
            flex-direction: column;
            /* Aligns items in a column */
            align-items: center;
            /* Centers items horizontally */
            margin-top: 5px;
            /* Adds some space above the user name */
        }

        .user-name {
            font-size: small;
            /* Set the font size for the user's name */
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="container">
        <ul class="nav">
            <div class="part1">
                <li class="nav-list"><img src='./Images/CWMlogo.png' class="logo" /></li>
                <li class="Cname">C.W. Mackie PLC</li>
            </div>
            <div class="part2">
                <li class="nav-list"><a href="/Banglow/index.php">HOME</a></li>
                <li class="nav-list"><a href="/Banglow/Reservations.php">RESERVATION</a></li>
                <li class="nav-list"><a href="/Banglow/About.php">RULES & REGULATIONS</a></li>
            </div>
            <div class="part3">
                <li class="nav-list dropdown">
                    <!-- Center the user's name and icon -->
                    <div class="user-info">
                        <img src="./Images/image.png" class="profile-img" onclick="toggleDropdown()" />
                        <div id="profileDropdown" class="dropdown-content">
                            <a href="Profile.php">View Profile</a>
                            <a href="Logout.php">Logout</a>
                        </div>
                        <div class="user-name"><?php echo htmlspecialchars($userName); ?></div>
                    </div>
                </li>
                <li class="bookbtn">
                    <a href="/Banglow/Booking.php">
                        Book now
                    </a>
                </li>
            </div>
        </ul>

        <div class="coverpage-content">
            <h2>Book Now</h2>
            <p>Escape to the Tranquil Bungalow in Bandarawela - Where Memories are made</p>
        </div>
    </div>
</body>

</html>