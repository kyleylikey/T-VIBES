<?php
require_once '../config/dbconnect.php';

// Initialize variables
$success = false;
$message = 'Your email is already verified, the link is invalid, or it has expired.';
$iconHtml = '<i class="fas fa-exclamation-circle"></i>';
$debugInfo = 'No matching token found or token expired';

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    $decodedToken = urldecode($token);
    $token = $decodedToken;
    
    // Log to PHP error log
    error_log("Verification attempt with token: " . $token);
    
    $database = new Database();
    $conn = $database->getConnection();

    try {
        // First check without the conditions to diagnose the issue
        $baseQuery = "SELECT email, status, 
                     CONVERT(VARCHAR, token_expiry, 120) as expiry_time,
                     CONVERT(VARCHAR, GETDATE(), 120) as current_datetime
                     FROM [taaltourismdb].[users] 
                     WHERE emailveriftoken = ?";
        
        $baseStmt = $conn->prepare($baseQuery);
        $baseStmt->bindParam(1, $token, PDO::PARAM_STR);
        $baseStmt->execute();
        
        // Log whether we found anything with just the token
        if ($baseStmt->rowCount() > 0) {
            $foundData = $baseStmt->fetch(PDO::FETCH_ASSOC);
            error_log("Token EXISTS in database! Status: " . $foundData['status'] . 
                      ", Expiry: " . $foundData['expiry_time'] . ", Current: " . $foundData['current_datetime']);
            
            $debugInfo = "Token found. Status: " . $foundData['status'] . 
                      ", Expiry: " . $foundData['expiry_time'] . ", Current: " . $foundData['current_datetime'];
            
            // If the token exists but didn't match our full criteria, let's see why
            if ($foundData['status'] !== 'inactive') {
                error_log("Token found but status is not inactive: " . $foundData['status']);
                $message = 'Your email has already been verified.';
                $debugInfo = "Token found but account status is already: " . $foundData['status'];
            } else {
                // Try to parse the dates to see if expiration is the issue
                $expiryTime = strtotime($foundData['expiry_time']);
                $currentTime = strtotime($foundData['current_datetime']);
                
                if ($expiryTime && $currentTime) {
                    error_log("Expiry timestamp: " . $expiryTime . ", Current timestamp: " . $currentTime);
                    if ($expiryTime <= $currentTime) {
                        error_log("Token found but is expired: " . $foundData['expiry_time']);
                        $message = 'Your verification link has expired. Please request a new one.';
                        $debugInfo = "Token expired: " . $foundData['expiry_time'] . " < " . $foundData['current_datetime'];
                    } else {
                        error_log("Token should be valid! Expiry > Current time");
                        
                        // Token is valid, perform verification
                        $updateQuery = "UPDATE [taaltourismdb].[users] SET status = 'active', emailveriftoken = NULL, token_expiry = NULL WHERE emailveriftoken = ?";
                        $updateStmt = $conn->prepare($updateQuery);
                        $updateStmt->bindParam(1, $token);
                        
                        if ($updateStmt->execute() && $updateStmt->rowCount() > 0) {
                            $success = true;
                            $message = 'Your email has been successfully verified!';
                            $iconHtml = '<i class="fas fa-check-circle"></i>';
                            $debugInfo = "Email verification successful for: " . $foundData['email'];
                            error_log("Email verification successful for: " . $foundData['email']);
                        } else {
                            $message = 'Error updating account status. Please try again.';
                            $debugInfo = "Database update failed: " . implode(', ', $updateStmt->errorInfo());
                            error_log("Failed to update user status: " . implode(', ', $updateStmt->errorInfo()));
                        }
                    }
                }
            }
        } else {
            error_log("Token NOT found in database: " . substr($token, 0, 10) . "...");
            $debugInfo = "No matching token found in database";
        }
    } catch (PDOException $e) {
        error_log("Diagnostic query error: " . $e->getMessage());
        $message = 'An error occurred during verification.';
        $debugInfo = "Database error: " . $e->getMessage();
    }
}

// Escape quotes for JavaScript
$message = str_replace("'", "\\'", $message);
$debugInfo = str_replace("'", "\\'", $debugInfo);

    echo "<!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Verify Email Address</title>
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css'>
        <link href='https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;700&family=Raleway:wght@300;400;700&display=swap' rel='stylesheet'>
        <style>
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
            
            .debug-info {
                margin-top: 20px;
                padding: 10px;
                background-color: #f8f9fa;
                border: 1px solid #ddd;
                border-radius: 5px;
                font-family: monospace;
                font-size: 12px;
                white-space: pre-wrap;
                display: none;
            }
            
            .debug-toggle {
                margin-top: 10px;
                text-align: center;
                font-size: 12px;
                color: #6c757d;
                cursor: pointer;
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
                console.log('Success:', " . ($success ? 'true' : 'false') . ");
                console.log('Message:', '$message');
                console.log('Debug info:', '$debugInfo');
                
                // Try-catch to catch and log any errors during SweetAlert execution
                try {
                    Swal.fire({
                        iconHtml: '$iconHtml', 
                        customClass: {
                            title: 'swal2-title-custom',
                            icon: 'swal2-icon-custom',
                            popup: 'swal-custom-popup'
                        },
                        title: '$message',
                        html: '$message<div class=\"debug-toggle\" onclick=\"toggleDebug()\">Show technical details</div><div class=\"debug-info\" id=\"debugInfo\">Token: " . htmlspecialchars(substr($token, 0, 5)) . "...<br>Full debug info: $debugInfo</div>',
                        showConfirmButton: true,
                        confirmButtonText: 'Go to Login',
                        timer: 15000,
                        timerProgressBar: true
                    }).then(() => {
                        console.log('Redirecting to login page...');
                        window.location.href = '../views/frontend/login.php'; 
                    }).catch(error => {
                        console.error('SweetAlert error:', error);
                    });
                } catch (error) {
                    console.error('Error in verification process:', error);
                    document.body.innerHTML += '<div style=\"padding: 20px; color: red;\">Error occurred: ' + error + '</div>';
                    document.body.innerHTML += '<div style=\"padding: 20px;\">Debug info: ' + '$debugInfo' + '</div>';
                }
            });
            
            function toggleDebug() {
                const debugElement = document.getElementById('debugInfo');
                if (debugElement.style.display === 'block') {
                    debugElement.style.display = 'none';
                } else {
                    debugElement.style.display = 'block';
                }
            }
        </script>
    </body>
    </html>";
?>