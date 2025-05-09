<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Taal Heritage Town</title>
    <link rel="stylesheet" href="../../../public/assets/styles/main.css">
    <link rel="stylesheet" href="../../../public/assets/styles/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;700&family=Raleway:wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
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

        .large-text-overlay-login h1 {
            color: #FFFFFF !important;
            font-family: 'Raleway', sans-serif !important;
            font-weight: 700 !important;
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

        .login-container h1 {
            font-family: 'Raleway', sans-serif !important;
            color: #102E47 !important;
            font-weight: bold !important;
        }

        .login-container p {
            color: #434343 !important;
        }

        .forgot-password {
            color: #102E47;
            font-weight: bold;
        }
    </style>
</head>
<body class="taalbgpic">
    <div class="container">
        <div class="row justify-content-between">
            <div class="col-12 col-md-4 p-md-4 login-container">
                <h1>Login</h1>
                <p style="margin-bottom: 30px;">Please sign in to continue.</p>
                <form id="loginForm">
                    <input type="text" id="username" name="username" placeholder="Username" required>
                    <input type="password" id="password" name="password" placeholder="Password" required>
                    <a href="resetpwemail.php" class="forgot-password">Forgot Password?</a>
                    <button type="submit" style="font-weight: bold;" class="btn-custom">Login</button>
                    <p style="margin-top: 20%; color: #434343; font-size: 14px;">Don't have an account? <a href="signup.php" style="text-decoration: none; font-weight: bold; font-size: 14px; color: #102E47;" onmouseover="this.style.textDecoration='underline';" onmouseout="this.style.textDecoration='none';">Sign Up</a></p>
                </form>
            </div>

            <div class="col-0 col-md-3"></div>

            <div class="col-12 col-md-5 p-md-4 d-none d-md-block large-text-overlay-login">
                <h1>We've Missed You!</h1>
                <h1>Let's Pick Up</h1>
                <h1>Where You Left Off</h1>
            </div>
        </div>
    </div>
    <script>
        document.getElementById('loginForm').addEventListener('submit', async function(event) {
            event.preventDefault();
            
            const formData = new FormData(this);
            
            try {
                const response = await fetch('../../controllers/logincontroller.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.status === 'success') {
                    window.location.href = data.redirect;
                } else {
                    Swal.fire({
                        iconHtml: '<i class="fas fa-exclamation-circle" style="color: #EC6350 !important;"></i>', 
                        customClass: {
                            title: "swal2-title-custom",
                            icon: "swal2-icon-custom",
                            popup: "swal-custom-popup"
                        },
                        title: data.message,
                        showConfirmButton: false,
                        timer: 3000
                    });
                }
            } catch (error) {
                console.error('Error:', error);
                Swal.fire({
                    iconHtml: '<i class="fas fa-exclamation-circle" style="color: #EC6350 !important;"></i>', 
                    customClass: {
                        title: "swal2-title-custom",
                        icon: "swal2-icon-custom",
                        popup: "swal-custom-popup"
                    },
                    title: 'Something went wrong. Please try again later.',
                    showConfirmButton: false,
                    timer: 3000
                });
            }
        });
    </script>
</body>
</html>