<?php
session_start();
require_once '../../controllers/helpers.php';

if (!isset($_SESSION['userid'])) {
    header('Location: ../frontend/login.php'); 
    exit();
}

if ($_SESSION['usertype'] !== 'emp') {
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
                    window.location.href = '../frontend/login.php';
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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Dashboard - </title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;700&family=Raleway:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
        * {
            font-family: 'Nunito', sans-serif;
            box-sizing: border-box;
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 250px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 20px;
            background-color: white;
            z-index: 1000;
            transition: all 0.3s ease-in-out;
        }

        .sidebar img {
            max-width: 100%; 
            height: auto;
            display: block; 
            margin: auto;
            transition: all 0.3s ease-in-out; 
        }

        .menu-section {
            margin-top: auto;
            margin-bottom: auto;
        }

        .nav-link {
            color: #102E47 !important;
            padding: 10px;
            border-radius: 5px;
            transition: background 0.3s ease;
        }

        .nav-link.active {
            background-color: #102E47 !important;
            color: white !important;
            font-weight: bold;
        }

        .nav-link i {
            color: inherit; 
        }

        .nav-link:hover {
            background-color: #102E47 !important; 
            color: white !important;
            transition: background 0.3s ease;
        }

        .content-container {
            background-color: #E7EBEE;
            padding: 20px;
            border-radius: 10px;
        }

        .main-content {
            margin-left: 260px;
            padding: 20px;
            transition: all 0.3s ease-in-out;
            width: calc(100% - 260px); 
        }

        .content-container h2 {
            font-weight: bold;
        }

        @media (max-width: 912px) {
            .sidebar {
                width: 200px; 
                padding: 15px;
            }

            .nav-link {
                font-size: 14px; 
                padding: 8px; 
            }

            .menu-section {
                margin-top: auto;
                margin-bottom: auto;
                padding: 10px 0; 
            }

            .main-content {
                margin-left: 200px;
                width: calc(100% - 200px);
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 80px;
                padding: 10px;
            }

            .sidebar img {
                max-width: 60px; 
            }

            .main-content {
                margin-left: 90px; 
                width: calc(100% - 90px); 
            }

            .nav-link {
                text-align: center;
                padding: 10px;
            }

            .nav-link span {
                display: none; 
            }

            .nav-link.active {
                background-color: #102E47 !important;
                color: white !important;
                border-radius: 5px;
            }

            .nav-link:hover {
                background-color: #102E47 !important; 
                color: white !important;
                transition: background 0.3s ease;
            }
        }

        @media (max-width: 600px) {
            .sidebar {
                width: 70px;
                padding: 5px;
            }

            .main-content {
                margin-left: 75px;
                width: calc(100% - 75px);
            }

            .nav-link i {
                font-size: 20px;
            }

            .nav-link span {
                display: none;
            }

            .nav-link:hover {
                background-color: #102E47 !important; 
                color: white !important;
                transition: background 0.3s ease;
            }
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="pt-4 pb-1 px-2 text-center">
            <a href="#" class="text-decoration-none">
                <img src="../../../public/assets/images/headerlogo.jpg" alt="Header Logo" class="img-fluid">
            </a>
        </div>

        
        <div class="menu-section">
            <ul class="nav nav-pills flex-column mb-4">
                <li class="nav-item mb-4">
                    <a href="" class="nav-link text-dark">
                        <i class="bi bi-grid"></i>
                        <span class="d-none d-sm-inline">Overview</span>
                    </a>
                </li>
                <li class="nav-item mb-4">
                    <a href="" class="nav-link text-dark">
                        <i class="bi bi-map"></i>
                        <span class="d-none d-sm-inline">Tour Requests</span>
                    </a>
                </li>
                <li class="nav-item mb-4">
                    <a href="" class="nav-link text-dark">
                        <i class="bi bi-geo"></i>
                        <span class="d-none d-sm-inline">Upcoming Tours</span>
                    </a>
                </li>
                <li class="nav-item mb-4">
                    <a href="" class="nav-link text-dark">
                        <i class="bi bi-pencil-square"></i>
                        <span class="d-none d-sm-inline">Reviews</span>
                    </a>
                </li>
                <li class="nav-item mb-4">
                    <a href="" class="nav-link text-dark">
                        <i class="bi bi-image"></i>
                        <span class="d-none d-sm-inline">Tourist Sites</span>
                    </a>
                </li>
            </ul>
        </div>
        
        <ul class="nav nav-pills flex-column mb-4">
            <li class="nav-item mb-3">
                <a href="" class="nav-link active">
                    <i class="bi bi-person-circle"></i>
                    <span class="d-none d-sm-inline">Employee Name</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="" class="nav-link text-dark">
                    <i class="bi bi-box-arrow-right"></i>
                    <span class="d-none d-sm-inline">Sign Out</span>
                </a>
            </li>
        </ul>
    </div>

    <div class="main-content">
        <div class="content-container">
            
        </div>
    </div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>