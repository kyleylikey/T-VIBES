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
    <title>Employee Dashboard - Overview</title>
    <link rel="stylesheet" href="../../../public/assets/styles/main.css">
    <link rel="stylesheet" href="../../../public/assets/styles/dashboard.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
    <div class="vertnavbar">
        <div class="logocontainer">
            <img src="../../../public/assets/images/headerlogo.jpg" alt="Header Logo">
        </div>
        <ul>
            <li><a class="active"><i class="bi bi-grid-fill"></i><span class="nav-text">Overview</span></a></li>
            <li><a href="tourrequests.php"><i class="bi bi-map"></i><span class="nav-text">Tour Requests</span></a></li>
            <li><a href="#tours"><i class="bi bi-geo"></i><span class="nav-text">Upcoming Tours</span></a></li>
            <li><a href="#reviews"><i class="bi bi-pencil-square"></i><span class="nav-text">Reviews</span></a></li>
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
                <h1>Overview</h1>
                <span class="date"><h1><?php 
                    date_default_timezone_set('Asia/Manila');
                    echo date('M d, Y | h:i A'); 
                ?></h1></span>
            </div>
            <div class="statistics">
                <div id="pendingtours">
                    <h2>Pending Tours</h2>
                    <span class="bi bi-map-fill"></span>
                    <h1>12</h1>
                </div>
                <div id="upcomingtours">
                    <h2>Upcoming Tours</h2>
                    <span class="bi bi-geo-fill"></span>
                    <h1>8</h1>
                </div>
                <div id="pendingreviews">
                    <h2>Pending Reviews</h2>
                    <span class="bi bi-pencil-square"></span>
                    <h1>10</h1>
                </div>
                <div class="tablecontainer">
                    <h2>Latest Tour Requests</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th class="hide-on-small">Submitted</th>
                                <th class="hide-on-small">Destination</th>
                                <th>Travel Date</th>
                                <th>Pax</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>John Doe</td>
                                <td class="hide-on-small">Oct 10, 2023</td>
                                <td class="hide-on-small">3</td>
                                <td>Nov 15, 2023</td>
                                <td>2</td>
                            </tr>
                            <tr>
                                <td>Jane Smith</td>
                                <td class="hide-on-small">Oct 12, 2023</td>
                                <td class="hide-on-small">2</td>
                                <td>Dec 01, 2023</td>
                                <td>4</td>
                            </tr>
                            <tr>
                                <td>Michael Brown</td>
                                <td class="hide-on-small">Oct 14, 2023</td>
                                <td class="hide-on-small">1</td>
                                <td>Nov 20, 2023</td>
                                <td>1</td>
                            </tr>
                            <tr>
                                <td>Emily White</td>
                                <td class="hide-on-small">Oct 16, 2023</td>
                                <td class="hide-on-small">4</td>
                                <td>Dec 05, 2023</td>
                                <td>3</td>
                            </tr>
                            <tr>
                                <td>Chris Green</td>
                                <td class="hide-on-small">Oct 18, 2023</td>
                                <td class="hide-on-small">2</td>
                                <td>Nov 25, 2023</td>
                                <td>5</td>
                            </tr>
                        </tbody>
                    </table>
                    <button class="bluebutton">See All</button>
                </div>
                <div class="tablecontainer">
                    <h2>Recent Reviews</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Author</th>
                                <th>Rating</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>John Doe</td>
                            <td><?php echo generateStarRating(4.5); ?></td>
                            <td>Oct 10, 2023</td>
                        </tr>
                        <tr>
                            <td>Jane Smith</td>
                            <td><?php echo generateStarRating(4.0); ?></td>
                            <td>Oct 12, 2023</td>
                        </tr>
                        <tr>
                            <td>Michael Brown</td>
                            <td><?php echo generateStarRating(3.5); ?></td>
                            <td>Oct 14, 2023</td>
                        </tr>
                        <tr>
                            <td>Emily White</td>
                            <td><?php echo generateStarRating(5.0); ?></td>
                            <td>Oct 16, 2023</td>
                        </tr>
                        <tr>
                            <td>Chris Green</td>
                            <td><?php echo generateStarRating(4.2); ?></td>
                            <td>Oct 18, 2023</td>
                        </tr>
                        </tbody>
                    </table>
                    <button class="bluebutton">See All</button>
                </div>
            </div>
        </div>
    </div>
    <script src="../../../public/assets/scripts/dashboard.js"></script>
</body>
</html>