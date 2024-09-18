<?php
session_start();
require 'Mysqlconnection.php'; // Include the MySQL connection file

if (!isset($_SESSION['EmployeeID'])) {
    header("Location: Login.php");
    exit();
}

if (isset($_GET['id'])) {
    $invoicenumber = $_GET['id'];
    $sql = "SELECT * FROM reservations WHERE invoicenumber='$invoicenumber'";
    $result = mysqli_query($connection, $sql);

    if (!$result) {
        die("Query failed: " . mysqli_error($connection));
    }

    $row = mysqli_fetch_assoc($result);
    if (!$row) {
        header("Location: reservations.php");
        exit();
    }
} else {
    header("Location: reservations.php");
    exit();
}

if (isset($_POST['update'])) {
    $checkin = !empty($_POST['checkin']) ? $_POST['checkin'] : $row['checkin'];
    $checkout = !empty($_POST['checkout']) ? $_POST['checkout'] : $row['checkout'];
    $persons = $_POST['persons'];
    $requests = $_POST['requests'];
    $EmployeeID = $_SESSION['EmployeeID'];

    $sql = "UPDATE reservations SET checkin='$checkin', checkout='$checkout', persons='$persons', requests='$requests' WHERE invoicenumber='$invoicenumber'";
    if (mysqli_query($connection, $sql)) {
        // Check if there is an existing log entry for this EmployeeID and invoicenumber
        $log_sql = "SELECT * FROM update_logs WHERE EmployeeID='$EmployeeID' AND invoicenumber='$invoicenumber'";
        $log_result = mysqli_query($connection, $log_sql);

        if (mysqli_num_rows($log_result) > 0) {
            // If log entry exists, update the update_count
            $log_row = mysqli_fetch_assoc($log_result);
            $update_count = $log_row['update_count'] + 1;
            $update_log_sql = "UPDATE update_logs SET update_count='$update_count', update_date=CURRENT_TIMESTAMP WHERE log_id='{$log_row['log_id']}'";
            mysqli_query($connection, $update_log_sql);
        } else {
            // If no log entry exists, insert a new log entry
            $insert_log_sql = "INSERT INTO update_logs (EmployeeID, invoicenumber, update_count) VALUES ('$EmployeeID', '$invoicenumber', 1)";
            mysqli_query($connection, $insert_log_sql);
        }

        header("Location: Reservations.php");
        exit();
    } else {
        die("Error updating record: " . mysqli_error($connection));
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Reservation</title>
    <link rel="stylesheet" type="text/css" href="css/Booking.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <h2>Update Reservation</h2>
    <form class="booking-form" method="post">
        <div class="form-group">
            <label for="checkin">Check-in Date:</label>
            <input type="text" id="checkin" name="checkin" value="<?php echo htmlspecialchars($row['checkin']); ?>" >
        </div>
        <div class="form-group">
            <label for="checkout">Check-out Date:</label>
            <input type="text" id="checkout" name="checkout" value="<?php echo htmlspecialchars($row['checkout']); ?>" >
        </div>
        <div class="form-group">
            <label for="persons">Number of guests:</label>
            <input type="number" id="persons" name="persons" value="<?php echo htmlspecialchars($row['persons']); ?>" >
        </div>
        <div class="form-group">
            <label for="requests">Special Requests:</label>
            <textarea id="requests" name="requests" rows="4" cols="50"><?php echo htmlspecialchars($row['requests']); ?></textarea>
        </div>
        <div class="form-group">
            <button type="submit" name="update">Update</button>
        </div>
    </form>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        // Reserved dates and blocked dates fetched from the server-side
        const reservedDates = <?php
        $reservations_result = mysqli_query($connection, "SELECT checkin, checkout FROM reservations");
        $maintenance_result = mysqli_query($connection, "SELECT date FROM maintenance");

        $dates = [];
        while ($row = mysqli_fetch_assoc($reservations_result)) {
            $checkin = date('Y-m-d', strtotime($row['checkin']));
            $checkout = date('Y-m-d', strtotime($row['checkout']));
            $dates[] = ['from' => $checkin, 'to' => $checkout];
        }

        $blocked_dates = [];
        while ($row = mysqli_fetch_assoc($maintenance_result)) {
            $blocked_dates[] = $row['date'];
        }
        echo json_encode(['reservations' => $dates, 'blocked' => $blocked_dates]);
        ?>;

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

        const disabledDates = getDisabledDates(reservedDates.reservations).concat(reservedDates.blocked);

        function checkBlockedDatesInRange(start, end, blockedDates) {
            let current = new Date(start);
            current.setDate(current.getDate() + 1); // Start checking from the day after the check-in date
            while (current <= end) {
                if (blockedDates.includes(current.toISOString().split('T')[0])) {
                    return true;
                }
                current.setDate(current.getDate() + 1);
            }
            return false;
        }

        flatpickr("#checkin", {
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "F j, Y",
            allowInput: true,
            minDate: "today",
            maxDate: new Date().fp_incr(90), // Limit to 90 days from today
            disable: disabledDates,
            onChange: function (selectedDates, dateStr, instance) {
                if (selectedDates.length > 0) {
                    const checkinDate = selectedDates[0];
                    const maxCheckoutDate = new Date(checkinDate);
                    maxCheckoutDate.setDate(maxCheckoutDate.getDate() + 7); // Add 7 days

                    const checkoutPicker = flatpickr("#checkout", {
                        dateFormat: "Y-m-d",
                        altInput: true,
                        altFormat: "F j, Y",
                        allowInput: true,
                        minDate: dateStr,
                        maxDate: maxCheckoutDate > new Date().fp_incr(90) ? new Date().fp_incr(90) : maxCheckoutDate,
                        disable: disabledDates,
                        onChange: function (selectedCheckoutDates, checkoutDateStr, checkoutInstance) {
                            if (selectedCheckoutDates.length > 0) {
                                const checkoutDate = selectedCheckoutDates[0];
                                if (checkBlockedDatesInRange(checkinDate, checkoutDate, reservedDates.blocked)) {
                                    alert("There are blocked dates within the selected check-in and check-out dates. Please select different dates.");
                                    checkoutInstance.clear();
                                }
                            }
                        }
                    });
                }
            }
        });

        flatpickr("#checkout", {
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "F j, Y",
            allowInput: true,
            minDate: "today",
            maxDate: new Date().fp_incr(90), // Limit to 90 days from today
            disable: disabledDates
        });
    </script>
</body>

</html>
