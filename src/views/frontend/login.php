<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="../../../public/assets/styles/main.css">

</head>
<body>
    <div class="login-container">
        <h1>Login</h1>
        <p style="margin-bottom: 30px;">Please sign in to continue.</p>
        <form action="/login" method="POST">
            <input type="text" id="username" name="username" placeholder="   Username" required>
            <input type="password" id="password" name="password" placeholder="   Password" required>
            <a href="/forgot-password" class="forgot-password">Forgot Password?</a>
            <button type="submit">Login</button>
            <p style="margin-top: 20%;">Don't have an account? <a href="signup.php" style="color: #3a4989; text-decoration: none;">Sign Up</a></p>
        </form>
    </div>

    <div class="large-text-overlay-login">
        <h1>We've Missed You!</h1>
        <h1>Let's Pick Up</h1>
        <h1>Where You Left Off</h1>
    </div>
</body>
</html>
