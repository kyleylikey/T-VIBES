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
        <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css'>
        <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css' rel='stylesheet' integrity='sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH' crossorigin='anonymous'>
        <link href='https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;700&family=Raleway:wght@300;400;700&display=swap' rel='stylesheet'>
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
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

            .swal-custom-btn {
                padding: 10px 20px !important;
                font-size: 16px !important;
                font-weight: bold !important;
                border: 2px solid #102E47 !important;
                border-radius: 25px !important;
                background-color: #FFFFFF !important;
                color: #434343 !important;
                cursor: pointer !important;
                transition: all 0.3s ease !important;
            }

            .swal-custom-btn:hover {
                background-color: #102E47 !important;
                color: #FFFFFF !important;
            }
        </style>
    </head>
    <body>
    <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js' integrity='sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz' crossorigin='anonymous'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/js/all.min.js'></script>
    <script>
        setTimeout(function() {
            Swal.fire({
                iconHtml: '<i class=\"fas fa-exclamation-circle\"></i>',
                customClass: {
                    title: 'swal2-title-custom',
                    icon: 'swal2-icon-custom',
                    popup: 'swal-custom-popup',
                    confirmButton: 'swal-custom-btn',
                    cancelButton: 'swal-custom-btn'
                },
                title: 'Access Denied! You do not have permission to access this page.',
                showConfirmButton: false,
                timer: 3000
            }).then(() => {
                window.location.href = '/T-VIBES/src/views/frontend/login.php';
            });
        }, 100);
    </script>
    </body>
    </html>";
    exit();
}
?>