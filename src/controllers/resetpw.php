<?php
require_once '../config/dbconnect.php';

if (isset($_GET['token']) && isset($_GET['email'])) {
    $token = trim($_GET['token']);
    $email = urldecode($_GET['email']);
    
    // Initialize variables
    $success = false;
    $message = '';
    $iconHtml = '<i class="fas fa-exclamation-circle"></i>';
    $validReset = false;
    
    try {
        $database = new Database();
        $conn = $database->getConnection();
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Clean token and email
        $cleanedToken = trim($token);
        $cleanedEmail = trim($email);
        
        // Verify the token and email
        $checkQuery = "SELECT userid, username, email, token_expiry, emailveriftoken 
                       FROM taaltourismdb.users 
                       WHERE LOWER(email) = LOWER(?) AND LOWER(emailveriftoken) = LOWER(?)";
        $checkStmt = $conn->prepare($checkQuery);
        $checkStmt->bindParam(1, $cleanedEmail, PDO::PARAM_STR);
        $checkStmt->bindParam(2, $cleanedToken, PDO::PARAM_STR);
        $checkStmt->execute();
        
        // Fetch the user data
        $userData = $checkStmt->fetch(PDO::FETCH_ASSOC);
        
        if ($userData) {
            // Check if token is expired
            if (isset($userData['token_expiry']) && strtotime($userData['token_expiry']) < time()) {
                $message = 'Your password reset link has expired. Please request a new one.';
                
                // Set emailveriftoken and token_expiry to NULL for expired token
                $expireQuery = "UPDATE taaltourismdb.users 
                                SET emailveriftoken = NULL, token_expiry = NULL 
                                WHERE userid = ?";
                $expireStmt = $conn->prepare($expireQuery);
                $expireStmt->bindParam(1, $userData['userid'], PDO::PARAM_INT);
                $expireStmt->execute();
            }
            // Everything is good, allow password reset
            else {
                $validReset = true;
                $message = 'Please enter your new password.';
                $iconHtml = '<i class="fas fa-key"></i>';
                
                // Store user ID in session for the password reset form
                if (session_status() == PHP_SESSION_NONE) {
                    session_start();
                }
                $_SESSION['reset_user_id'] = $userData['userid'];
                $_SESSION['valid_reset'] = true;
            }
        } else {
            $message = 'Invalid password reset link. Please request a new one.';
        }
    } catch (PDOException $e) {
        $message = 'An error occurred during verification. Please contact support.';
    }

    // Escape quotes for JavaScript
    $message = str_replace("'", "\\'", $message);

    // If reset is valid, show the password reset form
    if ($validReset) {
        include 'resetpassword.php';
        exit;
    }
    
    // Otherwise, show error message and redirect
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Reset Password</title>
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css'>
    <link href='https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;700&family=Raleway:wght@300;400;700&display=swap' rel='stylesheet'>
    <style>
        body {
            font-family: 'Nunito', sans-serif;
            background-color: #f8f9fa;
            color: #434343;
            padding: 20px;
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
<body>
    <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js' integrity='sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz' crossorigin='anonymous'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/js/all.min.js'></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Debug information in console
            console.log('Reset password page loaded');
            console.log('Success:', <?php echo $success ? 'true' : 'false'; ?>);
            console.log('Message:', '<?php echo $message; ?>');
            
            // Try-catch to catch and log any errors during SweetAlert execution
            try {
                Swal.fire({
                    iconHtml: '<?php echo $iconHtml; ?>', 
                    customClass: {
                        title: 'swal2-title-custom',
                        icon: 'swal2-icon-custom',
                        popup: 'swal-custom-popup'
                    },
                    title: '<?php echo $message; ?>',
                    showConfirmButton: true,
                    confirmButtonText: 'Go to Login'
                }).then(() => {
                    console.log('Redirecting to login page...');
                    window.location.href = '../views/frontend/login.php'; 
                }).catch(error => {
                    console.error('SweetAlert error:', error);
                });
            } catch (error) {
                console.error('Error in password reset process:', error);
            }
        });
    </script>
</body>
</html>
<?php
}
?>