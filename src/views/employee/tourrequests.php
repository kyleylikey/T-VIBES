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
                color: #333;
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
    <title>Employee Dashboard - Tour Requests</title>
    <link rel="stylesheet" href="../../../public/assets/styles/main.css">
    <link rel="stylesheet" href="../../../public/assets/styles/emptourrequest.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<div class="vertnavbar">
    <div class="logocontainer">
        <img src="../../../public/assets/images/headerlogo.jpg" alt="Header Logo">
    </div>
    <ul>
        <li><a href="home.php"><i class="bi bi-grid-fill"></i><span class="nav-text">Overview</span></a></li>
        <li><a class="active"><i class="bi bi-map"></i><span class="nav-text">Tour Requests</span></a></li>
        <li><a href="upcomingtourstoday.php"><i class="bi bi-geo"></i><span class="nav-text">Upcoming Tours</span></a></li>
        <li><a href="reviews.php"><i class="bi bi-pencil-square"></i><span class="nav-text">Reviews</span></a></li>
        <li><a href="sites.php"><i class="bi bi-image"></i><span class="nav-text">Tourist Sites</span></a></li>
    </ul> 
    <div class="accountcontainer">
        <ul>
            <li class="accountname"><i class="bi bi-person-circle"></i><span class="nav-text">Employee Name</span></li>
            <li><a onclick="logoutConfirm()"><i class="bi bi-arrow-left-square-fill"></i><span class="">Sign Out</span></a></li>
        </ul>
    </div>
</div>
<div class="dashboardcontainer">
    <div class="content">
        <div class="header">
            <h1>Tour Requests</h1>
            <span class="date"><h1><?php 
                date_default_timezone_set('Asia/Manila');
                echo date('M d, Y | h:i A'); 
            ?></h1>
            </span>
        </div>
        <div class="requestlists">
            <div class="tablecontainerrequests">
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Submitted On</th>
                            <th>Destinations</th>
                            <th>Travel Date</th>
                            <th>Pax</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php for ($i = 0; $i < 8; $i++): ?>
                        <tr>
                            <td><i>User</i></td>
                            <td><i>DD MMM YY</i></td>
                            <td>2</td>
                            <td><i>DD MMM YY</i></td>
                            <td>2</td>
                        </tr>
                    <?php endfor; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script src="../../../public/assets/scripts/dashboard.js"></script>
<script src='../../../public/assets/scripts/main.js'></script>
</body>
</html>