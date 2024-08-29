<?php
session_start(); // Start the session to access session variables
include("Mysqlconnection.php");
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;

// Function to check if dates overlap
function datesOverlap($start1, $end1, $start2, $end2) {
    return $start1 <= $end2 && $start2 <= $end1;
}

// Function to send email
function sendReservationEmail($to, $subject, $body) {
    $mail = new PHPMailer;
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'denuwansathsara0412@gmail.com';
    $mail->Password = 'boaa moki kmax yzyz';
    $mail->SMTPSecure = 'ssl';
    $mail->Port = 465;

    $mail->setFrom('denuwansathsara0412@gmail.com', 'Denuwan');  
    $mail->addAddress($to);

    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body    = $body;

    return $mail->send();
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
                $sql = "INSERT INTO reservations (EmployeeID, checkin, checkout, persons, requests) 
                        VALUES ('$EmployeeID', '$checkin', '$checkout', '$persons', '$requests')";
                $result = mysqli_query($connection, $sql);

                if ($result) {
                    // Get the autogenerated invoicenumber
                    $invoicenumber = mysqli_insert_id($connection);

                    // Fetch the user's email
                    $userEmailQuery = "SELECT Email FROM users WHERE EmployeeID = '$EmployeeID'";
                    $userEmailResult = mysqli_query($connection, $userEmailQuery);
                    if ($userEmailResult && mysqli_num_rows($userEmailResult) > 0) {
                        $userEmailRow = mysqli_fetch_assoc($userEmailResult);
                        $userEmail = $userEmailRow['Email'];

                        // Prepare email content
                        $subject = "Reservation Confirmation";
                        $body = "<h1>Reservation Confirmed</h1>
                                 <p>Dear Guest,</p>
                                 <p>Your reservation is confirmed.</p>
                                 <p><strong>Invoice Number:</strong> $invoicenumber</p>
                                 <p><strong>Check-in:</strong> $checkin</p>
                                 <p><strong>Check-out:</strong> $checkout</p>
                                 <p><strong>Number of Guests:</strong> $persons</p>
                                 <p><strong>Special Requests:</strong> $requests</p>";

                        // Send the email
                        if (sendReservationEmail($userEmail, $subject, $body)) {
                            echo '<script>
                                    alert("Booking successful! Confirmation email sent.");
                                    window.location.href="index.php";
                                </script>';
                        } else {
                            echo '<script>
                                    alert("Booking successful, but failed to send confirmation email.");
                                    window.location.href="index.php";
                                </script>';
                        }
                    } else {
                        echo '<script>alert("Error fetching user email. Please try again.");</script>';
                    }
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