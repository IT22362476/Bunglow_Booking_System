<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" type="text/css" href="css/Login.css">
</head>

<body>
    <h1>Login</h1>
    <div class="loginform">
        <form action="Loginbackend.php" method="post">
            <div class="empInputContainer">
                <label for="">Employee ID</label>
                <input type="text" name="EmployeeID" placeholder="Employee ID" required><br>
            </div>
            <div class="empInputContainer">
                <label for="">Password</label>
                <input type="password" name="Password" placeholder="*********" required><br>
            </div>
            <div class="empInputContainer">
                <a href="/Banglow/Forgot.php">forgot password?</a>
            </div>
            <div class="empInputContainer">
                <a href="/Banglow/Signup.php">Don't have an account? Sign Up</a>
            </div>
            <div class="empInputContainer center">
                <button type="submit" name="submit">Login</button>
            </div>  
        </form>
    </div>
</body>

</html>