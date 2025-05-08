<?php
require_once '../config/dbconnect.php';

if (isset($_GET['token'])) {
    $token = trim($_GET['token']);
    
    // Initialize variables
    $success = false;
    $message = '';
    $iconHtml = '<i class="fas fa-exclamation-circle"></i>';
    
    try {
        $database = new Database();
        $conn = $database->getConnection();
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Clean token
        $cleanedToken = trim($token);
    
        
        // Verify the token directly using the correct table name
        $checkQuery = "SELECT userid, username, email, status, token_expiry, emailveriftoken 
               FROM taaltourismdb.users 
               WHERE LOWER(emailveriftoken) = LOWER(?)";
        $checkStmt = $conn->prepare($checkQuery);
        $checkStmt->bindParam(1, $cleanedToken, PDO::PARAM_STR);
        $checkStmt->execute();
        
        // Fetch the user data
        $userData = $checkStmt->fetch(PDO::FETCH_ASSOC);
        
        if ($userData) {
            // Check if already verified
            if ($userData['status'] !== 'inactive') {
                $message = 'Your email is already verified.';
            }
            // Check if token is expired
            else if (isset($userData['token_expiry']) && strtotime($userData['token_expiry']) < time()) {
                $message = 'Your verification link has expired. Please request a new one.';

                // Set emailveriftoken and token_expiry to NULL for expired token
                    $expireQuery = "UPDATE taaltourismdb.users 
                    SET emailveriftoken = NULL, token_expiry = NULL 
                    WHERE userid = ?";
                $expireStmt = $conn->prepare($expireQuery);
                $expireStmt->bindParam(1, $userData['userid'], PDO::PARAM_INT);
                $expireStmt->execute();

            }
            // Everything is good, verify the email
            else {
                $updateQuery = "UPDATE taaltourismdb.users 
                               SET status = 'active', emailveriftoken = NULL, token_expiry = NULL 
                               WHERE emailveriftoken = ?";
                $updateStmt = $conn->prepare($updateQuery);
                $updateStmt->bindParam(1, $cleanedToken, PDO::PARAM_STR);
                
                if ($updateStmt->execute()) {
                    $success = true;
                    $message = 'Your email has been successfully verified!';
                    $iconHtml = '<i class="fas fa-check-circle"></i>';
                } else {
                    $message = 'Error updating your account.';
                    
                }
            }
        } else {
            $message = 'Invalid verification link. Please request a new one.';
            
            // Additional debugging info for token matching issues
            $directQuery = "SELECT TOP 5 username, 
                           SUBSTRING(emailveriftoken, 1, 10) AS token_start,
                           SUBSTRING(emailveriftoken, LEN(emailveriftoken)-9, 10) AS token_end, 
                           LEN(emailveriftoken) AS token_length
                           FROM taaltourismdb.users 
                           WHERE emailveriftoken IS NOT NULL";
            $directResult = $conn->query($directQuery);
            
        }
    } catch (PDOException $e) {
        $message = 'An error occurred during verification. Please contact support.';

    }

    // Escape quotes for JavaScript
    $message = str_replace("'", "\\'", $message);

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Verify Email Address</title>
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
        pre {
            background: #f4f4f4;
            padding: 10px;
            border-radius: 5px;
            overflow-x: auto;
        }
        .debug-container {
            margin-top: 40px;
            padding: 20px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
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
            console.log('Verification page loaded');
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
                console.error('Error in verification process:', error);
            }
        });
    </script>
</body>
</html>
<?php
}
?>