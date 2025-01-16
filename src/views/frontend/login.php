<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <style>
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

        .login-container {
            background-color: #ffffffd1;
            padding: 30px 40px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 400px;
            text-align: center;
            color: #333333;
            position: relative;
        }

        .login-container h1 {
            margin-bottom: 20px;
            font-size: 35px;
        }

        .login-container p {
            margin-bottom: 30px;
            font-size: 15px;
        }

        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #cccccc;
            border-radius: 50px;
            font-size: 14px;
        }

        button[type="submit"] {
            width: 70%;
            padding: 12px;
            background-color: #3a4989;
            border: none;
            border-radius: 50px;
            color: white;
            font-size: 13px;
            cursor: pointer;
        }

        button[type="submit"]:hover {
            background-color: #2d3873;
        }

        .error-message {
            color: red;
            font-size: 14px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>Login</h1>
        <p>Please sign in to continue.</p>
        
        <?php
        // Define hardcoded credentials
        $valid_username = 'admin';
        $valid_password = 'password';

        // Initialize error message
        $error_message = '';

        // Handle form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'];
            $password = $_POST['password'];

            if ($username === $valid_username && $password === $valid_password) {
                echo '<p style="color: green; font-size: 16px;">Login successful! Welcome, ' . htmlspecialchars($username) . '.</p>';
            } else {
                $error_message = 'Invalid username or password.';
            }
        }
        ?>

        <!-- Show error message if present -->
        <?php if (!empty($error_message)): ?>
            <p class="error-message"><?php echo $error_message; ?></p>
        <?php endif; ?>

        <!-- Login form -->
        <form method="POST" action="">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>
