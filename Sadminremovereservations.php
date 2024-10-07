<?php
// Include PHPMailer files
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;

include("Mysqlconnection.php");

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($connection, $_GET['id']);

    // Fetch reservation details before deletion
    $reservationQuery = "SELECT * FROM reservations WHERE invoicenumber = '$id'";
    $reservationResult = mysqli_query($connection, $reservationQuery);

    if (mysqli_num_rows($reservationResult) > 0) {
        $reservation = mysqli_fetch_assoc($reservationResult);
        $employeeID = $reservation['EmployeeID'];
        $checkin = $reservation['checkin'];
        $checkout = $reservation['checkout'];
        $persons = $reservation['persons'];
        $requests = $reservation['requests'];

        // Fetch the user's email from the users table
        $userQuery = "SELECT Email FROM users WHERE EmployeeID = '$employeeID'";
        $userResult = mysqli_query($connection, $userQuery);
        if (mysqli_num_rows($userResult) > 0) {
            $user = mysqli_fetch_assoc($userResult);
            $userEmail = $user['Email'];

            // Initialize PHPMailer
            $mail = new PHPMailer(true);

            try {
                //Server settings
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'denuwansathsara0412@gmail.com';
                $mail->Password = 'boaa moki kmax yzyz'; // Consider moving sensitive info to a secure environment
                $mail->SMTPSecure = 'ssl';
                $mail->Port = 465;                              // TCP port to connect to

                //Recipients
                $mail->setFrom('denuwansathsara0412@gmail.com', 'Denuwan');
                $mail->addAddress($userEmail);
                // Content
                $mail->isHTML(true);                                   // Set email format to HTML
                $mail->Subject = 'Reservation Deleted';
                $mail->Body = "Dear User,<br><br>The Superadmin has deleted your reservation (Invoice No: $id) for maintenance issues.<br><br>Please contact us for further information.<br><br>Best Regards,<br>Admin Team";
                $mail->AltBody = "Dear User,\n\nThe Superadmin has deleted your reservation (Invoice No: $id) for maintenance issues.\n\nPlease contact us for further information.\n\nBest Regards,\nAdmin Team";

                $mail->send();
                echo 'Message has been sent';
            } catch (Exception $e) {
                echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }
        }

        // Insert reservation into reservationhistories table with 'deleted' status
        $historyQuery = "INSERT INTO reservationhistories (invoicenumber, EmployeeID, checkin, checkout, persons, requests, status) 
                         VALUES ('$id', '$employeeID', '$checkin', '$checkout', '$persons', '$requests', 'deleted')";
        mysqli_query($connection, $historyQuery);

        // Delete the reservation from the reservations table
        $deleteQuery = "DELETE FROM reservations WHERE invoicenumber = '$id'";
        mysqli_query($connection, $deleteQuery);

        // Redirect back to the Superadminreservations page
        header("Location: Superadminreservations.php");
        exit();
    } else {
        echo "Reservation not found.";
    }
} else {
    echo "Invalid request.";
}
?>