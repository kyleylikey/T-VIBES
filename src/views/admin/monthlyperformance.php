<?php
include '../../../includes/auth.php';
require_once '../../config/dbconnect.php';

$database = new Database();
$conn = $database->getConnection();

$toursThisMonthQuery = "
    SELECT COUNT(DISTINCT tourid) AS total_tours 
    FROM [taaltourismdb].[tour] 
    WHERE status = 'accepted' 
    AND MONTH(date) = MONTH(GETDATE()) 
    AND YEAR(date) = YEAR(GETDATE())";

$toursThisMonthStmt = $conn->prepare($toursThisMonthQuery);
$toursThisMonthStmt->execute();
$toursThisMonth = $toursThisMonthStmt->fetch(PDO::FETCH_ASSOC);
$totalTours = $toursThisMonth['total_tours'];

function getTourCount($conn, $status, $dateCondition) {
    $query = "
        SELECT COUNT(DISTINCT tourid) AS total_tours 
        FROM [taaltourismdb].[tour] 
        WHERE status = :status AND $dateCondition";
        
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':status', $status);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['total_tours'] ?? 0;
}

$thisMonthCondition = "MONTH(date) = MONTH(GETDATE()) AND YEAR(date) = YEAR(GETDATE())";
$lastMonthCondition = "MONTH(date) = MONTH(DATEADD(MONTH, -1, GETDATE())) AND YEAR(date) = YEAR(DATEADD(MONTH, -1, GETDATE()))";

$acceptedThisMonth = getTourCount($conn, 'accepted', $thisMonthCondition);
$acceptedLastMonth = getTourCount($conn, 'accepted', $lastMonthCondition);

$cancelledThisMonth = getTourCount($conn, 'cancelled', $thisMonthCondition);
$cancelledLastMonth = getTourCount($conn, 'cancelled', $lastMonthCondition);

$completedThisMonth = getTourCount($conn, 'accepted', "$thisMonthCondition AND date < CAST(GETDATE() AS DATE)");
$completedLastMonth = getTourCount($conn, 'accepted', "$lastMonthCondition AND date < CAST(GETDATE() AS DATE)");

function calculatePercentageChange($current, $previous) {
    if ($previous == 0) {
        return $current > 0 ? 100 : 0;
    }
    return round((($current - $previous) / $previous) * 100, 2);
}

$acceptedChange = calculatePercentageChange($acceptedThisMonth, $acceptedLastMonth);
$cancelledChange = calculatePercentageChange($cancelledThisMonth, $cancelledLastMonth);
$completedChange = calculatePercentageChange($completedThisMonth, $completedLastMonth);

$busiestDaysQuery = "
    SELECT TOP 3 CAST(date AS DATE) AS tour_date, COUNT(DISTINCT tourid) AS total_tours 
    FROM [taaltourismdb].[tour] 
    WHERE status = 'accepted' 
    AND MONTH(date) = MONTH(GETDATE()) 
    AND YEAR(date) = YEAR(GETDATE()) 
    GROUP BY CAST(date AS DATE) 
    ORDER BY total_tours DESC
    ";

$busiestDaysStmt = $conn->prepare($busiestDaysQuery);
$busiestDaysStmt->execute();
$busiestDays = $busiestDaysStmt->fetchAll(PDO::FETCH_ASSOC);

$topSitesQuery = "
    SELECT TOP 3 s.siteid, s.sitename, s.siteimage, SUM(t.companions) AS total_visitors
    FROM [taaltourismdb].[tour] t
    JOIN [taaltourismdb].[sites] s ON t.siteid = s.siteid
    WHERE t.status = 'accepted'
    AND MONTH(t.date) = MONTH(GETDATE()) 
    AND YEAR(t.date) = YEAR(GETDATE())
    GROUP BY s.siteid, s.sitename, s.siteimage
    ORDER BY total_visitors DESC
    ";

$topSitesStmt = $conn->prepare($topSitesQuery);
$topSitesStmt->execute();
$topSites = $topSitesStmt->fetchAll(PDO::FETCH_ASSOC);

$today = new DateTime();
$currentYear = $today->format('Y');
$currentMonth = $today->format('m');
$daysInMonth = cal_days_in_month(CAL_GREGORIAN, $currentMonth, $currentYear);

$visitorData = array_fill(1, $daysInMonth, 0);

$query = "SELECT DAY(date) AS day, SUM(companions) AS total_visitors 
        FROM (SELECT userid, date, companions FROM [taaltourismdb].[tour] 
            WHERE YEAR(date) = :year AND MONTH(date) = :month AND status = 'accepted' GROUP BY tourid, userid, date, companions
            ) AS distinct_tours
        GROUP BY DAY(date)";

$stmt = $conn->prepare($query);
$stmt->bindParam(':year', $currentYear, PDO::PARAM_INT);
$stmt->bindParam(':month', $currentMonth, PDO::PARAM_INT);
$stmt->execute();

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $visitorData[$row['day']] = $row['total_visitors'];
}

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
    <title>Admin Dashboard - Monthly Performance</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;700&family=Raleway:wght@300;400;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="../../../public/assets/styles/admin/monthlyperformance.css">
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
                <a href="home.php" class="nav-link">
                    <i class="bi bi-grid"></i>
                    <span class="d-none d-sm-inline">Dashboard</span>
                </a>
            </li>
            <li class="nav-item mb-4">
                <a href="" class="nav-link active">
                    <i class="bi bi-bar-chart-line-fill"></i>
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
            <h2>Monthly Performance</h2>
            <span class="date">
                <h2>
                    <?php 
                        date_default_timezone_set('Asia/Manila');
                        echo date('M d, Y | h:i A'); 
                    ?>
                </h2>
            </span>
        </div>

        <div class="row mt-3">
            <div class="col-lg-4 col-md-6 col-12 mb-3">
                <div class="info-box" onclick="window.location.href='statistics/tourstatistics.php';" style="cursor: pointer;">
                    <span>Tours This Month</span>
                    <i class="bi bi-map-fill"></i>
                    <h1><?php echo $totalTours; ?></h1>
                </div>
            </div>
            <div class="col-lg-8 col-md-6 col-12 mb-3">
                <div class="stats-container">
                    <!-- Accepted Tours -->
                    <div class="stat-box">
                        <div class="stat-header">
                            <span class="stat-title">Accepted</span>
                            <span class="stat-percentage <?= $acceptedChange >= 0 ? 'text-success' : 'text-danger' ?>">
                                <?= abs($acceptedChange) ?>% <?= $acceptedChange >= 0 ? '▲' : '▼' ?>
                            </span>
                        </div>
                        <h1 class="stat-value"><?= $acceptedThisMonth ?></h1>
                        <div class="stat-footer">
                            <span class="stat-compare"><b>vs. last month</b></span>
                            <span class="stat-last-value"><?= $acceptedLastMonth ?></span>
                        </div>
                    </div>

                    <!-- Cancelled Tours -->
                    <div class="stat-box">
                        <div class="stat-header">
                            <span class="stat-title">Cancelled</span>
                            <span class="stat-percentage <?= $cancelledChange >= 0 ? 'text-danger' : 'text-success' ?>">
                                <?= abs($cancelledChange) ?>% <?= $cancelledChange >= 0 ? '▲' : '▼' ?>
                            </span>
                        </div>
                        <h1 class="stat-value"><?= $cancelledThisMonth ?></h1>
                        <div class="stat-footer">
                            <span class="stat-compare"><b>vs. last month</b></span>
                            <span class="stat-last-value"><?= $cancelledLastMonth ?></span>
                        </div>
                    </div>

                    <!-- Completed Tours -->
                    <div class="stat-box">
                        <div class="stat-header">
                            <span class="stat-title">Completed</span>
                            <span class="stat-percentage <?= $completedChange >= 0 ? 'text-warning' : 'text-danger' ?>">
                                <?= abs($completedChange) ?>% <?= $completedChange >= 0 ? '▲' : '▼' ?>
                            </span>
                        </div>
                        <h1 class="stat-value"><?= $completedThisMonth ?></h1>
                        <div class="stat-footer">
                            <span class="stat-compare"><b>vs. last month</b></span>
                            <span class="stat-last-value"><?= $completedLastMonth ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div> 
        <div class="row mt-3 d-flex justify-content-start">
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

            <div class="col-lg-6 col-md-6 col-12 d-flex flex-column">
                <div class="visitors-box" onclick="window.location.href='statistics/visitorstatistics.php';" style="cursor: pointer;">
                    <span class="section-title">Visitors This Month</span>
                    <div class="inner-container">
                        <div class="chart-container">
                            <canvas id="visitorStatisticsChart"></canvas>
                        </div>
                    </div>
                </div>
                <button class="btn-custom"><i class="bi bi-file-earmark-arrow-down-fill"></i> Download PDF Report</button>
            </div>

        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/js/all.min.js"></script>
<script src="../../../public/assets/scripts/main.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    var daysArray = <?php echo json_encode(range(1, $daysInMonth)); ?>;
    var visitorData = <?php echo json_encode(array_values($visitorData)); ?>;

    var ctx = document.getElementById('visitorStatisticsChart').getContext('2d');
    var visitorStatisticsChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: daysArray.map(day => day.toString()),
            datasets: [
                {
                    label: 'Total Visitors',
                    data: visitorData,
                    borderColor: '#102E47',
                    borderWidth: 2,
                    fill: false
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: {
                    title: {
                        display: true,
                        text: '<?php echo date("F Y"); ?>',
                        font: {
                            family: 'Nunito'
                        }
                    },
                    ticks: {
                        font: {
                            family: 'Nunito'
                        }
                    }
                },
                y: {
                    ticks: {
                        font: {
                            family: 'Nunito'
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    labels: {
                        font: {
                            family: 'Nunito'
                        }
                    }
                }
            }
        }
    });
</script>
</body>
</html>