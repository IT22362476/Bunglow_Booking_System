<?php
include ("Mysqlconnection.php");
require 'PHPMailer/src/PHPMailer.php'; // Make sure to require PHPMailer files
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;

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
                    // Send a success email to the user
                    $mail = new PHPMailer;
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'denuwansathsara0412@gmail.com';
                    $mail->Password = 'boaa moki kmax yzyz';
                    $mail->SMTPSecure = 'ssl';
                    $mail->Port = 465;
            
                    $mail->setFrom('denuwansathsara0412@gmail.com', 'Denuwan');
                    $mail->addAddress($Email);
            
                    $mail->isHTML(true);

                    $mail->isHTML(true);
                    $mail->Subject = 'Bungalow Booking Account Created';
                    $mail->Body = "<h1>Account Created Successfully</h1>
                                   <p>Dear $Guestname,</p>
                                   <p>You have successfully created a Bungalow Booking account.</p>
                                   <p>Thank you for joining us!</p>";

                    if ($mail->send()) {
                        echo '<script>
                                alert("Signup successful! Confirmation email sent.");
                                window.location.href="Login.php";
                              </script>';
                    } else {
                        echo '<script>
                                alert("Signup successful, but failed to send confirmation email.");
                                window.location.href="Login.php";
                              </script>';
                    }
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
                alert("EmployeeID does not exist! Contact administrator or HR.");
              </script>';
    }
}
?>
