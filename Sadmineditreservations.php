<?php
include("Mysqlconnection.php");

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $query = "SELECT * FROM reservations WHERE invoicenumber = '$id'";
    $result = mysqli_query($connection, $query);
    $row = mysqli_fetch_assoc($result);
}

if (isset($_POST['update'])) {
    $invoicenumber = $_POST['invoicenumber'];
    $checkin = !empty($_POST['checkin']) ? $_POST['checkin'] : $row['checkin'];
    $checkout = !empty($_POST['checkout']) ? $_POST['checkout'] : $row['checkout'];
    $persons = $_POST['persons'];
    $requests = $_POST['requests'];

    // Fetch blocked dates
    $blocked_dates_query = "SELECT date FROM maintenance";
    $blocked_dates_result = mysqli_query($connection, $blocked_dates_query);
    $blocked_dates = [];
    while ($blocked_row = mysqli_fetch_assoc($blocked_dates_result)) {
        $blocked_dates[] = $blocked_row['date'];
    }

    // Compare original dates with new ones
    $original_checkin = $row['checkin'];
    $original_checkout = $row['checkout'];

    if ($checkin !== $original_checkin || $checkout !== $original_checkout) {
        // Check for blocked dates within the check-in and check-out range
        $blocked_in_range = false;
        $current_date = strtotime($checkin);
        $end_date = strtotime($checkout);

        // Skip the check-in date
        $current_date = strtotime('+1 day', $current_date);

        while ($current_date <= $end_date) {
            if (in_array(date('Y-m-d', $current_date), $blocked_dates)) {
                $blocked_in_range = true;
                break;
            }
            $current_date = strtotime('+1 day', $current_date);
        }

        if ($blocked_in_range) {
            echo "There are blocked dates within the selected check-in and check-out dates. Please select different dates.";
        } else {
            // If no blocked dates, proceed with the update
            $query = "UPDATE reservations SET checkin='$checkin', checkout='$checkout', persons='$persons', requests='$requests' WHERE invoicenumber='$invoicenumber'";
            mysqli_query($connection, $query);
            header("Location: Superadminreservations.php");
        }
    } else {
        // If dates haven't changed, directly update the other fields
        $query = "UPDATE reservations SET persons='$persons', requests='$requests' WHERE invoicenumber='$invoicenumber'";
        mysqli_query($connection, $query);
        header("Location: Superadminreservations.php");
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Reservation</title>
    <link rel="stylesheet" type="text/css" href="css/Booking.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        .dashboard-button {
            background-color: green;
            color: white;
            padding: 10px 20px;
            text-align: center;
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            border: none;
            border-radius: 5px;
        }

        .button-container {
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <h2>Edit Reservation</h2>
    <form action="Sadmineditreservations.php?id=<?php echo $row['invoicenumber']; ?>" method="post" class="booking-form">
        <input type="hidden" name="invoicenumber" value="<?php echo $row['invoicenumber']; ?>">
        <div class="form-group">
            <label for="checkin">Check-in Date:</label>
            <input type="text" id="checkin" name="checkin" value="<?php echo $row['checkin']; ?>" >
        </div>
        <div class="form-group">
            <label for="checkout">Check-out Date:</label>
            <input type="text" id="checkout" name="checkout" value="<?php echo $row['checkout']; ?>" >
        </div>
        <div class="form-group">
            <label for="persons">Persons:</label>
            <input type="number" id="persons" name="persons" value="<?php echo $row['persons']; ?>" >
        </div>
        <div class="form-group">
            <label for="requests">Requests:</label>
            <textarea id="requests" name="requests" rows="4" cols="50"><?php echo $row['requests']; ?></textarea>
        </div>
        <div class="form-group">
            <button type="submit" name="update">Update</button>
        </div>
    </form>

    <div class="button-container">
        <a href="Superadminreservations.php" class="dashboard-button">Go to Dashboard</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        // Reserved dates fetched from the server-side
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
            disable: disabledDates,
            onChange: function (selectedDates, dateStr, instance) {
                if (selectedDates.length > 0) {
                    const checkinDate = selectedDates[0];
                    const maxCheckoutDate = new Date(checkinDate);
                    maxCheckoutDate.setDate(maxCheckoutDate.getDate() + 7); // Add 7 days

                    // Re-initialize checkout flatpickr with updated date range
                    flatpickr("#checkout", {
                        dateFormat: "Y-m-d",
                        altInput: true,
                        altFormat: "F j, Y",
                        allowInput: true,
                        minDate: dateStr,
                        maxDate: maxCheckoutDate,
                        disable: disabledDates, // Only apply the initially blocked dates, not the entire check-in range
                        onChange: function (selectedCheckoutDates, checkoutDateStr, checkoutInstance) {
                            if (selectedCheckoutDates.length > 0) {
                                const checkoutDate = selectedCheckoutDates[0];
                                if (checkBlockedDatesInRange(checkinDate, checkoutDate, reservedDates.blocked)) {
                                    alert("There are blocked dates within the selected check-in and check-out dates. Please select different dates.");
                                    checkoutInstance.clear();
                                }
                            }
                        }
                    }).clear(); // Ensure the field is cleared on re-initialization
                }
            }
        });

        // Initialize the checkout field with basic settings initially
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
