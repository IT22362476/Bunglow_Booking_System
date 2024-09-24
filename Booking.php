<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // User is not logged in, redirect to Login.php
    header("Location: /Banglow/Login.php");
    exit();
}

$timeout_duration = 3600;

if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $timeout_duration) {
    // Last activity was over an hour ago
    session_unset();     // Unset $_SESSION variable
    session_destroy();   // Destroy session data
    header("Location: Login.php?timeout=true");
    exit();
}

$_SESSION['LAST_ACTIVITY'] = time(); // Update last activity time stamp
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Form</title>
    <link rel="stylesheet" type="text/css" href="css/Booking.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <style>
        /* Styles for the countdown circle */
        .countdown-container {
            position: fixed;
            top: 10px;
            right: 10px;
            width: 80px;
            height: 80px;
        }

        .countdown-circle {
            width: 80px;
            height: 80px;
            position: relative;
            border-radius: 50%;
            background-color: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .countdown-circle svg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            transform: rotate(-90deg);
        }

        .countdown-circle circle {
            fill: none;
            stroke-width: 6;
            stroke-linecap: round;
        }

        .countdown-circle .background {
            stroke: #eee;
        }

        .countdown-circle .progress {
            stroke: red;
            stroke-dasharray: 251; /* 2 * π * radius (40 - stroke-width) */
            stroke-dashoffset: 0;
            transition: stroke-dashoffset 1s linear;
        }

        .countdown-number {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 18px;
            font-weight: bold;
        }
    </style>
</head>

<body>

    <!-- Countdown Timer Circle -->
    <div class="countdown-container">
        <div class="countdown-circle">
            <svg>
                <circle class="background" cx="40" cy="40" r="37"></circle>
                <circle class="progress" cx="40" cy="40" r="37"></circle>
            </svg>
            <div class="countdown-number" id="countdown-number">120</div>
        </div>
    </div>

    <!-- Booking Form -->
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
        // Countdown Timer JavaScript
        const countdownNumberEl = document.getElementById('countdown-number');
        const progressCircle = document.querySelector('.countdown-circle .progress');
        let countdown = 120; // 120 seconds
        const interval = 1000; // 1 second
        const total = countdown;

        const countdownInterval = setInterval(() => {
            countdownNumberEl.textContent = countdown;

            // Calculate the stroke offset for the countdown animation
            const strokeDashOffset = 251 * (1 - countdown / total);
            progressCircle.style.strokeDashoffset = strokeDashOffset;

            // If countdown reaches zero, stop the timer and redirect
            if (countdown === 0) {
                clearInterval(countdownInterval);
                window.location.href = 'index.php'; // Redirect to index.php after countdown finishes
            }

            countdown--;
        }, interval);

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

        /**
         * Function to get all disabled dates
         * @param {Array} dates - Array of reservation date ranges.
         * @returns {Array} - Array of individual dates to disable.
         */
        function getDisabledDates(dates) {
            const disabled = [];
            dates.forEach(range => {
                const start = new Date(range.from);
                const end = new Date(range.to);
                let current = new Date(start);
                while (current <= end) {
                    disabled.push(current.toISOString().split('T')[0]); // Push each date as 'YYYY-MM-DD'
                    current.setDate(current.getDate() + 1);
                }
            });
            return disabled;
        }

        /**
         * Function to check if any blocked dates exist in the selected range.
         * @param {Date} start - The check-in date.
         * @param {Date} end - The check-out date.
         * @param {Array} blockedDates - Array of blocked dates (YYYY-MM-DD).
         * @returns {Boolean} - True if blocked dates exist in range, false otherwise.
         */
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

        // Fetch disabled dates for Flatpickr from server data
        const disabledDates = getDisabledDates(reservedDates.reservations).concat(reservedDates.blocked);

        // Configure Flatpickr
        flatpickr("#checkin", {
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "F j, Y",
            allowInput: true,
            minDate: "today",
            maxDate: new Date().fp_incr(90), // Limit to 90 days from today
            disable: disabledDates, // Disable reserved/blocked dates
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
                        disable: disabledDates, // Disable reserved/blocked dates
                        onChange: function (selectedCheckoutDates, checkoutDateStr, checkoutInstance) {
                            if (selectedCheckoutDates.length > 0) {
                                const checkoutDate = selectedCheckoutDates[0];

                                // Ensure check-out date is not the same as the check-in date
                                if (checkinDate.getTime() === checkoutDate.getTime()) {
                                    alert('Check-out date must be at least one day after the check-in date.');
                                    checkoutInstance.clear();
                                } else if (checkBlockedDatesInRange(checkinDate, checkoutDate, reservedDates.blocked)) {
                                    alert('Selected range contains blocked dates. Please choose another range.');
                                    checkoutInstance.clear();
                                }
                            }
                        }
                    });
                }
            }
        });
    </script>
</body>

</html>
