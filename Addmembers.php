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
        .form-group input[type="email"],
        .form-group input[type="file"] {
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
    <div class="container">
        <h2>Add New Executive</h2>
        <form action="Addmembers.php" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="employee_id">Employee ID:</label>
                <input type="text" id="employee_id" name="employee_id" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="picture">Picture:</label>
                <input type="file" id="picture" name="picture" accept="image/*" required>
            </div>
            <div class="form-group">
                <button type="submit" name="submit">Add Member</button>
            </div>
        </form>

        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            include("Mysqlconnection.php");

            $employee_id = mysqli_real_escape_string($connection, $_POST['employee_id']);
            $email = mysqli_real_escape_string($connection, $_POST['email']);
            
            // Handling the picture upload
            $picture = $_FILES['picture']['name'];
            $target_dir = "uploads/";
            $target_file = $target_dir . basename($picture);

            // Check if file was uploaded successfully
            if (move_uploaded_file($_FILES['picture']['tmp_name'], $target_file)) {
                // Insert into the database
                $query = "INSERT INTO executives (EmployeeID, Email, picture) VALUES ('$employee_id', '$email', '$picture')";

                if (mysqli_query($connection, $query)) {
                    echo '<div class="message">New executive added successfully!</div>';
                } else {
                    echo '<div class="error">Error: ' . mysqli_error($connection) . '</div>';
                }
            } else {
                echo '<div class="error">Error uploading picture.</div>';
            }

            mysqli_close($connection);
        }
        ?>
    </div>
</body>

</html>
