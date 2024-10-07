<?php
require 'Mysqlconnection.php'; // Include your database connection file

if (isset($_GET['UserID'])) {
    $userID = $_GET['UserID'];

    // Fetch the current user data
    $sql = "SELECT * FROM users WHERE UserID = ?";
    $stmt = mysqli_prepare($connection, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $userID);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
    } else {
        echo "User not found!";
        exit;
    }
} else {
    echo "Invalid request!";
    exit;
}

// Update the user data
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $userID = $_POST['UserID'];
    $employeeID = $_POST['EmployeeID'];
    $Name = $_POST['Name'];
    $email = $_POST['Email'];
    $phone = $_POST['Phone'];
    $Password = $_POST['Password'];
    

    $sql = "UPDATE users SET EmployeeID = ?, Name = ?, Email = ?, Phone = ?,Password = ? WHERE UserID = ?";
    $stmt = mysqli_prepare($connection, $sql);
    mysqli_stmt_bind_param($stmt, 'sssssi', $employeeID, $Name, $email, $phone,$Password ,$userID);

    if (mysqli_stmt_execute($stmt)) {
        echo "User updated successfully!";
        header("Location: Superadmindashboard.php"); // Redirect back to the table page
        exit;
    } else {
        echo "Error updating user: " . mysqli_error($connection);
    }
}

// Close the database connection
mysqli_close($connection);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <style>
        form {
            margin: 0 auto;
            width: 400px;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
        }

        input[type="text"],
        input[type="number"],
        input[type="email"] {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }

        button {
            padding: 10px 15px;
            background-color: #4CAF50;
            color: #fff;
            border: none;
            cursor: pointer;
            margin-top: 10px;
        }

        .back-button {
            display: inline-block;
            padding: 10px 15px;
            background-color: #4CAF50;
            color: #fff;
            text-align: center;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
            width: 200px; /* Set a fixed width for the button */
        }

        h2 {
            text-align: center;
        }

        .center-container {
            display: flex;
            flex-direction: column;
            align-items: center; /* Center align horizontally */
            margin-top: 20px; /* Optional: Add some space above */
        }
    </style>
</head>

<body>
    <h2>Edit User</h2>
    <form action="Userupdate.php?UserID=<?php echo htmlspecialchars($user['UserID']); ?>" method="post">
        <input type="hidden" name="UserID" value="<?php echo htmlspecialchars($user['UserID']); ?>">
        <div class="form-group">
            <label for="EmployeeID">EmployeeID:</label>
            <input type="text" name="EmployeeID" id="EmployeeID" value="<?php echo htmlspecialchars($user['EmployeeID']); ?>" required>
        </div>
        <div class="form-group">
            <label for="Name">Guestname:</label>
            <input type="text" name="Name" id="Name" value="<?php echo htmlspecialchars($user['Name']); ?>" required>
        </div>
        <div class="form-group">
            <label for="Email">Email:</label>
            <input type="email" name="Email" id="Email" value="<?php echo htmlspecialchars($user['Email']); ?>" required>
        </div>
        <div class="form-group">
            <label for="Phone">Phone:</label>
            <input type="text" name="Phone" id="Phone" value="<?php echo htmlspecialchars($user['Phone']); ?>" required>
        </div>
        <div class="form-group">
            <label for="Password">Password:</label>
            <input type="text" name="Password" id="Password" value="<?php echo htmlspecialchars($user['Password']); ?>" required>
        </div>
        <button type="submit" name="update">Update User</button>
    </form>
    <div class="center-container">
        <a href="Superadmindashboard.php" class="back-button">Back to Superadmin Dashboard</a>
    </div>
</body>
</html>
