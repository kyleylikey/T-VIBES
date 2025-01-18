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
        <form action="../../../controllers/signupcontroller.php" method="POST">
            <div class="form-group">
                <label for="fullname" class="visually-hidden">Full Name</label>
                <input 
                    type="text" 
                    id="fullname" 
                    name="fullname" 
                    placeholder="Full Name" 
                    required 
                    class="half-width-input"
                >
                
                <label for="username" class="visually-hidden">Username</label>
                <input 
                    type="text" 
                    id="username" 
                    name="username" 
                    placeholder="Username" 
                    required 
                    class="half-width-input"
                >
            </div>
            
            <label for="contact" class="visually-hidden">Contact Number</label>
            <input 
                type="tel" 
                id="contact" 
                name="contact" 
                placeholder="Contact Number" 
                required 
                class="full-width-input"
            >
            
            <label for="email" class="visually-hidden">Email</label>
            <input 
                type="email" 
                id="email" 
                name="email" 
                placeholder="Email" 
                required 
                class="full-width-input"
            >
            
            <label for="password" class="visually-hidden">Password</label>
            <input 
                type="password" 
                id="password" 
                name="password" 
                placeholder="Password" 
                required 
                class="full-width-input"
            >

            <div class="checkbox-container">
                <input 
                    type="checkbox" 
                    id="privacyPolicy" 
                    name="privacyPolicy" 
                    required
                >
                <label for="privacyPolicy">I agree to the Privacy Policy & Terms of Service</label>
            </div>
            
            <button type="submit" class="submit-button">Create Account</button>
            
            <p class="login-redirect">
                Already have an account? 
                <a href="login.php">Login</a>
            </p>
        </form>
    </div>

    <div class="large-text-overlay-signup">
        <h1>Your Lively</h1>
        <h1>Getaway Awaits!</h1>
    </div>
</body>
</html>
