<!DOCTYPE html> 
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up Page</title>
    <style>
        /* Reset default margins and paddings */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-image: url('images/taal_volcano.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            position: relative;
        }

        /* Dim the background with gradient fading from top */
        body::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to bottom, rgba(0, 0, 0, 0.8), rgba(0, 0, 0, 0));
            z-index: -1;
        }
        .signup-container {
            position: relative;
            background-color: #ffffffd1;
            padding: 30px 40px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 400px;
            max-width: 90%;
            text-align: center;
            color: #333333;
            z-index: 1;
            position: absolute;
            right: 10%;
            top: 50%;
            transform: translateY(-50%);
            height: 600px;
            transition: opacity 1s ease-in-out;
        }

        .signup-container h1 {
            margin-top: 20px;
            margin-bottom: 20px;
            color: #333333;
            font-size: 35px;
            font-weight: bold;
        }

        .signup-container p {
            margin-bottom: 40px;
            color: #555555;
            font-size: 15px;
        }

        /* Form Input Styling */
        .form-group {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .half-width-input {
            width: 48%;
            padding: 10px;
            border: 1px solid #a7a7a7;
            border-radius: 10px;
            box-sizing: border-box;
            font-size: 14px;
        }

        .full-width-input {
            width: 100%;
            padding: 10px;
            border: 1px solid #cccccc;
            border-radius: 10px;
            box-sizing: border-box;
            font-size: 14px;
            margin-bottom: 20px;
        }

        .checkbox-container {
            margin-top: 10px;
            margin-bottom: 35px;
            text-align: center;
            font-size: 12px;
            color: #555555;
        }

        .checkbox-container input[type="checkbox"] {
            margin-right: 10px;
        }

        button[type="submit"] {
            width: 150px;
            padding: 12px;
            background-color: #3a4989;
            border: none;
            border-radius: 50px;
            color: white;
            font-size: 14px;
            cursor: pointer;
            font-weight: bold;
        }

        button[type="submit"]:hover {
            background-color: #3a4989;
        }

        .large-text-overlay {
            position: absolute;
            bottom: 20%;
            left: 5%;
            color: #ffffff;
            font-size: 30px;
            font-weight: bold;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
            z-index: 2;
            text-align: left;
        }

        /* Responsive Design */
        @media (max-width: 500px) {
            .signup-container {
                left: 10%;
                width: 95%;
                max-width: 100%;
            }

            .form-group {
                flex-direction: column;
            }
        }
    </style>
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
            <p style="margin-top: 10%;">Already have an account?   <a href="Login_v1.html" style="color: #3a4989; text-decoration: none; font-weight: bold;">  Login</a></p>
        </form>
    </div>

    <div class="large-text-overlay">
        <h1>Your Lively</h1>
        <h1>Getaway Awaits!</h1>
    </div>
</body>
</html>
