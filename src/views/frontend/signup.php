<!DOCTYPE html> 
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up Page</title>
    <link rel="stylesheet" href="../../../public/assets/styles/main.css">
</head>
<body>
    <div class="signup-container">
        <h1>Sign Up</h1>
        <p>Please register to log in.</p>
        <form action="/register" method="POST">
            <div class="form-group">
                <input type="text" id="fullname" name="fullname" placeholder="   Full Name" required class="half-width-input">
                <input type="text" id="username" name="username" placeholder="   Username" required class="half-width-input">
            </div>
            <input type="tel" id="contact" name="contact" placeholder="   Contact Number" required class="full-width-input">
            <input type="email" id="email" name="email" placeholder="   Email" required class="full-width-input">            
            <input type="password" id="password" name="password" placeholder="   Password" required class="full-width-input">

            <div class="checkbox-container">
                <input type="checkbox" id="privacyPolicy" name="privacyPolicy" required>
                <label for="privacyPolicy">Privacy Policy & Terms of Service</label>
            </div>
            
            <button type="submit">Create Account</button>
            <p class="login-redirect">Already have an account? <a href="login.php">Login</a></p>
        </form>
    </div>

    <div class="large-text-overlay-signup">
        <h1>Your Lively</h1>
        <h1>Getaway Awaits!</h1>
    </div>
</body>
</html>
