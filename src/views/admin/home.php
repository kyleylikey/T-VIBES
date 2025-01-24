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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
    <div class="vertnavbar">
        <div class="logocontainer">
            <img src="../../../public/assets/images/headerlogo.jpg" alt="Header Logo">
        </div>
        <ul>
            <li><a class="active" href="#home"><i class="bi bi-grid-fill"></i><span class="nav-text">Overview</span></a></li>
            <li><a href="#performance"><i class="bi bi-graph-up-arrow"></i><span class="nav-text">Monthly Performance</span></a></li>
            <li><a href="#tourhistory"><i class="bi bi-map"></i><span class="nav-text">Tour History</span></a></li>
            <li><a href="#sites"><i class="bi bi-image"></i><span class="nav-text">Tourist Sites</span></a></li>
            <li><a href="#accounts"><i class="bi bi-people"></i><span class="nav-text">Accounts</span></a></li>
            <li><a href="#logs"><i class="bi bi-person-vcard"></i><span class="nav-text">Employee Logs</span></a></li>
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
                <h1>Overview</h1>
                <span class="date"><h1><?php 
                    date_default_timezone_set('Asia/Manila');
                    echo date('M d, Y | h:i A'); 
                ?></h1></span>
            </div>
            <div class="statistics">
                <div id="requeststhismonth">
                    <h2>Requests this Month &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</h2>
                    <span class="bi bi-map-fill"></span>
                    <h1>32</h1>
                </div>
                <div id="websitevisits">
                    <h2>Website Visits this Month</h2>
                    <span class="bi bi-globe"></span>
                    <h1>89</h1>
                </div>
                <div id="activeemployees">
                    <h2>Active Employees &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</h2>
                    <span class="bi bi-people-fill"></span>
                    <h1>3</h1>
                </div>
                <div class="container">
                        <div id="busiestdays">
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
                                    <p class="marquee-content">Destination Name</p>
                                    <p><i class="bi-star-fill"></i>&nbsp5.0</p>
                                </div>
                                <div class="topsitecontainer">
                                    <span style="display: flex; justify-content: center;"><i class="bi-image-fill" style="font-size: 80px;"></i></span>
                                    <p class="marquee-content">Destination Name</p>
                                    <p><i class="bi-star-fill"></i>&nbsp5.0</p>
                                </div>
                                <div class="topsitecontainer">
                                    <span style="display: flex; justify-content: center;"><i class="bi-image-fill" style="font-size: 80px;"></i></span>
                                    <p class="marquee-content">Destination Name</p>
                                    <p><i class="bi-star-fill"></i>&nbsp5.0</p>
                                </div>
                            </div>
                        </div>
                </div>
                <div class="tablecontainer">
                    <h2>Recent Reviews</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Author</th>
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