<?php
require_once '../../../controllers/helpers.php';
require_once '../../../controllers/admin/toptouristsitecontroller.php';
include '../../../../includes/auth.php';
require_once '../../../config/dbconnect.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../../../../public/assets/styles/main.css">
    <link rel="stylesheet" href="../../../../public/assets/styles/dashboard.css">
    <link rel="stylesheet" href="../../../../public/assets/styles/monthlyperformance.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.5.3/jspdf.debug.js"></script>
    <script src="../../../../public/assets/scripts/main.js"></script>
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
</head>
<body>
    <div class="vertnavbar">
        <div class="logocontainer">
            <img src="../../../../public/assets/images/headerlogo.jpg" alt="Header Logo">
        </div>
        <ul>
            <li><a href="../home.php"><i class="bi bi-grid"></i><span class="nav-text">Overview</span></a></li>
            <li><a class="active" href="../monthlyperformance.php"><i class="bi bi-graph-up-arrow"></i><span class="nav-text">Monthly Performance</span></a></li>
            <li><a href="../tourhistory.php"><i class="bi bi-map"></i><span class="nav-text">Tour History</span></a></li>
            <li><a href="../touristsites.php"><i class="bi bi-image"></i><span class="nav-text">Tourist Sites</span></a></li>
            <li><a href="../accounts.php"><i class="bi bi-people"></i><span class="nav-text">Accounts</span></a></li>
            <li><a href="../employeelogs.php"><i class="bi bi-person-vcard"></i><span class="nav-text">Employee Logs</span></a></li>
        </ul> 
        <div class="accountcontainer">
            <ul>
                <li class="accountname"><i class="bi bi-person-circle"></i><span class="nav-text">Manager Name</span></li>
                <li><a onclick="logoutConfirm()"><i class="bi bi-arrow-left-square-fill"></i><span class="">Sign Out</span></a></li>
            </ul>
        </div>
    </div>
    <div class="dashboardcontainer">
        <div class="content">
            <div class="header">
                <h1><a href="../monthlyperformance.php" style="text-decoration: none; color: inherit;"><i class="bi bi-arrow-left-circle-fill" style="cursor: pointer;"></i>&nbspTop Tourist Sites this Year</a></h1>
                <span class="date"><h1><?php 
                    date_default_timezone_set('Asia/Manila');
                    echo date('M d, Y | h:i A'); 
                ?></h1></span>
            </div>
            <div class="statistics">
                <div>
                    <div class="chartcontainer">
                        <canvas id="topsite"></canvas>
                        <script>
                            // Data from PHP
                            const siteNames = <?php echo $siteNamesJSON; ?>;
                            const visitorCounts = <?php echo $visitorCountsJSON; ?>;
                       

                            let topsite = document.getElementById('topsite').getContext('2d');

                            let topsiteChart = new Chart(topsite, {
                                type: 'pie',
                                data: {
                                    labels: siteNames, // Use PHP-generated site names
                                    datasets: [{
                                        label: 'Visitors',
                                        data: visitorCounts, // Use PHP-generated visitor counts
                                        backgroundColor: [
                                            'rgba(75, 192, 192, 0.6)',
                                            'rgba(54, 162, 235, 0.6)',
                                            'rgba(255, 206, 86, 0.6)',
                                            'rgba(255, 99, 132, 0.6)',
                                            'rgba(153, 102, 255, 0.6)',
                                            'rgba(255, 159, 64, 0.6)'
                                        ],
                                        borderColor: [
                                            'rgba(75, 192, 192, 1)',
                                            'rgba(54, 162, 235, 1)',
                                            'rgba(255, 206, 86, 1)',
                                            'rgba(255, 99, 132, 1)',
                                            'rgba(153, 102, 255, 1)',
                                            'rgba(255, 159, 64, 1)'
                                        ],
                                        borderWidth: 1
                                    }]
                                },
                                options: {
                                    plugins: {
                                        legend: {
                                            position: 'right'
                                        }
                                    },
                                    responsive: true,
                                    maintainAspectRatio: false
                                }
                            });
                        </script>
                    </div>
                </div>
            </div>
            <div class="summary">
                <div>
                    <h4>Total Visitors This Year:</h4>
                    <h4><?php echo $totalVisitors; ?></h4>
                </div>
                <div>
                    <button id="downloadPdf" class="bluebutton download bi bi-file-earmark-arrow-down-fill">&nbspDownload PDF Report</button>
                </div>
            </div>
        </div>
    </div>
    <script src="../../../../public/assets/scripts/dashboard.js"></script>
    <script src="../../../../public/assets/scripts/downloadreport.js"></script>
</body>
</html>