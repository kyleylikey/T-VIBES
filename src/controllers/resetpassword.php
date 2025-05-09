<?php
require_once '../config/dbconnect.php';

if (!isset($_GET['email']) || !isset($_GET['token'])) {
    header("Location: login.php?error=invalid_reset");
    exit;
}


$success = false;
$message = '';
$validRequest = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle password reset submission
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $token = isset($_POST['token']) ? trim($_POST['token']) : '';
    $newPassword = isset($_POST['newPassword']) ? $_POST['newPassword'] : '';
    $retypeNewPassword = isset($_POST['retypeNewPassword']) ? $_POST['retypeNewPassword'] : '';

    // Validate passwords
    if ($newPassword !== $retypeNewPassword) {
        $message = 'Passwords do not match.';
    } elseif (strlen($newPassword) < 8 || !preg_match('/[A-Z]/', $newPassword) || !preg_match('/\d/', $newPassword)) {
        $message = 'Password does not meet requirements.';
    } else {
        // Check token/email validity and expiry
        $database = new Database();
        $conn = $database->getConnection();
        $query = "SELECT userid, token_expiry FROM [taaltourismdb].[users] 
                  WHERE LOWER(email) = LOWER(:email) AND LOWER(emailveriftoken) = LOWER(:token)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':token', $token, PDO::PARAM_STR);
        $stmt->execute();
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($userData && isset($userData['token_expiry']) && strtotime($userData['token_expiry']) > time()) {
            // Hash the new password
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

            // Update password and clear token
            $updateQuery = "UPDATE [taaltourismdb].[users] 
                            SET hashedpassword = :password, emailveriftoken = NULL, token_expiry = NULL 
                            WHERE userid = :userid";
            $updateStmt = $conn->prepare($updateQuery);
            $updateStmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
            $updateStmt->bindParam(':userid', $userData['userid'], PDO::PARAM_INT);
            if ($updateStmt->execute()) {
                $success = true;
                $message = 'Your password has been reset successfully. You can now <a href="https://tourtaal.azurewebsites.net/src/views/frontend/login.php">login</a>.';
            } else {
                $message = 'Failed to reset password. Please try again.';
            }
        } else {
            $message = 'Invalid or expired reset link.';
        }
    }
} else {
    // GET: Validate token/email for form display
    if (!isset($_GET['email']) || !isset($_GET['token'])) {
        $message = 'Invalid reset link.';
    } else {
        $email = urldecode($_GET['email']);
        $token = $_GET['token'];
        $database = new Database();
        $conn = $database->getConnection();
        $query = "SELECT userid, token_expiry, emailveriftoken FROM [taaltourismdb].[users] 
                  WHERE LOWER(email) = LOWER(:email) AND LOWER(emailveriftoken) = LOWER(:token)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':token', $token, PDO::PARAM_STR);
        $stmt->execute();
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($userData && isset($userData['token_expiry']) && strtotime($userData['token_expiry']) > time()) {
            $validRequest = true;
        } else {
            $message = 'This password reset link is invalid or has expired. Please request a new password reset from the login page.';
        }
    }
}

$email = urldecode($_GET['email']);
$token = $_GET['token'];

// Validate token still exists and is valid (without revealing success/failure yet)
$database = new Database();
$conn = $database->getConnection();

try {
    $query = "SELECT userid, token_expiry, emailveriftoken FROM [taaltourismdb].[users] 
          WHERE LOWER(email) = LOWER(:email) AND LOWER(emailveriftoken) = LOWER(:token)";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->bindParam(':token', $token, PDO::PARAM_STR);
    $stmt->execute();

    $userData = $stmt->fetch(PDO::FETCH_ASSOC);
    $validRequest = false;
    if ($userData) {
        // Check expiry in PHP
        if (isset($userData['token_expiry']) && strtotime($userData['token_expiry']) > time()) {
            $validRequest = true;
        }
    }
} catch (PDOException $e) {
    // Just log the error, don't reveal details
    error_log("Error validating reset request: " . $e->getMessage());
    $validRequest = false;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Reset Password</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css' rel='stylesheet'>
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css'>
    <link href='https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;700&family=Raleway:wght@300;400;700&display=swap' rel='stylesheet'>
    <style>
        body {
            font-family: 'Nunito', sans-serif;
            background-color: #f8f9fa;
            color: #434343;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .form-container {
            width: 100%;
            max-width: 450px;
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .form-title {
            text-align: center;
            margin-bottom: 25px;
            color: #434343;
        }
        .form-control {
            border-radius: 10px;
            padding: 12px;
            border: 1px solid #ddd;
        }
        .form-control:focus {
            box-shadow: 0 0 0 3px rgba(236, 99, 80, 0.25);
            border-color: #EC6350;
        }
        .btn-primary {
            background-color: #EC6350;
            border: none;
            border-radius: 10px;
            padding: 12px 20px;
            font-weight: 600;
        }
        .btn-primary:hover {
            background-color: #d5573e;
        }
        .btn-primary:disabled {
            background-color: #e9a199;
        }
        .password-container {
            position: relative;
        }
        .toggle-password {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6c757d;
        }
        .requirements {
            font-size: 0.85rem;
            color: #6c757d;
            margin-top: 15px;
        }
        .requirement-item {
            margin-bottom: 5px;
        }
        .requirement-item i {
            margin-right: 5px;
        }
        .valid {
            color: #28a745;
        }
        .invalid {
            color: #dc3545;
        }
        .info-text {
            text-align: center;
            margin-bottom: 25px;
            font-size: 0.9rem;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <?php if ($success): ?>
    <div class="form-container">
        <h2 class="form-title">Password Reset Successful</h2>
        <div class="info-text">
            <?php echo $message; ?>
        </div>
        <div class="d-grid gap-2 mt-4">
            <a href="https://tourtaal.azurewebsites.net/src/views/frontend/login.php" class="btn btn-primary">Go to Login</a>
        </div>
    </div>
    <?php elseif ($validRequest): ?>
    <div class="form-container">
        <h2 class="form-title">Reset Your Password</h2>
        <div class="info-text">
            Please enter a new password for your account.
        </div>
        
        <form id="resetPasswordForm" method="POST">
            <!-- Hidden fields to pass email & token -->
            <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
            
            <div class="mb-4 password-container">
                <label for="newPassword" class="form-label">New Password</label>
                <input type="password" class="form-control" id="newPassword" name="newPassword" required>
                <i class="far fa-eye toggle-password" id="togglePassword"></i>
            </div>
            
            <div class="mb-4 password-container">
                <label for="retypeNewPassword" class="form-label">Confirm Password</label>
                <input type="password" class="form-control" id="retypeNewPassword" name="retypeNewPassword" required>
                <i class="far fa-eye toggle-password" id="toggleConfirmPassword"></i>
            </div>
            
            <div class="requirements">
                <div class="requirement-item" id="length"><i class="fas fa-times"></i> At least 8 characters</div>
                <div class="requirement-item" id="uppercase"><i class="fas fa-times"></i> At least one uppercase letter</div>
                <div class="requirement-item" id="number"><i class="fas fa-times"></i> At least one number</div>
                <div class="requirement-item" id="match"><i class="fas fa-times"></i> Passwords match</div>
            </div>
            
            <div class="d-grid gap-2 mt-4">
                <button type="submit" class="btn btn-primary" id="submitBtn" disabled>Reset Password</button>
            </div>
        </form>
    </div>
    <?php else: ?>
    <div class="form-container">
        <h2 class="form-title">Invalid Reset Request</h2>
        <div class="info-text">
            This password reset link is invalid or has expired. Please request a new password reset from the login page.
        </div>
        <div class="d-grid gap-2 mt-4">
            <a href="https://tourtaal.azurewebsites.net/src/views/frontend/login.php" class="btn btn-primary">Go to Login</a>
        </div>
    </div>
    <?php endif; ?>

    <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js'></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            <?php if ($validRequest): ?>
            const newPassword = document.getElementById('newPassword');
            const retypeNewPassword = document.getElementById('retypeNewPassword');
            const togglePassword = document.getElementById('togglePassword');
            const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
            const submitBtn = document.getElementById('submitBtn');
            
            // Requirements
            const lengthReq = document.getElementById('length');
            const uppercaseReq = document.getElementById('uppercase');
            const numberReq = document.getElementById('number');
            const matchReq = document.getElementById('match');
            
            // Toggle password visibility
            togglePassword.addEventListener('click', function() {
                const type = newPassword.getAttribute('type') === 'password' ? 'text' : 'password';
                newPassword.setAttribute('type', type);
                this.classList.toggle('fa-eye');
                this.classList.toggle('fa-eye-slash');
            });
            
            toggleConfirmPassword.addEventListener('click', function() {
                const type = retypeNewPassword.getAttribute('type') === 'password' ? 'text' : 'password';
                retypeNewPassword.setAttribute('type', type);
                this.classList.toggle('fa-eye');
                this.classList.toggle('fa-eye-slash');
            });
            
            // Validate password on input
            function validatePassword() {
                const val = newPassword.value;
                const confirmVal = retypeNewPassword.value;
                
                // Check length
                if (val.length >= 8) {
                    lengthReq.classList.add('valid');
                    lengthReq.classList.remove('invalid');
                    lengthReq.querySelector('i').classList.remove('fa-times');
                    lengthReq.querySelector('i').classList.add('fa-check');
                } else {
                    lengthReq.classList.remove('valid');
                    lengthReq.classList.add('invalid');
                    lengthReq.querySelector('i').classList.add('fa-times');
                    lengthReq.querySelector('i').classList.remove('fa-check');
                }
                
                // Check uppercase
                if (/[A-Z]/.test(val)) {
                    uppercaseReq.classList.add('valid');
                    uppercaseReq.classList.remove('invalid');
                    uppercaseReq.querySelector('i').classList.remove('fa-times');
                    uppercaseReq.querySelector('i').classList.add('fa-check');
                } else {
                    uppercaseReq.classList.remove('valid');
                    uppercaseReq.classList.add('invalid');
                    uppercaseReq.querySelector('i').classList.add('fa-times');
                    uppercaseReq.querySelector('i').classList.remove('fa-check');
                }
                
                // Check number
                if (/\d/.test(val)) {
                    numberReq.classList.add('valid');
                    numberReq.classList.remove('invalid');
                    numberReq.querySelector('i').classList.remove('fa-times');
                    numberReq.querySelector('i').classList.add('fa-check');
                } else {
                    numberReq.classList.remove('valid');
                    numberReq.classList.add('invalid');
                    numberReq.querySelector('i').classList.add('fa-times');
                    numberReq.querySelector('i').classList.remove('fa-check');
                }
                
                // Check if passwords match
                if (val === confirmVal && val !== '') {
                    matchReq.classList.add('valid');
                    matchReq.classList.remove('invalid');
                    matchReq.querySelector('i').classList.remove('fa-times');
                    matchReq.querySelector('i').classList.add('fa-check');
                } else {
                    matchReq.classList.remove('valid');
                    matchReq.classList.add('invalid');
                    matchReq.querySelector('i').classList.add('fa-times');
                    matchReq.querySelector('i').classList.remove('fa-check');
                }
                
                // Enable/disable submit button
                if (val.length >= 8 && /[A-Z]/.test(val) && /\d/.test(val) && 
                    val === confirmVal && val !== '') {
                    submitBtn.disabled = false;
                } else {
                    submitBtn.disabled = true;
                }
            }
            
            newPassword.addEventListener('keyup', validatePassword);
            retypeNewPassword.addEventListener('keyup', validatePassword);
            
            <?php endif; ?>
        });
    </script>
</body>
</html>