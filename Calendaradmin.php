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
        $sql = "INSERT INTO maintenance (date,reason) VALUES ('$blocked_date','$reason') ON DUPLICATE KEY UPDATE date = date";
        if (!mysqli_query($connection, $sql)) {
            die("Error updating record: " . mysqli_error($connection));
        }
    }
    echo '<script>
    window.location.href="Admindashboard.php";
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
        h2 {
            text-align: center;
            margin-bottom: 20px;
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
    <nav>
        <ul class="nav-list">
            <li class="nav-items"><a href="/Banglow/Admindashboard.php">Users list</a></li>
            <li class="nav-items"><a href="/Banglow/Calendaradmin.php">Calendar</a></li>
            <li class="nav-items"><a href="/Banglow/Blocked.php">Blocked Days</a></li>
        </ul>
    </nav>
    <h2>Admin Calendar</h2>
    <form method="post">
        <div class="form-group">
            <label for="block_dates">Select Dates to Block for Maintenance:</label>
            <input type="text" id="block_dates" name="block_dates"><br /><br />
            <label for="reason">Reason for Maintenance:</label>
            <input type="text" id="reason" name="reason">
        </div>
        <button type="submit">Block Dates</button>
    </form>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        const reservedDates = <?php echo json_encode($reserved_dates); ?>;
        const blockedDates = <?php echo json_encode($blocked_dates); ?>;

        function isDateInRange(date, range) {
            const dateTime = date.getTime();
            return dateTime >= new Date(range.from).getTime() && dateTime <= new Date(range.to).getTime();
        }

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
    </script>
</body>

</html>