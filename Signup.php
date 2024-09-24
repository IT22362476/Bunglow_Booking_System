<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup</title>
    <link rel="stylesheet" type="text/css" href="css/Signup.css">
</head>

<body>
    <!-- Background Video or Image for iPhones and mobile devices -->
    <video autoplay muted loop playsinline id="backgroundVideo">
        <source src="./Videos/BackgroundVideo.mp4" type="video/mp4">
        Your browser does not support the video tag.
    </video>

    <!-- Content -->
    <div class="addform">
        <form action="Signupbackend.php" method="post" enctype="multipart/form-data"> <!-- Added enctype -->
            <div class="empInputContainer">
                <label for="">Employee ID</label>
                <input type="text" name="EmployeeID" placeholder="Employee ID" required><br>
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
                <input type="password" name="cPassword" placeholder="*********" required><br>
            </div>
            <div class="empInputContainer">
                <label for="">Profile Picture</label>
                <input type="file" name="Picture" accept="image/*" required><br> <!-- Added file input -->
            </div>
            <div class="btnContainer">
                <button type="submit" name="submit">Signup</button>
            </div>
        </form>
    </div>
</body>

</html>
