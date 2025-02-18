<?php
session_start();
require_once '../../controllers/helpers.php';

if (!isset($_SESSION['userid'])) {
    header('Location: ../frontend/login.php'); 
    exit();
}

if ($_SESSION['usertype'] !== 'mngr') {
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

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../../../public/assets/styles/main.css">
    <link rel="stylesheet" href="../../../public/assets/styles/dashboard.css">
    <link rel="stylesheet" href="../../../public/assets/styles/monthlyperformance.css">
    <link rel="stylesheet" href="../../../public/assets/styles/tourrequest.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../../../public/assets/styles/emptourrequest.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="vertnavbar">
        <div class="logocontainer">
            <img src="../../../public/assets/images/headerlogo.jpg" alt="Header Logo">
        </div>
        <ul>
            <li><a href="home.php"><i class="bi bi-grid"></i><span class="nav-text">Overview</span></a></li>
            <li><a href="monthlyperformance.php"><i class="bi bi-graph-up-arrow"></i><span class="nav-text">Monthly Performance</span></a></li>
            <li><a class = "active" href="tourhistory.php"><i class="bi bi-map-fill"></i><span class="nav-text">Tour History</span></a></li>
            <li><a href="touristsites.php"><i class="bi bi-image"></i><span class="nav-text">Tourist Sites</span></a></li>
            <li><a href="accounts.php"><i class="bi bi-people"></i><span class="nav-text">Accounts</span></a></li>
            <li><a href="employeelogs.php"><i class="bi bi-person-vcard"></i><span class="nav-text">Employee Logs</span></a></li>
        </ul> 
        <div class="accountcontainer">
            <ul>
                <li class="accountname"><i class="bi bi-person-circle"></i><span class="nav-text">Manager Name</span></li>
                <li><a href="../../controllers/logout.php" onclick="return confirm('Are you sure you want to sign out?');"><i class="bi bi-arrow-left-square-fill"></i><span class="">Sign Out</span></a></li>
            </ul>
        </div>
    </div>
    <div class="dashboardcontainer">
        <div class="content">
            <div class="header">
                <h1>Tour History</h1>
                <span class="date"><h1><?php 
                    date_default_timezone_set('Asia/Manila');
                    echo date('M d, Y | h:i A'); 
                ?></h1></span>
            </div>
            <div class="tabs">
                <button class="tabbutton active" onclick="setActiveTab(this)">Completed</button>
                <button class="tabbutton" onclick="setActiveTab(this)">Cancelled</button>
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
            <div id="requestModal" class="modal">
                <div class="modal-content">
                <span id="closeforsmall" class="close">&times;</span>
                    <div class="tourcontainer">
                        <table class="sitelist">
                            <tr class="site">
                                <td class="sitenumber">
                                    <h2 class="circle">1</h2>
                                </td>
                                <td class="sitecontainer">
                                    <div class="siteimage">
                                        <i class="bi bi-image"></i>
                                    </div>
                                    <div class="siteinfo">
                                        <div class="sitename"><h3>Site Name</h3></div>
                                        <div></div>
                                        <div class="price"><h3>P100.00</h3></div>
                                        <div class="filler3"></div>
                                        <div class="filler2"></div>
                                    </div>
                                </td>
                            </tr>
                            <tr class="site">
                                <td class="sitenumber">
                                    <h2 class="circle">2</h2>
                                </td>
                                <td class="sitecontainer">
                                    <div class="siteimage">
                                        <i class="bi bi-image"></i>
                                    </div>
                                    <div class="siteinfo">
                                        <div class="sitename"><h3>Site Name</h3></div>
                                        <div></div>
                                        <div class="price"><h3>P100.00</h3></div>
                                        <div class="filler3"></div>
                                        <div class="filler2"></div>
                                    </div>
                                </td>
                            </tr>
                            <tr class="site">
                                <td class="sitenumber">
                                    <h2 class="circle">3</h2>
                                </td>
                                <td class="sitecontainer">
                                    <div class="siteimage">
                                        <i class="bi bi-image"></i>
                                    </div>
                                    <div class="siteinfo">
                                        <div class="sitename"><h3>Site Name</h3></div>
                                        <div></div>
                                        <div class="price"><h3>P100.00</h3></div>
                                        <div class="filler3"></div>
                                        <div class="filler2"></div>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="tourfees">
                        <h2>Date Created</h2>
                        <p>January 12, 2025</p>
                        <h2>Number of Visitors</h2>
                        <p>12</p>
                        <br>
                        <br>
                        <h2>Estimated Fees</h2>
                        <table>
                            <tr>
                                <td>Site Name</td>
                                <td>P300.00</td>
                            </tr>
                            <tr>
                                <td>Site Name</td>
                                <td>P0.00</td>
                            </tr>
                            <tr>
                                <td>Site Name</td>
                                <td>P300.00</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td style="font-weight: bold;">P600.00</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td style="font-weight: bold;">x 17 pax</td>
                            </tr>
                            <tfoot>
                                <tr>
                                    <td>&nbsp</td>
                                </tr>
                                <tr>
                                    <td><h2>Total:</h2></td>
                                    <td><h2>P1500</h2></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <span id="closeforbig" class="close">&times;</span>
                </div>
            </div>
        </div>
    </div>
    <script src="../../../public/assets/scripts/dashboard.js"></script>
    <script src="../../../public/assets/scripts/monthlyperformance.js"></script>
</body>
</html>