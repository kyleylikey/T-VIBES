<?php
require_once '../../controllers/helpers.php';
require_once '../../controllers/admin/homecontroller.php';
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="../../../public/assets/scripts/main.js"></script>
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
</head>
<body>
    <div class="vertnavbar">
        <div class="logocontainer">
            <img src="../../../public/assets/images/headerlogo.jpg" alt="Header Logo">
        </div>
        <ul>
            <li><a class="active" href="home.php"><i class="bi bi-grid-fill"></i><span class="nav-text">Overview</span></a></li>
            <li><a href="monthlyperformance.php"><i class="bi bi-graph-up-arrow"></i><span class="nav-text">Monthly Performance</span></a></li>
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
                <h1>Overview</h1>
                <span class="date"><h1><?php 
                    date_default_timezone_set('Asia/Manila');
                    echo date('M d, Y | h:i A'); 
                ?></h1></span>
            </div>
            <div class="statistics">
                <div id="requeststhismonth">
                    <h2>Requests Left this Month &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</h2>
                    <span class="bi bi-map-fill"></span>
                    <h1><?php echo $requestcount; ?></h1>
                </div>
                <div id="websitevisits">
                    <h2>Website Visits this Month</h2>
                    <span class="bi bi-globe"></span>
                    <h1><?php echo $monthlyVisits; ?></h1>
                    <p>Total: <?php echo $totalVisits; ?></p>
                </div>
                <div id="activeemployees">
                    <h2>Active Employees &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</h2>
                    <span class="bi bi-people-fill"></span>
                    <h1><?php echo $activeempcount; ?></h1>
                </div>
                <div class="container">
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
                        <div id="topsites" onclick="location.href='statistics/toptouristsite.php';" style="cursor: pointer;">
                        <h2>Top Tourist Sites</h2>
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
                <div class="tablecontainer">
                    <h2>Recent Ratings</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Site</th>
                                <th>Rating</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody> <!-- Sample data: SHOW ONLY 8 RESULTS FOR HOME PAGE-->
                        <tr>
                            <td>John Doe</td>
                            <td><?php echo generateStarRating(4.5); ?></td>
                            <td>Oct 10, 2023</td>
                        </tr>
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