<?php
session_start();

if (!isset($_SESSION['userid'])) {
    header('Location: /T-VIBES/src/views/frontend/login.php');
    exit();
}

$current_page = $_SERVER['REQUEST_URI'];
$access_control = [
    'mngr' => ['/src/views/admin/'],
    'emp' => ['/src/views/employee/'],
    'trst' => ['/src/views/frontend/tours'],
];

$allowed = false;
foreach ($access_control as $role => $paths) {
    foreach ($paths as $path) {
        if (strpos($current_page, $path) !== false && $_SESSION['usertype'] === $role) {
            $allowed = true;
            break 2;
        }
    }
}

if (!$allowed) {
    echo "<!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Access Denied</title>
        <link rel='stylesheet' href='../../../public/assets/styles/main.css'>
        <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css'>
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        <script>
            setTimeout(function() {
                Swal.fire({
                    iconHtml: '<i class=\"fas fa-exclamation-circle\"></i>',
                    customClass: {
                        icon: 'swal2-icon swal2-error-icon',
                    },
                    html: '<p style=\"font-size: 24px; font-weight: bold;\">Access Denied! You do not have permission to access this page.</p>',
                    showConfirmButton: false,
                    timer: 3000
                }).then(() => {
                    window.location.href = '/T-VIBES/src/views/frontend/login.php';
                });
            }, 100);
        </script>
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
                color: #102E47;
            }
        </style>
    </head>
    <body></body>
    </html>";
    exit();
}
?>