<?php
session_start(); // Start the session to access session variables
include("Mysqlconnection.php");

// Function to check if dates overlap
function datesOverlap($start1, $end1, $start2, $end2) {
    return $start1 <= $end2 && $start2 <= $end1;
}

if (isset($_POST['submit'])) {
    $checkin = $_POST['checkin'];
    $checkout = $_POST['checkout'];
    $persons = $_POST['persons'];
    $requests = $_POST['requests'];

    // Convert dates to YYYY-MM-DD format if needed
    $checkin = date('Y-m-d', strtotime($checkin));
    $checkout = date('Y-m-d', strtotime($checkout));

    // Fetch existing reservations
    $sql = "SELECT checkin, checkout FROM reservations";
    $result = mysqli_query($connection, $sql);

    if ($result) {
        $isAvailable = true;

        while ($row = mysqli_fetch_assoc($result)) {
            $existingCheckin = $row['checkin'];
            $existingCheckout = $row['checkout'];

            if (datesOverlap($checkin, $checkout, $existingCheckin, $existingCheckout)) {
                $isAvailable = false;
                break;
            }
        }

        if ($isAvailable) {
            // Retrieve the EmployeeID from the session
            if (isset($_SESSION['EmployeeID'])) {
                $EmployeeID = $_SESSION['EmployeeID'];

                // Insert the new reservation if available, including EmployeeID
                $sql = "INSERT INTO reservations (EmployeeID, checkin, checkout, persons, requests) VALUES ('$EmployeeID', '$checkin', '$checkout', '$persons', '$requests')";
                $result = mysqli_query($connection, $sql);
                if ($result) {
                    echo '<script>
                            alert("Booking successful!!");
                            window.location.href="Home.php";
                        </script>';
                } else {
                    echo '<script>alert("Error while booking. Please try again.");</script>';
                }
            } else {
                echo '<script>alert("EmployeeID not found in session. Please log in again.");</script>';
            }
        } else {
            echo '<script>
            alert("Selected dates are already reserved. Please choose different dates.");
            window.location.href="Booking.php";
            </script>';
        }
    } else {
        echo '<script>alert("Error fetching reservations. Please try again.");</script>';
    }
}
?>
