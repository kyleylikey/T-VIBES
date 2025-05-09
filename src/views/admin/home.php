<?php
include '../../../includes/auth.php';
require_once '../../config/dbconnect.php';

$database = new Database();
$conn = $database->getConnection();

$tourQuery = "SELECT COUNT(DISTINCT tourid) AS tour_requests 
              FROM [taaltourismdb].[tour] 
              WHERE status = 'submitted' 
              AND MONTH(date) = MONTH(GETDATE()) 
              AND YEAR(date) = YEAR(GETDATE())";

$tourStmt = $conn->prepare($tourQuery);
$tourStmt->execute();
$tourResult = $tourStmt->fetch(PDO::FETCH_ASSOC);
$tourRequests = $tourResult['tour_requests'] ?? 0;

$counterDir = __DIR__ . '/../../data/';
$totalCountFile = $counterDir . 'total_visits.txt';
$monthlyCountFile = $counterDir . date('Y_m') . '_visits.txt';

$totalVisits = (file_exists($totalCountFile)) ? (int)file_get_contents($totalCountFile) : 0;
$monthlyVisits = (file_exists($monthlyCountFile)) ? (int)file_get_contents($monthlyCountFile) : 0;

$employeeQuery = "SELECT COUNT(*) AS active_employees FROM [taaltourismdb].[users] WHERE usertype = 'emp' AND status = 'active'";
$employeeStmt = $conn->prepare($employeeQuery);
$employeeStmt->execute();
$employeeResult = $employeeStmt->fetch(PDO::FETCH_ASSOC);
$activeEmployees = $employeeResult['active_employees'] ?? 0;

$busiestDaysQuery = "
        SELECT TOP 3 
        CAST(date AS DATE) AS tour_date, 
        COUNT(DISTINCT tourid) AS total_tours 
    FROM 
        [taaltourismdb].[tour] 
    WHERE 
        status = 'accepted' 
        AND MONTH(date) = MONTH(GETDATE()) 
        AND YEAR(date) = YEAR(GETDATE()) 
    GROUP BY 
        CAST(date AS DATE) 
    ORDER BY 
        total_tours DESC;
    ";

$busiestDaysStmt = $conn->prepare($busiestDaysQuery);
$busiestDaysStmt->execute();
$busiestDays = $busiestDaysStmt->fetchAll(PDO::FETCH_ASSOC);

$topSitesQuery = "
        SELECT TOP 3 
        s.siteid, 
        s.sitename, 
        s.siteimage, 
        SUM(t.companions) AS total_visitors
    FROM 
        [taaltourismdb].[tour] t
    LEFT JOIN 
        [taaltourismdb].[sites] s ON t.siteid = s.siteid
    WHERE 
        t.status = 'accepted'
        AND MONTH(t.date) = MONTH(GETDATE()) 
        AND YEAR(t.date) = YEAR(GETDATE())
    GROUP BY 
        s.siteid, s.sitename, s.siteimage
    ORDER BY 
        total_visitors DESC;
    ";

$topSitesStmt = $conn->prepare($topSitesQuery);
$topSitesStmt->execute();
$topSites = $topSitesStmt->fetchAll(PDO::FETCH_ASSOC);

$recentLogsQuery = "
    SELECT TOP 6 u.name, l.action 
    FROM [taaltourismdb].[logs] l
    JOIN [taaltourismdb].[users] u ON l.userid = u.userid
    WHERE u.usertype = 'emp'
    ORDER BY l.datetime DESC
    ";

$recentLogsStmt = $conn->prepare($recentLogsQuery);
$recentLogsStmt->execute();
$recentLogs = $recentLogsStmt->fetchAll(PDO::FETCH_ASSOC);

$userid = $_SESSION['userid'];
$query = "SELECT TOP 1 name FROM [taaltourismdb].[users] WHERE userid = :userid";
$stmt = $conn->prepare($query);
$stmt->bindParam(':userid', $userid);
$stmt->execute();
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

$adminName = $admin ? htmlspecialchars($admin['name']) : "Admin";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Overview</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;700&family=Raleway:wght@300;400;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="../../../public/assets/styles/admin/home.css">
</head>
<body>
    
<div class="sidebar">
    <div class="pt-4 pb-1 px-2 text-center">
        <a href="#" class="text-decoration-none">
            <img src="../../../public/assets/images/headerlogo.jpg" alt="Header Logo" class="img-fluid">
        </a>
    </div>

    <div class="menu-section">
        <ul class="nav nav-pills flex-column mb-4">
            <li class="nav-item mb-4">
                <a href="" class="nav-link active">
                    <i class="bi bi-grid-fill"></i>
                    <span class="d-none d-sm-inline">Dashboard</span>
                </a>
            </li>
            <li class="nav-item mb-4">
                <a href="monthlyperformance.php" class="nav-link">
                    <i class="bi bi-bar-chart-line"></i>
                    <span class="d-none d-sm-inline">Monthly Performance</span>
                </a>
            </li>
            <li class="nav-item mb-4">
                <a href="tourhistory.php" class="nav-link">
                    <i class="bi bi-map"></i>
                    <span class="d-none d-sm-inline">Tour History</span>
                </a>
            </li>
            <li class="nav-item mb-4">
                <a href="touristsites.php" class="nav-link">
                    <i class="bi bi-image"></i>
                    <span class="d-none d-sm-inline">Tourist Sites</span>
                </a>
            </li>
            <li class="nav-item mb-4">
                <a href="accounts.php?usertype=mngr" class="nav-link">
                    <i class="bi bi-people"></i>
                    <span class="d-none d-sm-inline">Accounts</span>
                </a>
            </li>
            <li class="nav-item mb-4">
                <a href="employeelogs.php" class="nav-link">
                    <i class="bi bi-person-vcard"></i>
                    <span class="d-none d-sm-inline">Employee Logs</span>
                </a>
            </li>
        </ul>
    </div>
        
    <ul class="nav nav-pills flex-column mb-4">
        <li class="nav-item mb-3">
            <a href="javascript:void(0);" class="nav-link admin-name active">
                <i class="bi bi-person-circle"></i>
                <span class="d-none d-sm-inline"><?= $adminName; ?></span>
            </a>
        </li>
        <li class="nav-item">
            <a href="javascript:void(0);" class="nav-link sign-out active" onclick="logoutConfirm()">
                <i class="bi bi-box-arrow-right"></i>
                <span class="d-none d-sm-inline">Sign Out</span>
            </a>
        </li>
    </ul>
</div>

<div class="main-content">
    <div class="content-container">
        <div class="header">
            <h2>Admin Dashboard</h2>
            <span class="date">
                <h2>
                    <?php 
                        date_default_timezone_set('Asia/Manila');
                        echo date('M d, Y | h:i A'); 
                    ?>
                </h2>
            </span>
        </div>

        <div class="row mt-3 d-flex justify-content-center">
            <div class="col-lg-4 col-md-6 col-12 mb-3">
                <div class="info-box">
                    <span>Tour Requests This Month</span>
                    <i class="bi bi-map-fill"></i>
                    <h1><?php echo $tourRequests; ?></h1>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-12 mb-3">
                <div class="info-box">
                    <span>Website Visits This Month</span>
                    <i class="bi bi-globe"></i>
                    <h1><?php echo $monthlyVisits; ?></h1>
                    <p>Total: <?php echo $totalVisits; ?></p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-12 mb-3">
                <div class="info-box">
                    <span>Number Of Active Employees</span>
                    <i class="bi bi-people-fill"></i>
                    <h1><?php echo $activeEmployees; ?></h1>
                </div>
            </div>
        </div>

        <div class="row mt-3 d-flex justify-content-center">
            <div class="col-lg-6 col-md-6 col-12">
                <div class="row">

                    <div class="col-lg-12 mb-3">
                        <div class="busy-days-box" onclick="window.location.href='statistics/busiestmonthstatistics.php';" style="cursor: pointer;">
                            <span class="section-title">Busiest Days This Month</span>
                            <div class="d-flex justify-content-around">
                                <?php foreach ($busiestDays as $day): ?>
                                    <div class="inner-box">
                                        <span class="month"><?php echo date('M', strtotime($day['tour_date'])); ?></span>
                                        <span class="day"><?php echo date('d', strtotime($day['tour_date'])); ?></span>
                                        <span class="tours"><?php echo $day['total_tours']; ?> Tour/s</span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-12 mb-3">
                        <div class="tour-sites-box" onclick="window.location.href='statistics/toptouristsites.php';" style="cursor: pointer;">
                            <span class="section-title">Top Tourist Sites This Month</span>
                            <div class="d-flex justify-content-around">
                                <?php foreach ($topSites as $site): ?>
                                    <div class="tour-card">
                                        <div class="inner-box">
                                            <img src="https://tourtaal.azurewebsites.net/public/uploads/<?php echo htmlspecialchars($site['siteimage']); ?>" alt="<?php echo htmlspecialchars($site['sitename']); ?>" style="width: 100%; height: 100%; object-fit: cover; border-radius: 25px;">
                                        </div>
                                        <span class="destination-name"><?php echo htmlspecialchars($site['sitename']); ?></span>
                                        <div class="rating">
                                            <span>Total Visitors: <?php echo $site['total_visitors']; ?></span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="col-lg-6 col-md-6 col-12">
                <div class="logs-box recent-logs-box">
                    <span>Recent Employee Logs</span>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Activity</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentLogs as $log): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($log['name']); ?></td>
                                        <td><?php echo htmlspecialchars($log['action']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <button class="btn-custom mt-3" onclick="window.location.href='employeelogs.php'">See All</button>
                </div>
            </div>

        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/js/all.min.js"></script>
<script src="../../../public/assets/scripts/main.js"></script>
</body>
</html>