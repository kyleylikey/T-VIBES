<?php
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', '/T-VIBES/temp/error.txt');
error_reporting(E_ALL);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up Page</title>
    <link rel="stylesheet" href="../../../public/assets/styles/main.css">
    <link rel="stylesheet" href="../../../public/assets/styles/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;700&family=Raleway:wght@300;400;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        * {
            font-family: 'Nunito', sans-serif;
        }

        .swal2-popup {
            border-radius: 12px;
            padding: 20px;
        }
        .swal2-icon.swal2-email-icon,
        .swal2-icon.swal2-error-icon {
            border: none; 
            font-size: 10px; 
            display: flex;
            align-items: center;
            justify-content: center;
            width: 60px; 
            height: 60px; 
            color: #333; 
        }

        .large-text-overlay-signup h1 {
            color: #FFFFFF !important;
        }
    </style>
</head>
<body class="taalbgpic">
    <div class="signup-container">
        <h1>Sign Up</h1>
        <p>Please register to log in.</p>
        <form id="signupForm">
            <div class="form-group">
                <input type="text" id="fullname" name="fullname" placeholder="Full Name" required class="half-width-input" pattern="^[A-Za-z\s]+$" title="Full name must contain only alphabets and spaces.">
                <input type="text" id="username" name="username" placeholder="Username" required class="half-width-input" pattern="^\w{3,20}$" title="Username must be 3-20 characters long and can only include letters, numbers, and underscores.">
            </div>
            <input type="tel" id="contact" name="contact" placeholder="Contact Number" pattern="^\d{11}$" required class="full-width-input" title="Contact number must be exactly 11 digits." inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '');">
            <input type="email" id="email" name="email" placeholder="Email" required class="full-width-input">
            <input type="password" id="password" name="password" placeholder="Password" required class="full-width-input" pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$" title="Password must be at least 8 characters long, include at least one uppercase letter, one lowercase letter, one number, and one special character.">

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

    <script>
        document.getElementById('signupForm').addEventListener('submit', function(event) {
            event.preventDefault(); 

            const formData = new FormData(this);

            fetch('../../controllers/signupcontroller.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire({
                        iconHtml: '<i class="fas fa-envelope"></i>',
                        customClass: {
                            icon: 'swal2-icon swal2-email-icon'
                        },
                        html: '<p style="font-size: 24px; font-weight: bold;">' + data.message + '</p>',
                        showConfirmButton: false, 
                        timer: 3000
                    });
                } else {
                    Swal.fire({
                        iconHtml: '<i class="fas fa-exclamation-circle"></i>', 
                        customClass: {
                            icon: 'swal2-icon swal2-error-icon', 
                        },
                        html: '<p style="font-size: 24px; font-weight: bold;">' + data.message + '</p>',
                        showConfirmButton: false,
                        timer: 3000
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    iconHtml: '<i class="fas fa-exclamation-circle"></i>', 
                    customClass: {
                        icon: 'swal2-icon swal2-error-icon', 
                    },
                    html: '<p style="font-size: 24px; font-weight: bold;">Something went wrong. Please try again later.</p>',
                    showConfirmButton: false,
                    timer: 3000
                });
            });
        });
    </script>
</body>
</html>