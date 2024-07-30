<?php
include ("Mysqlconnection.php");
if (isset($_POST['submit'])) {
    $EmployeeID = $_POST['EmployeeID'];
    $Guestname = $_POST['Guestname'];
    $Email = $_POST['Email'];
    $Phone = $_POST['Phone'];
    $Password = $_POST['Password'];
    $cPassword = $_POST['cPassword'];


$sql = "select * from users where EmployeeID='$EmployeeID'";
$result = mysqli_query($connection, $sql);
$count_user = mysqli_num_rows($result);

if ($count_user == 0) {
    if ($Password == $cPassword) {
        $hash = password_hash($Password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users(EmployeeID,Guestname,Email,Phone,Password) VALUES('$EmployeeID','$Guestname','$Email','$Phone','$Password')";
        $result = mysqli_query($connection, $sql);
        if ($result) {
            header("Location: Login.php");
        }
    }
} else {
    if ($count_user > 0) {
        echo '<script>
                window.location.href="Signup.php";
                alert("EmployeeID already exists!!");
            </script>';
    }
}
}
?>