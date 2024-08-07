<?php
session_start();
require 'Mysqlconnection.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (isset($_POST['submit'])) {
    $email = $_POST['Email'];
    $sql = "SELECT * FROM users WHERE Email='$email'";
    $result = $connection->query($sql);

    if ($result->num_rows > 0) {
        $otp = rand(100000, 999999);
        $_SESSION['OTP'] = $otp;
        $_SESSION['Email'] = $email;

        $mail = new PHPMailer;
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'denuwansathsara0412@gmail.com';
        $mail->Password = 'boaa moki kmax yzyz';
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;

        $mail->setFrom('denuwansathsara0412@gmail.com', 'Denuwan');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'OTP for Password Reset';
        $mail->Body    = 'Your OTP for password reset is ' . $otp;

        if(!$mail->send()) {
            echo 'Message could not be sent. Mailer Error: ' . $mail->ErrorInfo;
        } else {
            header('Location: VerifyOTP.php');
        }
    } else {
        echo "<script>alert('Email not found'); window.location.href='Forgot.php';</script>";
    }
}
?>
