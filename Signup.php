<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup</title>
    <link rel="stylesheet" type="text/css" href="css/Signup.css">

</head>

<body>
    <h1>Signup</h1>
    <div class="addform">
        <form action="Signupbackend.php" method="post">
            <div class="empInputContainer">
                <label for="">Employee ID</label>
                <input type="text" name="EmployeeID" placeholder="Employee ID" required><br>
            </div>
            <div class="empInputContainer">
                <label for="">Guest name</label>
                <input type="text" name="Guestname" placeholder="Guest name" required><br>
            </div>
            <div class="empInputContainer">
                <label for="">Phone number</label>
                <input type="phone" name="Phone" placeholder="Phone number" required><br>
            </div>
            <div class="empInputContainer">
                <label for="">Password</label>
                <input type="password" name="Password" placeholder="*********" required><br>
            </div>
            <div class="empInputContainer">
                <label for="">Confirm Password</label>
                <input type="text" name="cPassword" placeholder="*********" required><br>
            </div>
            <div class="btnContainer">
                <button type="submit" name="submit">Signup</button>
            </div>
        </form>
    </div>
</body>

</html>