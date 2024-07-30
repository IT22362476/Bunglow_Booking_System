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
    $checkin = $_POST['checkin'];
    $checkout = $_POST['checkout'];
    $persons = $_POST['persons'];
    $requests = $_POST['requests'];

    $sql = "UPDATE reservations SET checkin='$checkin', checkout='$checkout', persons='$persons', requests='$requests' WHERE invoicenumber='$invoicenumber'";
    if (mysqli_query($connection, $sql)) {
        header("Location: reservations.php");
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
            <input type="text" id="checkin" name="checkin" value="<?php echo htmlspecialchars($row['checkin']); ?>">
        </div>
        <div class="form-group">
            <label for="checkout">Check-out Date:</label>
            <input type="text" id="checkout" name="checkout" value="<?php echo htmlspecialchars($row['checkout']); ?>">
        </div>
        <div class="form-group">
            <label for="persons">Number of guests:</label>
            <input type="number" id="persons" name="persons" value="<?php echo htmlspecialchars($row['persons']); ?>">
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
        // Reserved dates fetched from the server-side
        const reservedDates = <?php
        $result = mysqli_query($connection, "SELECT checkin, checkout FROM reservations");
        $dates = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $checkin = date('Y-m-d', strtotime($row['checkin']));
            $checkout = date('Y-m-d', strtotime($row['checkout']));
            $dates[] = ['from' => $checkin, 'to' => $checkout];
        }
        echo json_encode($dates);
        ?>;

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

        const disabledDates = getDisabledDates(reservedDates);

        flatpickr("#checkin", {
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "F j, Y",
            allowInput: true,
            minDate: "today",
            disable: disabledDates,
            onChange: function (selectedDates, dateStr, instance) {
                if (selectedDates.length > 0) {
                    const checkinDate = selectedDates[0];
                    flatpickr("#checkout", {
                        dateFormat: "Y-m-d",
                        altInput: true,
                        altFormat: "F j, Y",
                        allowInput: true,
                        minDate: dateStr,
                        disable: disabledDates.concat([{ from: checkinDate, to: checkinDate }]), // Include the check-in date
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
            disable: disabledDates
        });
    </script>
</body>

</html>
