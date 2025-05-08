<?php
require_once '../config/dbconnect.php';

if (isset($_GET['token'])) {
    $token = trim($_GET['token']);
    
    // Log to PHP error log
    error_log("Verification attempt with token: " . $token);
    
    $database = new Database();
    $conn = $database->getConnection();
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Initialize variables to avoid undefined variable warnings
    $success = false;
    $message = '';
    $iconHtml = '<i class=\"fas fa-exclamation-circle\"></i>';
    $debugInfo = '';

    try {
        $cleanedToken = trim($token);
        echo "Cleaned token: '$cleanedToken'\n"; // Log cleaned token for debugging

        // Check for database connectivity first
        try {
            $testQuery = "SELECT 1";
            $testStmt = $conn->query($testQuery);
            echo "Database connection successful\n";
        } catch (PDOException $e) {
            echo "Database connection test failed: " . $e->getMessage() . "\n";
        }

        // First, check if the token exists without any conditions
        echo "Checking if token exists in database...\n";
        
        // Use the fully qualified table name with schema
        $fullTableName = "[taaltourismdb].[taaltourismdb].[users]";
        
        $checkTokenQuery = "SELECT userid, status, token_expiry FROM $fullTableName WHERE emailveriftoken = ?";
        $checkStmt = $conn->prepare($checkTokenQuery);
        $checkStmt->execute([$cleanedToken]);
        
        echo "Check token query executed\n";
        
        if ($checkStmt && $checkStmt->rowCount() > 0) {
            $userData = $checkStmt->fetch(PDO::FETCH_ASSOC);
            echo "Token found in database!\n";
            echo "User ID: " . $userData['id'] . "\n";
            echo "User status: " . $userData['status'] . "\n";
            echo "Token expiry: " . $userData['token_expiry'] . "\n";
            echo "Current date: " . date('Y-m-d H:i:s') . "\n";
            
            // Check specific conditions
            if ($userData['status'] !== 'inactive') {
                echo "User is already active!\n";
            }
            
            if (strtotime($userData['token_expiry']) < time()) {
                echo "Token has expired!\n";
            }
        } else {
            echo "Token not found in database or error occurred!\n";
            if ($checkStmt) {
                echo "PDO Error Info: " . print_r($checkStmt->errorInfo(), true) . "\n";
            }
            
            // Try another version of the query to get a sample row
            echo "Trying to get a sample row...\n";
            $altQuery = "SELECT TOP 1 userid, status, token_expiry, emailveriftoken FROM $fullTableName";
            $altStmt = $conn->query($altQuery);
            
            if ($altStmt && $altStmt->rowCount() > 0) {
                $sampleRow = $altStmt->fetch(PDO::FETCH_ASSOC);
                echo "Sample row from users table: " . print_r($sampleRow, true) . "\n";
                
                if (isset($sampleRow['emailveriftoken'])) {
                    echo "Token format in database vs provided token:\n";
                    echo "Database token format: " . gettype($sampleRow['emailveriftoken']) . " Length: " . strlen($sampleRow['emailveriftoken']) . "\n";
                    echo "Provided token format: " . gettype($cleanedToken) . " Length: " . strlen($cleanedToken) . "\n";
                } else {
                    echo "emailveriftoken column not found in sample row\n";
                }
            } else {
                echo "Could not get a sample row from the users table\n";
            }
        }

        // Main verification query with proper schema reference
        $query = "SELECT * FROM $fullTableName 
                  WHERE emailveriftoken = ? 
                  AND status = 'inactive' 
                  AND token_expiry > GETDATE()";
        
        echo "Executing main query: $query\n";
        $stmt = $conn->prepare($query);
        
        if (!$stmt) {
            echo "Statement preparation failed: " . print_r($conn->errorInfo(), true) . "\n";
            $debugInfo = "Statement preparation failed";
        } else {
            $stmt->execute([$cleanedToken]);
            echo "Statement executed. Row count: " . $stmt->rowCount() . "\n";
            
            // Debug the error info
            $errorInfo = $stmt->errorInfo();
            echo "PDO Error Info: " . print_r($errorInfo, true) . "\n";
            
            if ($errorInfo[0] !== '00000') {
                $debugInfo = "SQL Error: " . implode(' - ', $errorInfo);
            }

            if ($stmt->rowCount() > 0) {
                $updateQuery = "UPDATE $fullTableName 
                    SET status = 'active', emailveriftoken = NULL, token_expiry = NULL 
                    WHERE emailveriftoken = ?";
                    
                $updateStmt = $conn->prepare($updateQuery);

                if ($updateStmt && $updateStmt->execute([$cleanedToken])) {
                    $success = true;
                    $iconHtml = '<i class=\"fas fa-check-circle\"></i>';
                    $message = 'Your email has been successfully verified!';
                    $debugInfo = 'User account activated successfully';
                } else {
                    $message = 'Failed to verify your email. Please try again later.';
                    $debugInfo = 'Database update failed: ' . ($updateStmt ? implode(', ', $updateStmt->errorInfo()) : 'Unknown error');
                }
            } else {
                $message = 'Your email is already verified, the link is invalid, or it has expired.';
                $debugInfo = 'No matching token found or token expired. Row count: ' . $stmt->rowCount();
            }
        }
    } catch (PDOException $e) {
        $message = 'An error occurred during verification. Please contact support.';
        $debugInfo = 'Database error: ' . $e->getMessage();
        
        echo "PDO Exception caught: " . $e->getMessage() . "\n";
        echo "Error code: " . $e->getCode() . "\n";
        echo "Stack trace: " . $e->getTraceAsString() . "\n";
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
        
        <!-- Debug information section - will be visible on the page -->
        <div class='debug-container'>
            <h2>Debug Information</h2>
            <p>This section is for debugging purposes and should be removed in production.</p>
            <pre id='debug-output'>
Token: <?php echo htmlspecialchars($token); ?>

Success: <?php echo $success ? 'true' : 'false'; ?>

Message: <?php echo htmlspecialchars($message); ?>

Debug Info: <?php echo htmlspecialchars($debugInfo); ?>
            </pre>
        </div>
    </body>
    </html>";
}
?>