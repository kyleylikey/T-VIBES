<?php
require_once '../config/dbconnect.php';

if (isset($_GET['email'])) {
    $email = urldecode($_GET['email']);
    
    $database = new Database();
    $conn = $database->getConnection();

    $query = "SELECT * FROM users WHERE email = :email AND status = 'inactive'";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    $success = false; 
    $message = '';
    $iconHtml = '<i class=\"fas fa-exclamation-circle\"></i>';
    $customClass = 'swal2-icon swal2-error-icon';

    if ($stmt->rowCount() > 0) {
        $updateQuery = "UPDATE users SET status = 'active' WHERE email = :email";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bindParam(':email', $email);

        if ($updateStmt->execute()) {
            $success = true;
            $iconHtml = '<i class=\"fas fa-check-circle\"></i>';
            $customClass = 'swal2-icon swal2-success-icon';
            $message = 'Your email has been successfully verified!';
        } else {
            $message = 'Failed to verify your email. Please try again later.';
        }
    } else {
        $message = 'Your email is already verified or the link is invalid.';
    }

    echo "<!DOCTYPE html>
    <html>
    <head>
        <link rel='stylesheet' href='../../public/assets/styles/main.css'>
        <link rel='stylesheet' href='../../public/assets/styles/login.css'>
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css'>
        <style>
            .swal2-icon.swal2-error-icon, .swal2-icon.swal2-success-icon {
                border: none;
                font-size: 10px;
                display: flex;
                align-items: center;
                justify-content: center;
                width: 60px;
                height: 60px;
                color: #333;
            }
            .swal2-popup {
                border-radius: 12px;
                padding: 20px;
            }
        </style>
    </head>
    <body>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    iconHtml: '$iconHtml', 
                    customClass: {
                        icon: '$customClass',
                        popup: 'swal2-popup'
                    },
                    html: '<p style=\'font-size: 24px; font-weight: bold; text-align: center;\'>$message</p>',
                    showConfirmButton: false, 
                    timer: 3000
                }).then(() => {
                    window.location.href = '../views/frontend/login.php'; 
                });
            });
        </script>
    </body>
    </html>";
}
?>