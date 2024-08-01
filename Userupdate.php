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
    $guestname = $_POST['Guestname'];
    $email = $_POST['Email'];
    $phone = $_POST['Phone'];

    $sql = "UPDATE users SET EmployeeID = ?, Guestname = ?, Email = ?, Phone = ? WHERE UserID = ?";
    $stmt = mysqli_prepare($connection, $sql);
    mysqli_stmt_bind_param($stmt, 'ssssi', $employeeID, $guestname, $email, $phone, $userID);

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
        }

        h2 {
            text-align: center;
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
            <label for="Guestname">Guestname:</label>
            <input type="text" name="Guestname" id="Guestname" value="<?php echo htmlspecialchars($user['Guestname']); ?>" required>
        </div>
        <div class="form-group">
            <label for="Email">Email:</label>
            <input type="email" name="Email" id="Email" value="<?php echo htmlspecialchars($user['Email']); ?>" required>
        </div>
        <div class="form-group">
            <label for="Phone">Phone:</label>
            <input type="text" name="Phone" id="Phone" value="<?php echo htmlspecialchars($user['Phone']); ?>" required>
        </div>
        <button type="submit" name="update">Update User</button>
    </form>
</body>
</html>
