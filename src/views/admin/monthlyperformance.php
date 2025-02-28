<?php
session_start();
require_once '../../controllers/helpers.php';
require_once '../../controllers/monthlyperformancecontroller.php';

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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="../../../public/assets/scripts/main.js"></script>
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
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
                <li><a onclick="logoutConfirm()"><i class="bi bi-arrow-left-square-fill"></i><span class="">Sign Out</span></a></li>
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
                <!-- Tours This Month -->
                <div class="toursthismonth" onclick="location.href='statistics/tourperformance.php';" style="cursor: pointer;">
                    <h2>Tours this Month</h2>
                    <span class="bi bi-map-fill"></span>
                    <h1><?php echo $toursThisMonth; ?></h1>
                </div>
                <!-- Tour Statistics -->
                <div class="tourstatistics" onclick="location.href='statistics/tourperformance.php';" style="cursor: pointer;">
                    <!-- Approved Tours -->
                    <div class="approved">
                        <h2>Approved Tours <span class="bi bi-arrow-up-circle-fill"><?php echo $approvedDiff; ?>%</span></h2>
                        <h1><?php echo $approvedToursCurrent; ?></h1>
                        <p>vs. last month <span><?php echo $approvedToursLast; ?></span></p>
                    </div>
                    <!-- Cancelled Tours -->
                    <div class="cancelled">
                        <h2>Cancelled Tours <span class="bi bi-arrow-down-circle-fill"><?php echo $cancelledDiff; ?>%</span></h2>
                        <h1><?php echo $cancelledToursCurrent; ?></h1>
                        <p>vs. last month <span><?php echo $cancelledToursLast; ?></span></p>
                    </div>
                    <!-- Completed Tours -->
                    <div class="completed">
                        <h2>Completed Tours <span>= <?php echo $completedDiff; ?>%</span></h2>
                        <h1><?php echo $completedToursCurrent; ?></h1>
                        <p>vs. last month <span><?php echo $completedToursLast; ?></span></p>
                    </div>
                </div>
                <div class="container">
                    <!-- Busiest Days -->
                    <div id="busiestdays" onclick="location.href='statistics/busiestmonths.php';" style="cursor: pointer;">
                        <h2>Busiest Days</h2>
                        <div class="busydays">
                            <?php if(!empty($busiestDays)): ?>
                                <?php foreach($busiestDays as $day): ?>
                                    <div class="busydaycontainer">
                                        <h3>Day <?php echo $day['day']; ?></h3>
                                        <h1><?php echo $day['count']; ?></h1>
                                        <p><?php echo $day['count']; ?> tours</p>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p>No data available for busiest days.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <!-- Top Tourist Sites -->
                    <div id="topsites" onclick="location.href='statistics/toptouristsite.php';" style="cursor: pointer;">
                        <h2>Top Tourist Sites</h2>
                        <div class="topthree">
                            <?php if(!empty($topSites)): ?>
                                <?php foreach($topSites as $site): ?>
                                    <div class="topsitecontainer">
                                        <span style="display: flex; justify-content: center;">
                                            <?php if(!empty($site['siteimage'])): ?>
                                                <img src="<?php echo $site['siteimage']; ?>" alt="<?php echo $site['sitename']; ?>" style="max-width:80px; max-height:80px;">
                                            <?php else: ?>
                                                <i class="bi bi-image-fill" style="font-size: 80px;"></i>
                                            <?php endif; ?>
                                        </span>
                                        <p><?php echo $site['sitename']; ?></p>
                                        <p style="display: flex; align-items: flex-end;">
                                            <?php echo generateStarRating($site['ratings']); ?>&nbsp;<?php echo $site['ratings']; ?>
                                        </p>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p>No top sites available.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <!-- Visitor Chart and PDF Report -->
                <div class="tablecontainer">
                    <div class="visitorchart" onclick="location.href='statistics/visitor.php';" style="cursor: pointer;">
                        <h2>Visitor Chart</h2>
                        <canvas id="visitorchartpreview"></canvas>
                    </div>
                    <button class="bluebutton download bi bi-file-earmark-arrow-down-fill">&nbsp;Download PDF Report</button>
                </div>
            </div>
        </div>
    </div>
    <script src="../../../public/assets/scripts/monthlyperformance.js"></script>
    <script src="../../../public/assets/scripts/dashboard.js"></script>
</body> 
</html>
