<?php
require 'Mysqlconnection.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $EmployeeID = $_SESSION['EmployeeID'];
    $newPassword = $_POST['NewPassword'];
    $confirmPassword = $_POST['ConfirmPassword'];

    if ($newPassword == $confirmPassword) {
        // Hash the new password
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

        // Update the password in the database
        $sql = "UPDATE users SET password='$hashedPassword' WHERE EmployeeID='$EmployeeID'";
        if (mysqli_query($connection, $sql)) {
            echo "Password reset successfully.";
            session_unset();
            session_destroy();
            header("Location: Login.php");
            exit();
        } else {
            echo "Failed to reset password. Please try again.";
        }
    } else {
        echo "Passwords do not match.";
    }
}
?>
