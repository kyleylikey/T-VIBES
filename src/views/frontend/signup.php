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
    <title>Sign Up - Taal Heritage Town</title>
    <link rel="stylesheet" href="../../../public/assets/styles/main.css">
    <link rel="stylesheet" href="../../../public/assets/styles/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;700&family=Raleway:wght@300;400;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        * {
            font-family: 'Nunito', sans-serif;
        }

        .swal2-icon {
            background: none !important;
            border: none !important;
            box-shadow: none !important;
        }

        .swal2-icon-custom {
            font-size: 10px;
            color: #EC6350; 
        }

        .swal2-title-custom {
            font-size: 24px !important;
            font-weight: bold;
            color: #434343 !important;
        }

        .swal-custom-popup {
            padding: 20px;
            border-radius: 25px;
            font-family: 'Nunito', sans-serif !important;
        }

        .large-text-overlay-signup h1 {
            color: #FFFFFF !important;
            font-family: 'Raleway', sans-serif !important;
        }

        .btn-custom {
            font-weight: bold !important;
            border: 2px solid #102E47 !important;
            background-color: #FFFFFF !important;
            color: #434343 !important;
            cursor: pointer !important;
            transition: all 0.3s ease !important;
            font-family: 'Nunito', sans-serif !important;
        }

        .btn-custom:hover {
            background-color: #102E47 !important;
            color: #FFFFFF !important;
            font-weight: bold !important;
        }

        .font-link {
            color: #102E47 !important;
        }

        .signup-container h1 {
            font-family: 'Raleway', sans-serif !important;
            color: #102E47 !important;
            font-weight: bold !important;
        }

        .signup-container p {
            color: #434343 !important;
        }

        .checkbox-container {
            color: #434343 !important;
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

            <button type="submit" id="submitBtn" class="btn-custom">Create Account</button>
            <p class="login-redirect">Already have an account? <a href="login.php" class="font-link">Login</a></p>
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
        const submitButton = document.getElementById('submitBtn');
        submitButton.disabled = true;
        submitButton.textContent = "Loading...";

        fetch('../../controllers/signupcontroller.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            Swal.fire({
                iconHtml: data.status === 'success' ? '<i class="fas fa-envelope" style="color: #EC6350 !important;"></i>' : '<i class="fas fa-exclamation-circle" style="color: #EC6350 !important;"></i>',
                title: data.message,
                showConfirmButton: false,
                timer: 3000,
                customClass: {
                    title: "swal2-title-custom",
                    icon: "swal2-icon-custom",
                    popup: "swal-custom-popup"
                }
            });

            if (data.status === 'success') {
                submitButton.textContent = "Email Sent!";
            } else {
                submitButton.disabled = false;
                submitButton.textContent = "Create Account";
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                iconHtml: '<i class="fas fa-exclamation-circle" style: "color: #EC6350 !important;"></i>',
                title: 'Something went wrong. Please try again later.',
                showConfirmButton: false,
                timer: 3000,
                customClass: {
                    title: "swal2-title-custom",
                    icon: "swal2-icon-custom",
                    popup: "swal-custom-popup"
                }
            }).then(() => {
                submitButton.disabled = false;
                submitButton.textContent = "Create Account";
            });
        });
    });
</script>

</body>
</html>