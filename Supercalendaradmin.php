<?php
session_start();
require 'Mysqlconnection.php'; // Include the MySQL connection file

if (!isset($_SESSION['EmployeeID'])) {
    header("Location: Login.php");
    exit();
}

if (isset($_POST['block_dates']) && !empty($_POST['block_dates'])) {
    $reason = $_POST['reason'];
    $blocked_dates = $_POST['block_dates'];
    $blocked_dates_array = explode(",", $blocked_dates);

    foreach ($blocked_dates_array as $blocked_date) {
        $sql = "INSERT INTO maintenance (date, reason) VALUES ('$blocked_date', '$reason') ON DUPLICATE KEY UPDATE reason = '$reason'";
        if (!mysqli_query($connection, $sql)) {
            die("Error updating record: " . mysqli_error($connection));
        }
    }
    echo '<script>
    window.location.href="Superadmindashboard.php";
    alert("Dates blocked successfully!");
</script>';
    exit();
}

$reservations_result = mysqli_query($connection, "SELECT checkin, checkout FROM reservations");
$maintenance_result = mysqli_query($connection, "SELECT date FROM maintenance");

$reserved_dates = [];
while ($row = mysqli_fetch_assoc($reservations_result)) {
    $checkin = date('Y-m-d', strtotime($row['checkin']));
    $checkout = date('Y-m-d', strtotime($row['checkout']));
    $reserved_dates[] = ['from' => $checkin, 'to' => $checkout];
}

$blocked_dates = [];
while ($row = mysqli_fetch_assoc($maintenance_result)) {
    $blocked_dates[] = $row['date'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Calendar</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f8f5;
            margin: 0;
            padding: 0;
        }

        h2 {
            text-align: center;
            color: #2f8f2f;
            margin-bottom: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-bottom: 20px;
        }

        form {
            padding: 1em;
        }

        .form-container {
            width: 50%;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-top: 40px;
        }

        label {
            color: #2f8f2f;
            font-weight: bold;
        }

        input[type="text"] {
            width: 100%;
            padding: 10px;
            border: 2px solid #2f8f2f;
            border-radius: 4px;
            outline: none;
        }

        input[type="text"]:focus {
            border-color: #228b22;
        }

        button {
            background-color: #2f8f2f;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background-color: #228b22;
        }

        .menu-toggle {
            position: fixed;
            top: 10px;
            left: 10px;
            font-size: 24px;
            cursor: pointer;
            color: #4CAF50;
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

        .container {
            padding: 20px;
            transition: margin-left 0.3s;
            margin-left: 250px;
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

            </ul>
        </nav>
    </div>

    <div class="container" id="main-container">
        <h2>Admin Calendar</h2>
        <div class="form-container">
            <form method="post">
                <div class="form-group">
                    <label for="block_dates">Select Dates to Block for Maintenance:</label>
                    <input type="text" id="block_dates" name="block_dates" placeholder="Select dates">
                </div>
                <div class="form-group">
                    <label for="reason">Reason for Maintenance:</label>
                    <input type="text" id="reason" name="reason" placeholder="Enter reason">
                </div>
                <button type="submit">Block Dates</button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        const reservedDates = <?php echo json_encode($reserved_dates); ?>;
        const blockedDates = <?php echo json_encode($blocked_dates); ?>;

        function getDisabledDates(dates) {
            const disabled = [];
            dates.forEach(range => {
                const start = new Date(range.from);
                const end = new Date(range.to);
                let current = new Date(start);
                while (current <= end) {
                    disabled.push(current.toISOString().split('T')[0]);
                    current.setDate(current.getDate() + 1);
                }
            });
            return disabled;
        }

        const disabledDates = getDisabledDates(reservedDates).concat(blockedDates);

        flatpickr("#block_dates", {
            mode: "multiple",
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "F j, Y",
            allowInput: true,
            minDate: "today",
            disable: disabledDates,
            onDayCreate: function (dObj, dStr, fp, dayElem) {
                const dateStr = dayElem.dateObj.toISOString().split('T')[0];
                if (blockedDates.includes(dateStr)) {
                    dayElem.classList.add("blocked-date");
                }
            }
        });

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
