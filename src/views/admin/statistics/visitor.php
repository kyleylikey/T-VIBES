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
    <link rel="stylesheet" href="../../../../public/assets/styles/main.css">
    <link rel="stylesheet" href="../../../../public/assets/styles/dashboard.css">
    <link rel="stylesheet" href="../../../../public/assets/styles/monthlyperformance.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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
                <li><a href="../../../controllers/logout.php" onclick="return confirm('Are you sure you want to sign out?');"><i class="bi bi-arrow-left-square-fill"></i><span class="">Sign Out</span></a></li>
            </ul>
        </div>
    </div>
    <div class="dashboardcontainer">
        <div class="content">
            <div class="header">
                <h1><a href="../monthlyperformance.php" style="text-decoration: none; color: inherit;"><i class="bi bi-arrow-left-circle-fill" style="cursor: pointer;"></i>&nbspVisitor Statistics</a></h1>
                <span class="date"><h1><?php 
                    date_default_timezone_set('Asia/Manila');
                    echo date('M d, Y | h:i A'); 
                ?></h1></span>
            </div>
            <div class="statistics">
                <div>
                    <div class="chartcontainer">
                        <canvas id="visitor"></canvas>
                        <script>
                            let visitor = document.getElementById('visitor').getContext('2d');

                            let visitorChart = new Chart(visitor, {
                                type: 'line',
                                data: {
                                    labels: ['01/25', '02/5', '03/25', '04/25', '05/25', '06/25', '07/25', '08/25', '09/25', '10/25', '11/25', '12/25'],
                                    datasets: [
                                        {
                                            label: 'Visitors',
                                            data: [100, 200, 300, 600, 300, 600, 700, 800, 900, 1000, 1100, 1200],
                                            borderColor: 'rgba(75, 192, 192, 1)',
                                            borderWidth: 2,
                                            fill: false
                                        }
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
                    <h4>Total Visitors This Year:</h4>
                    <h4>6000</h4>
                </div>
                <div>
                    <button class="bluebutton download bi bi-file-earmark-arrow-down-fill">&nbspDownload PDF Report</button>
                </div>
            </div>
        </div>
    </div>
    <script src="../../../../public/assets/scripts/dashboard.js"></script>
</body>
</html>