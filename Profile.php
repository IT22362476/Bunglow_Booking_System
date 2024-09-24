<?php
session_start();
require 'Mysqlconnection.php';

// Redirect to login page if UserID is not set in the session
if (!isset($_SESSION['UserID'])) {
    header("Location: Login.php");
    exit();
}

$userID = $_SESSION['UserID'];

// Handle form submission for updating user details
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $employeeID = $_POST['employeeID'];
    $guestname = $_POST['guestname'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $picture = $_POST['picture'];

    $updateSql = "UPDATE users SET EmployeeID=?, Guestname=?, Phone=?, Email=?, picture=? WHERE UserID=?";
    $updateStmt = $connection->prepare($updateSql);
    $updateStmt->bind_param("sssssi", $employeeID, $guestname, $phone, $email, $picture, $userID);
    $updateStmt->execute();
    $updateStmt->close();
    $message = "Profile updated successfully!";
} else {
    // Fetch user details if not form submission
    $sql = "SELECT EmployeeID, Guestname, Phone, Email, picture FROM users WHERE UserID = ?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $stmt->bind_result($employeeID, $guestname, $phone, $email, $picture);
    $stmt->fetch();
    $stmt->close();
}

$connection->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Page</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 20px;
        }

        .profile-container {
            background: white;
            max-width: 600px;
            margin: auto;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .profile-header {
            margin-bottom: 20px;
        }

        .profile-picture {
            width: 150px;
            height: 150px;
            border-radius: 75px;
            object-fit: cover;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }

        .profile-form label {
            font-weight: bold;
            display: block;
            margin-top: 10px;
        }

        .profile-form input[type="text"],
        .profile-form input[type="email"] {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .update-btn {
            background-color: #4CAF50; /* Green */
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 20px;
            display: block;
            width: 100%;
        }

        .update-btn:hover {
            background-color: #45a049;
        }

        .success {
            color: green;
            font-size: 16px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="profile-container">
        <?php if (isset($message)) echo "<p class='success'>$message</p>"; ?>
        <div class="profile-header">
            <img src="./Images/<?php echo htmlspecialchars($picture); ?>" alt="Profile Picture" class="profile-picture">
        </div>
        <form action="profile.php" method="post" class="profile-form">
            <label for="employeeID">Employee ID:</label>
            <input type="text" id="employeeID" name="employeeID" value="<?php echo htmlspecialchars($employeeID); ?>">

            <label for="guestname">Name:</label>
            <input type="text" id="guestname" name="guestname" value="<?php echo htmlspecialchars($guestname); ?>">

            <label for="phone">Phone:</label>
            <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($phone); ?>">

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>">

            <label for="picture">Picture Filename:</label>
            <input type="text" id="picture" name="picture" value="<?php echo htmlspecialchars($picture); ?>">

            <button type="submit" class="update-btn">Update</button>
        </form>
    </div>
</body>
</html>
