<?php
require_once '../config/dbconnect.php';

if (isset($_GET['token'])) {
    $token = trim($_GET['token']);
    
    // Log to PHP error log
    error_log("Verification attempt with token: " . $token);
    
    $database = new Database();
    $conn = $database->getConnection();
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


    try {
        $cleanedToken = LTRIM(RTRIM($token));
        echo "Cleaned token: '$cleanedToken'\n"; // Log cleaned token for debugging

        $query = "SELECT * FROM [taaltourismdb].[taaltourismdb].[users] WHERE LTRIM(RTRIM(emailveriftoken)) = CAST(:token AS NVARCHAR(MAX)) AND status = 'inactive' AND token_expiry > GETDATE()";
        echo "Executing query: $query\n";
        echo "With token: $cleanedToken\n"; // Log token to make sure it's passed correctly
        $stmt = $conn->prepare($query);
        $stmt->execute([':token' => $cleanedToken]);
        $stmt->execute();


        $success = false; 
        $message = '';
        $iconHtml = '<i class=\"fas fa-exclamation-circle\"></i>';
        $debugInfo = '';

        if ($stmt->rowCount() > 0) {
            $updateQuery = "UPDATE [taaltourismdb].[taaltourismdb].[users] 
                SET status = 'active', emailveriftoken = NULL, token_expiry = NULL 
                WHERE LTRIM(RTRIM(emailveriftoken)) = CAST(:token AS NVARCHAR(MAX))
            ";
            $updateStmt = $conn->prepare($updateQuery);
            $stmt->execute([':token' => $cleanedToken]);


            if ($updateStmt->execute()) {
                $success = true;
                $iconHtml = '<i class=\"fas fa-check-circle\"></i>';
                $message = 'Your email has been successfully verified!';
                $debugInfo = 'User account activated successfully';
            } else {
                $message = 'Failed to verify your email. Please try again later.';
                $debugInfo = 'Database update failed: ' . implode(', ', $updateStmt->errorInfo());
            }
        } else {
            $message = 'Your email is already verified, the link is invalid, or it has expired.';
            $debugInfo = 'No matching token found or token expired';
        }
    } catch (PDOException $e) {
        $message = 'An error occurred during verification. Please contact support.';
        $debugInfo = 'Database error: ' . $e->getMessage();
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
                        showConfirmButton: false, 
                        timer: 3000
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
    </html>";
}
?>