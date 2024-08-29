<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // User is not logged in, redirect to Login.php
    header("Location: /Banglow/Login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Form</title>
    <link rel="stylesheet" type="text/css" href="css/Booking.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
</head>

<body>
    <form action="Bookingbackend.php" method="post" class="booking-form">
        <h2>Book Your Stay</h2>
        <div class="form-group">
            <label for="checkin">Check-in Date:</label>
            <input type="text" id="checkin" name="checkin" required>
        </div>
        <div class="form-group">
            <label for="checkout">Check-out Date:</label>
            <input type="text" id="checkout" name="checkout" required>
        </div>
        <div class="form-group">
            <label for="persons">Number of guests:</label>
            <input type="number" name="persons" required>
        </div>
        <div class="form-group">
            <label for="requests">Special Requests:</label>
            <textarea name="requests" rows="4" cols="50"></textarea>
        </div>
        <div class="form-group">
            <button type="submit" name="submit">Book Now</button>
        </div>
    </form>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        // Sample JavaScript logic for managing date selection
        const reservedDates = <?php
        include("Mysqlconnection.php");
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

                                // Check if check-in and check-out dates are the same
                                if (checkinDate.toISOString().split('T')[0] === checkoutDate.toISOString().split('T')[0]) {
                                    alert("Check-in and check-out dates cannot be the same. Please select different dates.");
                                    checkoutInstance.clear(); // Clear the checkout date if they are the same
                                    return;
                                }

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
