<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Member</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .maincontainer {
            display: flex;
            /* Enables flexbox layout */
            flex-direction: column;
            /* Arranges children in a vertical column */
            
        }

        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
        }

        .container h2 {
            margin-bottom: 20px;
            text-align: center;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
        }

        .form-group input[type="text"],
        .form-group input[type="email"] {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }

        .form-group button {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .form-group button:hover {
            background-color: #45a049;
        }

        .back-button {
            margin-top: 20px;
            text-align: center;
        }

        .back-button a {
            padding: 10px 15px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
        }

        .back-button a:hover {
            background-color: #007bb5;
        }

        .message {
            text-align: center;
            margin-top: 20px;
            color: green;
        }

        .error {
            color: red;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="maincontainer">
        <div class="container">
            <h2>Add New Executive</h2>
            <form action="Addmembers.php" method="post">
                <div class="form-group">
                    <label for="employee_id">Employee ID:</label>
                    <input type="text" id="employee_id" name="employee_id" required>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="name">Name:</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <button type="submit" name="submit">Add Member</button>
                </div>
            </form>
        </div>

    <div class="back-button">
        <a href="Admindashboard.php">Back</a>
    </div>
    </div>
    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        include("Mysqlconnection.php");

        $employee_id = mysqli_real_escape_string($connection, $_POST['employee_id']);
        $email = mysqli_real_escape_string($connection, $_POST['email']);
        $name = mysqli_real_escape_string($connection, $_POST['name']);

        // Check if the employee ID or email already exists
        $check_query = "SELECT * FROM executives WHERE EmployeeID = '$employee_id' OR Email = '$email'";
        $check_result = mysqli_query($connection, $check_query);

        if (mysqli_num_rows($check_result) > 0) {
            echo '<script>
                        alert("An executive with this Employee ID or Email already exists.");
                        window.location.href="Addmembers.php";
                    </script>';
        } else {
            // Insert into the database
            $query = "INSERT INTO executives (EmployeeID, Email, Name) VALUES ('$employee_id', '$email', '$name')";

            if (mysqli_query($connection, $query)) {
                echo '<script>
                            alert("New executive added successfully!");
                            window.location.href="Admindashboard.php";
                        </script>';
            } else {
                echo '<div class="error">Error: ' . mysqli_error($connection) . '</div>';
            }
        }

        mysqli_close($connection);
    }
    ?>
</body>

</html>