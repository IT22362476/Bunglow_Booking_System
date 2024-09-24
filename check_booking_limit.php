<?php
session_start();
include("Mysqlconnection.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $checkin = $_POST['checkin'];
    $EmployeeID = $_SESSION['EmployeeID'];

    // Extract the year from the check-in date
    $year = date('Y', strtotime($checkin));

    // Query the reservationhistories table to count bookings for the current year
    $query = "SELECT COUNT(*) as reservation_count FROM reservationhistories WHERE email = ? AND YEAR(checkin) = ? AND status = 'Completed'";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("ss", $email, $year);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row['reservation_count'] >= 2) {
        // If more than 2 bookings exist for the current year, return an error response
        echo json_encode(['success' => false, 'message' => 'You have already booked 2 reservations for this year.']);
    } else {
        // Proceed with booking
        echo json_encode(['success' => true]);
    }
    exit();
}
?>
