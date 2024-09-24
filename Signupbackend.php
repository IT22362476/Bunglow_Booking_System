<?php
include("Mysqlconnection.php");
require 'PHPMailer/src/PHPMailer.php'; // Make sure to require PHPMailer files
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;

if (isset($_POST['submit'])) {
    $EmployeeID = $_POST['EmployeeID'];
    $Phone = $_POST['Phone'];
    $Password = $_POST['Password'];
    $cPassword = $_POST['cPassword'];

    // Check if the EmployeeID exists in the executives table
    $sql_executives = "SELECT Email, Name FROM executives WHERE EmployeeID='$EmployeeID'"; 
    $result_executives = mysqli_query($connection, $sql_executives);
    $count_executives = mysqli_num_rows($result_executives);

    if ($count_executives > 0) { // EmployeeID exists in executives table
        $row_executives = mysqli_fetch_assoc($result_executives);
        $Email = $row_executives['Email'];
        $Name = $row_executives['Name'];

        $sql_users = "SELECT * FROM users WHERE EmployeeID='$EmployeeID'";
        $result_users = mysqli_query($connection, $sql_users);
        $count_users = mysqli_num_rows($result_users);

        if ($count_users == 0) {
            if ($Password == $cPassword) {
                // Handle file upload
                $target_dir = "uploads/"; // Ensure this directory exists
                $file_name = basename($_FILES["Picture"]["name"]); // Get the file name
                $target_file = $target_dir . $file_name; // Full path for moving the file
                $uploadOk = 1;
                $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

                // Check if image file is an actual image or fake image
                $check = getimagesize($_FILES["Picture"]["tmp_name"]);
                if ($check !== false) {
                    // Check file size (e.g., limit to 5MB)
                    if ($_FILES["Picture"]["size"] > 5000000) {
                        echo '<script>alert("Sorry, your file is too large."); window.location.href="Signup.php";</script>';
                        $uploadOk = 0;
                    }
                } else {
                    echo '<script>alert("File is not an image."); window.location.href="Signup.php";</script>';
                    $uploadOk = 0;
                }

                // Allow certain file formats
                if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
                    echo '<script>alert("Sorry, only JPG, JPEG, PNG & GIF files are allowed."); window.location.href="Signup.php";</script>';
                    $uploadOk = 0;
                }

                // Check if $uploadOk is set to 0 by an error
                if ($uploadOk == 0) {
                    echo '<script>alert("Sorry, your file was not uploaded."); window.location.href="Signup.php";</script>';
                } else {
                    // Attempt to upload the file
                    if (move_uploaded_file($_FILES["Picture"]["tmp_name"], $target_file)) {
                        // Insert data into the users table, including only the file name
                        $sql_insert = "INSERT INTO users(EmployeeID, Name, Email, Phone, Password, Picture) VALUES('$EmployeeID', '$Name', '$Email', '$Phone', '$Password', '$file_name')";
                        $result_insert = mysqli_query($connection, $sql_insert);
                        if ($result_insert) {
                            // Send a success email to the user
                            $mail = new PHPMailer;
                            $mail->isSMTP();
                            $mail->Host = 'smtp.gmail.com';
                            $mail->SMTPAuth = true;
                            $mail->Username = 'denuwansathsara0412@gmail.com';
                            $mail->Password = 'boaa moki kmax yzyz'; // Ensure this is secure in production
                            $mail->SMTPSecure = 'ssl';
                            $mail->Port = 465;

                            $mail->setFrom('denuwansathsara0412@gmail.com', 'Denuwan');
                            $mail->addAddress($Email);

                            $mail->isHTML(true);
                            $mail->Subject = 'Bungalow Booking Account Created';
                            $mail->Body = "<h1>Account Created Successfully</h1>
                                           <p>Dear $Name,</p>
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
                        echo '<script>alert("Sorry, there was an error uploading your file."); window.location.href="Signup.php";</script>';
                    }
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
