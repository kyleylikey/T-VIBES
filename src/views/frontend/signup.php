<!DOCTYPE html> 
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up Page</title>
    <link rel="stylesheet" href="../../../public/assets/styles/main.css">
    <link rel="stylesheet" href="../../../public/assets/styles/login.css">
</head>
<body class="taalbgpic">
    <div class="signup-container">
        <h1>Sign Up</h1>
        <p>Please register to log in.</p>
        <form action="../../controllers/signupcontroller.php" method="POST">
            <div class="form-group">
            <input type="text" id="fullname" name="fullname" placeholder="Full Name" required class="half-width-input" pattern="^[A-Za-z\s]+$" title="Full name must contain only alphabets and spaces.">
                <input type="text" id="username" name="username" placeholder="Username" required class="half-width-input" pattern="^\w{3,20}$" title="Username must be 3-20 characters long and can only include letters, numbers, and underscores.">
            </div>
            <input type="tel" id="contact" name="contact" placeholder="Contact Number" pattern="^\d{11}$" required class="full-width-input" title="Contact number must be exactly 11 digits." inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '');">
            <input type="email" id="email" name="email" placeholder="Email" required class="full-width-input">            
            <input type="password" id="password" name="password" placeholder="Password" required class="full-width-input" pattern="^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$" title="Password must be at least 8 characters long, and include one letter, one number, and one special character.">

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