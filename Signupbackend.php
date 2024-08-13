<?php
include ("Mysqlconnection.php");

if (isset($_POST['submit'])) {
    $EmployeeID = $_POST['EmployeeID'];
    $Guestname = $_POST['Guestname'];
    $Phone = $_POST['Phone'];
    $Password = $_POST['Password'];
    $cPassword = $_POST['cPassword'];

    // Check if the EmployeeID exists in the executives table
    $sql_executives = "SELECT Email, picture FROM executives WHERE EmployeeID='$EmployeeID'";
    $result_executives = mysqli_query($connection, $sql_executives);
    $count_executives = mysqli_num_rows($result_executives);

    if ($count_executives > 0) { // EmployeeID exists in executives table
        $row_executives = mysqli_fetch_assoc($result_executives);
        $Email = $row_executives['Email'];
        $Picture = $row_executives['picture'];

        $sql_users = "SELECT * FROM users WHERE EmployeeID='$EmployeeID'";
        $result_users = mysqli_query($connection, $sql_users);
        $count_users = mysqli_num_rows($result_users);

        if ($count_users == 0) {
            if ($Password == $cPassword) {
                // Insert data into the users table, including the picture
                $sql_insert = "INSERT INTO users(EmployeeID, Guestname, Email, Phone, Password, picture) VALUES('$EmployeeID', '$Guestname', '$Email', '$Phone', '$Password', '$Picture')";
                $result_insert = mysqli_query($connection, $sql_insert);
                if ($result_insert) {
                    header("Location: Login.php");
                } else {
                    echo "Error: " . mysqli_error($connection);
                }
            } else {
                echo '<script>
                        window.location.href="Signup.php";
                        alert("Passwords do not match!");
                      </script>';
            }
        } else {
            echo '<script>
                    window.location.href="Signup.php";
                    alert("EmployeeID already exists!!");
                  </script>';
        }
    } else {
        echo '<script>
                window.location.href="Signup.php";
                alert("EmployeeID does not defined!! contact administrator or HR");
              </script>';
    }
}
?>
