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
    <title>Employee Dashboard - Upcoming Tours</title>
    <link rel="stylesheet" href="../../../public/assets/styles/emptourrequest.css">
    <link rel="stylesheet" href="../../../public/assets/styles/main.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        .bluebuttonactive, .bluebuttonnotactive {
            margin-left: 12px;
            margin-top: 12px;
            margin-bottom: 12px;
            border-radius: 50px;
            padding: 8px 20px;
            font-weight: bold;
            font-size: 16px;
            background-color: #4a74f2;
            color: #FFFFFF;
            border: 2px solid #4a74f2;
        }

        .bluebuttonnotactive {
            background-color: #FFFFFF;
            color: #4a74f2;
        }

        .bluebuttonnotactive:hover {
            background-color: #4a74f2;
            color: #FFFFFF;
        }

        .containertours {
            display: flex;
            justify-content: space-between;
            background-color: #fee7eb;
            padding: 20px;
            border-radius: 10px;
            gap: 20px;
        }

        .tour-container {
            background-color: #fee7eb !important;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            flex: 1;
        }

        .tour-container h4 {
            margin-bottom: 10px;
        }

        .left-side {
            display: flex;
            flex-direction: column;
            flex: 1;
        }

        .right-side {
            flex: 2;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .indented {
            margin-left: 50px;
            margin-bottom: 5px;
        }

        .dateandpax {
            margin-top: 50px;
            margin-left: 30px;
        }

        .tour-container .bluebuttonnotactive {
            padding: 16px 56px;
            font-size: 20px;
            display: block;
            margin: 0 auto 10px auto; 
            text-align: center;
        }

        .tour-container .button-container {
            display: flex;
            justify-content: center;
            gap: 20px;
        }

        .tour-container .tour-info div {
            background-color: #fee7eb;
            padding: 10px;
        }

        .button-container {
            background-color: #fee7eb !important;
        }

        .divider {
            width: 80%; 
            border: none;
            height: 2px; 
            background-color: black;
            margin-left: 20%;
        }

        .active-tour {
            background-color: #4a74f2 !important;
            color: white;
        }

        .active-tour h4, .active-tour p {
            color: white;
        }
    </style>
</head>
<body>
<div class="vertnavbar">
    <div class="logocontainer">
        <img src="../../../public/assets/images/headerlogo.jpg" alt="Header Logo">
    </div>
    <ul>
        <li><a href="home.php"><i class="bi bi-grid-fill"></i><span class="nav-text">Overview</span></a></li>
        <li><a href="tourrequests.php"><i class="bi bi-map"></i><span class="nav-text">Tour Requests</span></a></li>
        <li><a class="active"><i class="bi bi-geo"></i><span class="nav-text">Upcoming Tours</span></a></li>
        <li><a href="reviews.php"><i class="bi bi-pencil-square"></i><span class="nav-text">Reviews</span></a></li>
        <li><a href="#sites"><i class="bi bi-image"></i><span class="nav-text">Tourist Sites</span></a></li>
    </ul> 
    <div class="accountcontainer">
        <ul>
            <li class="accountname"><i class="bi bi-person-circle"></i><span class="nav-text">Employee Name</span></li>
            <li><button href="#signout"><i class="bi bi-arrow-left-square-fill"></i><span class="nav-text">Sign Out</span></button></li>
        </ul>
    </div>
</div>
<div class="dashboardcontainer">
    <div class="content">
        <div class="header">
            <h1>Upcoming Tours</h1>
            <span class="date"><h1><?php 
                date_default_timezone_set('Asia/Manila');
                echo date('M d, Y | h:i A'); 
            ?></h1>
            </span>
        </div>
        <button class="bluebuttonnotactive" onclick="window.location.href='upcomingtourstoday.php'">Today</button>
        <button class="bluebuttonactive">All</button>
        <div class="requestlists">
            <div class="containertours">
                <div class="left-side">
                    <h4>11/29/2024</h4>
                    <hr class="divider">
                    <h4>Friday</h4>
                    <div class="tour-container" onclick="toggleActive(this)">
                        <h4>Tourist Name</h4>
                        <p>No. of Sites</p>
                    </div>
                    <h4>Date</h4>
                    <hr class="divider">
                    <h4>Day</h4>
                    <div class="tour-container" onclick="toggleActive(this)">
                        <h4>Tourist Name</h4>
                        <p>No. of Sites</p>
                    </div>
                </div>
                <div class="right-side">
                    <div class="tour-container merged-container">
                    <h4 style="margin-left: 30px;">Tourist Name</h4>
                    <p class="indented">Tour List</p>
                    <br>
                    <table class="dateandpax">
                        <tr>
                            <th style="text-align: left; padding-right: 40px; padding-bottom: 5px;">Tour Date:</th>
                            <th style="text-align: left; padding-left: 60px; padding-bottom: 5px;">Pax:</th>
                        </tr>
                        <tr>
                            <td style="line-height: 1.5;">11/12/2024</td>
                            <td style="line-height: 1.5; padding-left: 60px;">2</td>
                        </tr>
                    </table>
                    <br>
                    <div class="button-container">
                        <button class="bluebuttonnotactive">Edit</button>
                        <button class="bluebuttonnotactive">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="../../../public/assets/scripts/dashboard.js"></script>
<script>
    function toggleActive(selected) {
        document.querySelectorAll('.tour-container').forEach(container => {
            container.classList.remove('active-tour');
        });
        selected.classList.add('active-tour');
    }
</script>
</body>
</html>