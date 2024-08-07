<?php
session_start();
require 'Mysqlconnection.php';

if (!isset($_SESSION['Email'])) {
    header('Location: Forgot.php');
}

if (isset($_POST['submit'])) {
    $new_password = $_POST['Password'];
    $email = $_SESSION['Email'];
    $sql = "UPDATE users SET Password='$new_password' WHERE Email='$email'";

    if ($connection->query($sql) === TRUE) {
        echo "<script>alert('Password updated successfully'); window.location.href='Login.php';</script>";
    } else {
        echo "Error updating record: " . $connection->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" type="text/css" href="css/Login.css">
</head>
<body>
    <h1>Reset Password</h1>
    <div class="loginform">
        <form action="ResetPassword.php" method="post">
            <div class="empInputContainer">
                <label for="">New Password</label>
                <input type="password" name="Password" placeholder="Enter new password" required><br>
            </div>
            <div class="empInputContainer center">
                <button type="submit" name="submit">Reset Password</button>
            </div>
        </form>
    </div>
</body>
</html>
