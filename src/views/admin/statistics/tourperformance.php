<?php
session_start();

if (!isset($_SESSION['userid'])) {
    header('Location: ../../frontend/login.php'); 
    exit();
}

if ($_SESSION['usertype'] !== 'mngr') {
    echo "<!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Access Denied</title>
        <link rel='stylesheet' href='../../../../public/assets/styles/main.css'>
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
                    window.location.href = '../../frontend/login.php';
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
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../../../../public/assets/styles/main.css">
    <link rel="stylesheet" href="../../../../public/assets/styles/dashboard.css">
    <link rel="stylesheet" href="../../../../public/assets/styles/monthlyperformance.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.5.3/jspdf.debug.js"></script>

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
                <li><button href="#signout"><i class="bi bi-arrow-left-square-fill"></i><span class="nav-text">Sign Out</span></button></li>
            </ul>
        </div>
    </div>
    <div class="dashboardcontainer">
        <div class="content">
            <div class="header">
                <h1><a href="../monthlyperformance.php" style="text-decoration: none; color: inherit;"><i class="bi bi-arrow-left-circle-fill" style="cursor: pointer;"></i>&nbspTour Performance</a></h1>
                <span class="date"><h1><?php 
                    date_default_timezone_set('Asia/Manila');
                    echo date('M d, Y | h:i A'); 
                ?></h1></span>
            </div>
            <div class="statistics">
                <div>
                    <div class="chartcontainer">
                        <canvas id="tourperformance"></canvas>
                        <script>
                            let tourperformance = document.getElementById('tourperformance').getContext('2d');

                            let tourperformanceChart = new Chart(tourperformance, {
                                type: 'line',
                                data: {
                                    labels: ['01/25', '02/5', '03/25', '04/25', '05/25', '06/25', '07/25', '08/25', '09/25', '10/25', '11/25', '12/25'],
                                    datasets: [
                                        {
                                            label: 'Approved Tours',
                                            data: [100, 200, 300, 600, 300, 600, 700, 800, 900, 1000, 1100, 1200],
                                            borderColor: 'rgba(75, 192, 192, 1)',
                                            borderWidth: 2,
                                            fill: false
                                        },
                                        {
                                            label: 'Completed Tours',
                                            data: [90, 180, 270, 540, 270, 540, 630, 720, 810, 900, 990, 1080],
                                            borderColor: 'rgba(153, 102, 255, 1)',
                                            borderWidth: 2,
                                            fill: false
                                        },
                                        {
                                            label: 'Cancelled Tours',
                                            data: [22, 61, 90, 10, 40, 54, 63, 72, 81, 90, 99, 108],
                                            borderColor: 'rgb(255, 102, 102)',
                                            borderWidth: 2,
                                            fill: false
                                        },
                                    ]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                }
                            });
                        </script>
                    </div>
                </div>
            </div>
            <div class="summary">
                <div>
                    <h4>Total Approved Tours This Year:</h4>
                    <h4>600</h4>
                </div>
                <div>
                    <h4>Total Completed Tours This Year:</h4>
                    <h4>449</h4>
                </div>
                <div>
                    <h4>Total Cancelled Tours This Year:</h4>
                    <h4>49</h4>
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