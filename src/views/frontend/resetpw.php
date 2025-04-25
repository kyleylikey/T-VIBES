<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['valid_reset']) || !$_SESSION['valid_reset']) {
    die("Unauthorized access.");
}
unset($_SESSION['valid_reset']);
?>
<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Reset Password - Taal Heritage Town</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css' rel='stylesheet'>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css' rel='stylesheet'>
    <link rel='stylesheet' href='../../public/assets/styles/resetpw.css'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;700&family=Raleway:wght@300;400;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        * {
            font-family: 'Nunito', sans-serif;
        }

        .card-body h1 {
            font-family: 'Raleway', sans-serif !important;
            font-weight: bold;
            color: #102E47;
            font-size: 35px;
        }

        .text-muted {
            color: #434343;
            font-family: 'Nunito', sans-serif !important;
        }

        .card {
            border-radius: 25px;
            transition: opacity 1s ease-in-out !important;
        }

        .btn-custom {
            font-weight: bold !important;
            border: 2px solid #102E47 !important;
            background-color: #FFFFFF !important;
            color: #434343 !important;
            cursor: pointer !important;
            transition: all 0.3s ease !important;
            font-family: 'Nunito', sans-serif !important;
            margin-top: 50px;
        }

        .btn-custom:hover {
            background-color: #102E47 !important;
            color: #FFFFFF !important;
            font-weight: bold !important;
        }

        .taalbgpic {
            background-image: url('../../../public/assets/images/taal_welcome.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        .taalbgpic::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to bottom, rgba(0, 0, 0, 0.8), rgba(0, 0, 0, 0));
            z-index: -1;
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
    </style>
</head>
<body class='d-flex align-items-center justify-content-center vh-100 taalbgpic'>
    <div class='position-absolute top-0 start-0 m-3'>
        <a href='../views/frontend/login.php' class='btn btn-secondary d-flex align-items-center rounded-circle p-2' style='width: 40px; height: 40px; justify-content: center;'>
            <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-arrow-left' viewBox='0 0 16 16'>
                <path fill-rule='evenodd' d='M15 8a.5.5 0 0 1-.5.5H3.707l3.147 3.146a.5.5 0 0 1-.708.708l-4-4a.5.5 0 0 1 0-.708l4-4a.5.5 0 0 1 .708.708L3.707 7.5H14.5A.5.5 0 0 1 15 8z'/>
            </svg>
        </a>
    </div>

    <div class='card shadow-lg' style='width: 30rem; height: 30rem; padding: 2rem;'>
        <div class='card-body'>
            <h1 class='text-center mb-2'>Reset Password</h1>
            <p class='text-center text-muted mb-4'>Please enter and re-enter your new password</p><br>
            
            <form action='resetpassword.php' method='POST'>
                <input type='hidden' name='email' value='<?php echo htmlspecialchars($email); ?>'>
                <input type='hidden' name='token' value='<?php echo htmlspecialchars($token); ?>'>
                <div class='mb-3'>
                    <input type='password' id='newPassword' name='newPassword' class='form-control rounded-pill' placeholder='New Password' required>
                </div>
                <div class='mb-3'>
                    <input type='password' id='retypeNewPassword' name='retypeNewPassword' class='form-control rounded-pill' placeholder='Re-type New Password' required>
                </div>
                <button type='submit' class='btn btn-custom w-auto px-4 py-2 rounded-pill d-block mx-auto'>Submit</button>
            </form>
        </div>
    </div>

    <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js'></script>
</body>
</html>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const passwordInput = document.getElementById('newPassword');
        const confirmPasswordInput = document.getElementById('retypeNewPassword');
        const submitButton = document.querySelector('.btn-submit');

        submitButton.disabled = true;

        function validatePassword() {
            const password = passwordInput.value;
            const confirmPassword = confirmPasswordInput.value;

            const minLength = /.{8,}/;
            const upperCase = /[A-Z]/;
            const lowerCase = /[a-z]/;
            const digit = /[0-9]/;
            const specialChar = /[~`!@#$%^&*()-_+={}\[\]\\|;:"<>,\.\/\?]/;


            let isValid = true;
            let errorMessage = '';

            if (!minLength.test(password)) {
                errorMessage = 'Password must be at least 8 characters long.';
                isValid = false;
            } else if (!upperCase.test(password)) {
                errorMessage = 'Password must contain at least one uppercase letter.';
                isValid = false;
            } else if (!lowerCase.test(password)) {
                errorMessage = 'Password must contain at least one lowercase letter.';
                isValid = false;
            } else if (!digit.test(password)) {
                errorMessage = 'Password must contain at least one number.';
                isValid = false;
            } else if (!specialChar.test(password)) {
                errorMessage = 'Password must contain at least one special character (@, #, $, etc.).';
                isValid = false;
            } else if (password !== confirmPassword && confirmPassword.length > 0) {
                errorMessage = 'Passwords do not match.';
                isValid = false;
            }

            let errorDiv = document.getElementById('passwordError');
            if (!errorDiv) {
                errorDiv = document.createElement('div');
                errorDiv.id = 'passwordError';
                errorDiv.style.color = 'red';
                errorDiv.style.marginTop = '5px';
                passwordInput.parentNode.appendChild(errorDiv);
            }
            errorDiv.textContent = isValid ? '' : errorMessage;

            submitButton.disabled = !isValid;
        }

        passwordInput.addEventListener('input', validatePassword);
        confirmPasswordInput.addEventListener('input', validatePassword);
    });
</script>