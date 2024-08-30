<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" type="text/css" href="css/Login.css">
</head>
<body>
        <!-- Background Video -->
        <video autoplay muted loop playsinline>
        <source src="./Videos/BackgroundVideo.mp4" type="video/mp4">
        <!-- Fallback image for devices that can't play video -->
        <img src="./Images/FallbackImage.jpg" alt="Background Image">
        Your browser does not support the video tag.
    </video>
    <h1>Forgot Password</h1>
    <div class="loginform">
        <form action="SendOTP.php" method="post">
            <div class="empInputContainer">
                <label for="">Email</label>
                <input type="email" name="Email" placeholder="Enter your email" required><br>
            </div>
            <div class="empInputContainer center">
                <button type="submit" name="submit">Send OTP</button>
            </div>
        </form>
    </div>
</body>
</html>
