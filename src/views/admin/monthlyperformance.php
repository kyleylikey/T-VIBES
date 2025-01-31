<?php
session_start();
require_once '../../controllers/helpers.php';

if (!isset($_SESSION['userid'])) {
    header('Location: ../frontend/login.php'); 
    exit();
}
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>
<body>
    <div class="vertnavbar">
        <div class="logocontainer">
            <img src="../../../public/assets/images/headerlogo.jpg" alt="Header Logo">
        </div>
        <ul>
            <li><a href="home.php"><i class="bi bi-grid"></i><span class="nav-text">Overview</span></a></li>
            <li><a class="active" href="monthlyperformance.php"><i class="bi bi-graph-up-arrow"></i><span class="nav-text">Monthly Performance</span></a></li>
            <li><a href="tourhistory.php"><i class="bi bi-map"></i><span class="nav-text">Tour History</span></a></li>
            <li><a href="touristsites.php"><i class="bi bi-image"></i><span class="nav-text">Tourist Sites</span></a></li>
            <li><a href="accounts.php"><i class="bi bi-people"></i><span class="nav-text">Accounts</span></a></li>
            <li><a href="employeelogs.php"><i class="bi bi-person-vcard"></i><span class="nav-text">Employee Logs</span></a></li>
        </ul> 
        <div class="accountcontainer">
            <ul>
                <li class="accountname"><i class="bi bi-person-circle"></i><span class="nav-text">Manager Name</span></li>
                <li><button href="#signout"><i class="bi bi-arrow-left-square-fill"></i><span class="nav-text">Sign Out</span></button></li>
            </ul>
        </div>
    </div>
    <div class="dashboardcontainer">
        <div class="content">
            <div class="header">
                <h1>Monthly Performance Statistics</h1>
                <span class="date"><h1><?php 
                    date_default_timezone_set('Asia/Manila');
                    echo date('M d, Y | h:i A'); 
                ?></h1></span>
            </div>
            <div class="statistics">
                <div class="toursthismonth">
                    <h2>Tours this Month &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</h2>
                    <span class="bi bi-map-fill"></span>
                    <h1>32</h1>
                </div>
                <div class="tourstatistics">
                    <div class="approved">
                        <h2>Approved Tours</h2>
                        <span class="bi bi-check-circle-fill"></span>
                        <h1>25</h1>
                    </div>
                    <div class="cancelled">
                        <h2>Cancelled Tours</h2>
                        <span class="bi bi-x-circle-fill"></span>
                        <h1>7</h1>
                    </div>
                    <div class="completed">
                        <h2>Completed Tours</h2>
                        <span class="bi bi-check2-circle"></span>
                        <h1>20</h1>
                    </div>
                </div>
                <div class="container">
                        <div id="busiestdays" onclick="location.href='statistics/busiestmonths.php';" style="cursor: pointer;">
                            <h2>Busiest Days</h2>
                            <div class="busydays">
                                <div class="busydaycontainer">
                                    <h3>Jan</h3>
                                    <h1>12</h1>
                                    <p>7 tours</p>
                                </div>
                                <div class="busydaycontainer">
                                    <h3>Jan</h3>
                                    <h1>18</h1>
                                    <p>5 tours</p>
                                </div>
                                <div class="busydaycontainer">
                                    <h3>Jan</h3>
                                    <h1>19</h1>
                                    <p>3 tours</p>
                                </div>
                            </div>
                        </div>
                        <div id="topsites">
                            <h2>Top Tourist Sites</h2>
                            <div class="topthree">
                                <div class="topsitecontainer">
                                    <span style="display: flex; justify-content: center;"><i class="bi-image-fill" style="font-size: 80px;"></i></span><!-- Replace with image -->
                                    <p>Name</p>
                                    <p style="display: flex; align-items: flex-end;"><i class="bi-star-fill"></i>&nbsp5.0</p>
                                </div>
                                <div class="topsitecontainer">
                                    <span style="display: flex; justify-content: center;"><i class="bi-image-fill" style="font-size: 80px;"></i></span>
                                    <p>Destination Name</p>
                                    <p><i class="bi-star-fill"></i>&nbsp5.0</p>
                                </div>
                                <div class="topsitecontainer">
                                    <span style="display: flex; justify-content: center;"><i class="bi-image-fill" style="font-size: 80px;"></i></span>
                                    <p>Destination Name</p>
                                    <p><i class="bi-star-fill"></i>&nbsp5.0</p>
                                </div>
                            </div>
                        </div>
                </div>
                <div class="tablecontainer">
                    <div class="visitorchart">
                        <h2>Visitor Chart</h2>
                        <canvas id="visitorchartpreview"></canvas>
                    </div>
                <button class="bluebutton download bi bi-file-earmark-arrow-down-fill">&nbspDownload PDF Report</button>
                </div>
            </div>
        </div>
    </div>
    <script src="../../../public/assets/scripts/dashboard.js"></script>
    <script src="../../../public/assets/scripts/monthlyperformance.js"></script>
</body>
</html>