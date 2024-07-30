<?php
session_start();
require 'Mysqlconnection.php'; // Include the MySQL connection file

if (isset($_POST['EmployeeID']) && isset($_POST['Password'])) {
    function validate($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    $EmployeeID = validate($_POST['EmployeeID']);
    $Password = validate($_POST['Password']);

    if (empty($EmployeeID)) {
        header("Location: Login.php?error=Username is required");
        exit();
    } else if (empty($Password)) {
        header("Location: Login.php?error=Password is required");
        exit();
    } else {
        $sql = "SELECT * FROM users WHERE EmployeeID='$EmployeeID' AND Password='$Password'";
        $result = mysqli_query($connection, $sql);

        if (!$result) {
            die("Query failed: " . mysqli_error($connection));
        }

        if (mysqli_num_rows($result) === 1) {
            $row = mysqli_fetch_assoc($result);
            if ($row['EmployeeID'] === $EmployeeID && $row['Password'] === $Password) {
                $_SESSION['UserID'] = $row['UserID'];
                $_SESSION['EmployeeID'] = $row['EmployeeID'];

                if ($EmployeeID === 'SuperAdmin') {
                    header("Location: Superadmindashboard.php");
                }else if ($EmployeeID === 'Admin') {
                    header("Location: Admindashboard.php");
                }else if ($EmployeeID === 'Operational') {
                    header("Location: Operationaldashboard.php");
                } 
                else{
                    header("Location: Home.php");
                }
                exit();
            } else {
                header("Location: Login.php?error=Incorrect Username or Password");
                exit();
            }
        } else {
            header("Location: Login.php?error=Incorrect Username or Password");
            exit();
        }
    }
} else {
    header("Location: Login.php");
    exit();
}
?>