<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="../../../public/assets/styles/main.css">
    <link rel="stylesheet" href="../../../public/assets/styles/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .swal2-popup {
            border-radius: 12px;
            padding: 20px;
        }

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
    </style>
</head>
<body class="taalbgpic">
    <div class="login-container">
        <h1>Login</h1>
        <p style="margin-bottom: 30px;">Please sign in to continue.</p>
        <form id="loginForm">
            <input type="text" id="username" name="username" placeholder="Username" required>
            <input type="password" id="password" name="password" placeholder="Password" required>
            <a href="resetpwemail" class="forgot-password">Forgot Password?</a>
            <button type="submit" style="font-weight: bold;">Login</button>
            <p style="margin-top: 20%; color: #333333; font-size: 14px;">Don't have an account? <a href="signup.php" style="color: #3a4989; text-decoration: none; font-weight: bold; font-size: 14px;" onmouseover="this.style.textDecoration='underline';" onmouseout="this.style.textDecoration='none';">Sign Up</a></p>
        </form>
    </div>

    <div class="large-text-overlay-login">
        <h1>We've Missed You!</h1>
        <h1>Let's Pick Up</h1>
        <h1>Where You Left Off</h1>
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
                        iconHtml: '<i class="fas fa-exclamation-circle"></i>', 
                        customClass: {
                            icon: 'swal2-icon swal2-error-icon', 
                        },
                        html: `<p style="font-size: 24px; font-weight: bold;">${data.message}</p>`,
                        showConfirmButton: false,
                        timer: 3000
                    });
                }
            } catch (error) {
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
            }
        });
    </script>
</body>
</html>