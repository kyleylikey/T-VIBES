<?php
require_once '../../controllers/helpers.php';
require_once '../../controllers/monthlyperformancecontroller.php';
include '../../../includes/auth.php';
require_once '../../config/dbconnect.php';
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
                        <h2>Approved Tours 
                        <?php if ($approvedDiff === 'N/A'): ?>
                            <span class="text-success bi bi-arrow-up-circle-fill">
                                New
                            </span>
                        <?php else: ?>
                            <span class="<?php echo $approvedDiff > 0 ? 'text-success bi bi-arrow-up-circle-fill' : ($approvedDiff < 0 ? 'text-danger bi bi-arrow-down-circle-fill' : 'text-muted bi bi-dash-circle-fill'); ?>">
                                &nbsp;<?php echo abs($approvedDiff); ?>%
                            </span>
                        <?php endif; ?>
                        </h2>
                        <h1><?php echo $approvedToursCurrent; ?></h1>
                        <p>vs. last month <span><?php echo $approvedToursLast; ?></span></p>
                    </div>

                    <!-- Cancelled Tours -->
                    <div class="cancelled">
                        <h2>Cancelled Tours 
                        <?php if ($cancelledDiff === 'N/A'): ?>
                            <span class="text-success bi bi-arrow-up-circle-fill">
                                New
                            </span>
                        <?php else: ?>
                            <span class="<?php echo $cancelledDiff > 0 ? 'text-success bi bi-arrow-up-circle-fill' : ($cancelledDiff < 0 ? 'text-danger bi bi-arrow-down-circle-fill' : 'text-muted bi bi-dash-circle-fill'); ?>">
                                &nbsp;<?php echo abs($cancelledDiff); ?>%
                            </span>
                        <?php endif; ?>
                        </h2>
                        <h1><?php echo $cancelledToursCurrent; ?></h1>
                        <p>vs. last month <span><?php echo $cancelledToursLast; ?></span></p>
                    </div>

                    <!-- Completed Tours -->
                    <div class="completed">
                        <h2>Completed Tours 
                        <?php if ($completedDiff === 'N/A'): ?>
                            <span class="text-success bi bi-arrow-up-circle-fill">
                                New
                            </span>
                        <?php else: ?>
                            <span class="<?php echo $completedDiff > 0 ? 'text-success bi bi-arrow-up-circle-fill' : ($completedDiff < 0 ? 'text-danger bi bi-arrow-down-circle-fill' : 'text-muted bi bi-dash-circle-fill'); ?>">
                                &nbsp;<?php echo abs($completedDiff); ?>%
                            </span>
                        <?php endif; ?>
                        </h2>
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
                                        <p><?php echo $day['count']; ?> tour site visits</p>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p>No data available for busiest days.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <!-- Top Tourist Sites -->
                    <div id="topsites" onclick="location.href='statistics/toptouristsite.php';" style="cursor: pointer;">
                        <h2>Top Tourist Sites this Month</h2>
                        <div class="topthree">
                            <?php if(!empty($topSites)): ?>
                                <?php foreach($topSites as $site): ?>
                                    <div style="padding-top: 0;">
                                        <div style="margin-top: 16px; margin-left: 0; padding: 0; width:100%; height: 160px; display: flex; justify-content: center;">
                                            <?php if(!empty($site['siteimage'])): ?>
                                                <img style="width: 100%; height: 100%; object-fit: cover; margin: 0;" style=""src="/T-VIBES/public/uploads/<?php echo $site['siteimage']; ?>" alt="<?php echo $site['sitename']; ?>" style="max-width:80px; max-height:80px;">
                                            <?php else: ?>
                                                <i class="bi bi-image-fill" style="font-size: 80px;"></i>
                                            <?php endif; ?>
                                            </div>
                                        <p><?php echo $site['sitename']; ?></p>
                                        <p style="display: flex; align-items: flex-end;">
                                            <?php echo $site['visitor_count']; ?> total visitors
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
                    <div class="visitorchart" onclick="location.href='statistics/visitor.php';" style="cursor: pointer; height: 700px; position: relative;">
                        <h2>Visitor Chart</h2>
                        <script>
                            // Data from PHP
                            const chartDays = <?php echo $daysJSON; ?>;
                            const chartVisitors = <?php echo $visitorsJSON; ?>;
                        </script>
                        <canvas style="height:100%; width: 100%;" id="visitorchartpreview"></canvas>
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
