<?php
require_once '../../controllers/helpers.php';
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
            </div>
        </div>
    </div>
    <script src="../../../public/assets/scripts/dashboard.js"></script>
</body>
</html>