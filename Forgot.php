<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" type="text/css" href="css/Forgot.css">
</head>

<body>
    <h1>Forgot Password</h1>
    <div class="form-container">
        <form action="Forgotbackend.php" method="post">
            <div class="form-group">
                <label for="email">Enter your email address</label>
                <input type="email" id="email" name="email" placeholder="Email address" required>
            </div>
            <div class="form-group">
                <button type="submit">Send Reset Link</button>
            </div>
            <div class="form-group">
                <a href="/Login.php">Back to Login</a>
            </div>
        </form>
    </div>
</body>

</html>
