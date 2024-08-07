<?php
session_start();
if (!isset($_SESSION['Email'])) {
    header('Location: Forgot.php');
}

if (isset($_POST['submit'])) {
    $otp = $_POST['OTP'];
    if ($otp == $_SESSION['OTP']) {
        header('Location: ResetPassword.php');
    } else {
        echo "<script>alert('Invalid OTP'); window.location.href='VerifyOTP.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP</title>
    <link rel="stylesheet" type="text/css" href="css/Login.css">
</head>
<body>
    <h1>Verify OTP</h1>
    <div class="loginform">
        <form action="VerifyOTP.php" method="post">
            <div class="empInputContainer">
                <label for="">OTP</label>
                <input type="text" name="OTP" placeholder="Enter OTP" required><br>
            </div>
            <div class="empInputContainer center">
                <button type="submit" name="submit">Verify OTP</button>
            </div>
        </form>
    </div>
</body>
</html>
