<?php
session_start();
require_once(__DIR__ . '/../../../controllers/busiestMonthstatistics.php');

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
    <title>Admin Dashboard - Busiest Months</title>
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
                <h1>
                    <a href="../monthlyperformance.php" style="text-decoration: none; color: inherit;">
                        <i class="bi bi-arrow-left-circle-fill" style="cursor: pointer;"></i>&nbsp;Busiest Months Statistics
                    </a>
                </h1>
                <span class="date">
                    <h1><?php 
                        date_default_timezone_set('Asia/Manila');
                        echo date('M d, Y | h:i A'); 
                    ?></h1>
                </span>
            </div>
            <div class="statistics">
                <div>
                    <div class="chartcontainer">
                        <!-- Chart canvas -->
                        <canvas id="busiestmonths"></canvas>
                    </div>
                </div>
            </div>
            <div class="summary">
                <div>
                    <h4>Busiest Months:</h4>
                    <?php
                        // Example: You could list out the months with the highest counts
                        // Here we simply loop through the labels and data for demonstration
                        if (isset($busiestMonthsLabels) && isset($busiestMonthsData)) {
                            foreach ($busiestMonthsLabels as $index => $label) {
                                echo "<p>{$label}: " . $busiestMonthsData[$index] . "</p>";
                            }
                        }
                    ?>
                </div>
                <div>
                    <h4>Least Busy Months:</h4>
                    <!-- Add your code here if needed -->
                </div>
                <div>
                    <h4>Total Completed Tours This Year:</h4>
                    <!-- You can pass this value from your controller -->
                    <h4><?php echo isset($totalCompletedYear) ? $totalCompletedYear : 'N/A'; ?></h4>
                </div>
                <div>
                    <button id="downloadPdf" class="bluebutton download bi bi-file-earmark-arrow-down-fill">&nbsp;Download PDF Report</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        // Use the dynamic data provided by the controller for the chart.
        // Ensure that the variables $busiestMonthsLabels and $busiestMonthsData are defined.
        let busiestmonthsCtx = document.getElementById('busiestmonths').getContext('2d');
        let busiestmonthsstats = new Chart(busiestmonthsCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($busiestMonthsLabels); ?>,
                datasets: [{
                    label: 'Accepted Tours',
                    data: <?php echo json_encode($busiestMonthsData); ?>,
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        precision: 0
                    }
                }
            }
        });
    </script>
    <script src="../../../../public/assets/scripts/dashboard.js"></script>
    <script src="../../../../public/assets/scripts/downloadreport.js"></script>
</body>
</html>
