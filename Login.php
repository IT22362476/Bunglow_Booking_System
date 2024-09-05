<?php
session_start();

// If the session timeout error is set, display a message
$timeoutMessage = '';
if (isset($_GET['timeout']) && $_GET['timeout'] == 'true') {
    $timeoutMessage = 'Your session has expired due to inactivity. Please log in again.';
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
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

    <!-- Content -->
    <div class="loginform">
        <?php if ($timeoutMessage): ?>
            <p style="color: red; text-align: center;"><?php echo $timeoutMessage; ?></p>
        <?php endif; ?>
        
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
                <a href="/Banglow/Forgot.php">Forgot password?</a>
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
