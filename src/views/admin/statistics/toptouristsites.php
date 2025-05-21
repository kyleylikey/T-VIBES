<?php
include '../../../../includes/auth.php';
require_once '../../../config/dbconnect.php';

$database = new Database();
$conn = $database->getConnection();

$query = "SELECT s.sitename, SUM(t.companions) AS total_visitors 
        FROM [taaltourismdb].[tour] t
        JOIN [taaltourismdb].[sites] s ON t.siteid = s.siteid
        WHERE t.status = 'accepted'
        GROUP BY t.siteid, s.sitename";

$stmt = $conn->prepare($query);
$stmt->execute();

$sites = [];
$visitors = [];

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $sites[] = $row['sitename'];
    $visitors[] = (int)$row['total_visitors'];
}

$totalVisitors = 0;
$currentYear = date('Y');

try {
    $query = "SELECT SUM(companions) AS total_visitors 
        FROM (
            SELECT userid, tourid, MAX(companions) AS companions 
            FROM [taaltourismdb].[tour] 
            WHERE status = 'accepted' AND YEAR(date) = :currentYear
            GROUP BY tourid, userid
        ) AS distinct_tours;";

$stmt = $conn->prepare($query);
$stmt->bindParam(':currentYear', $currentYear, PDO::PARAM_INT);
$stmt->execute();

$result = $stmt->fetch(PDO::FETCH_ASSOC);
if ($result && $result['total_visitors'] !== null) {
    $totalVisitors = $result['total_visitors'];
}
} catch (PDOException $e) {
echo "Error: " . $e->getMessage();
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
    <title>Admin Dashboard - Top Tourist Sites</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;700&family=Raleway:wght@300;400;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="../../../../public/assets/styles/admin/statistics.css">
</head>
<body>
    
<div class="sidebar">
    <div class="pt-4 pb-1 px-2 text-center">
        <a href="#" class="text-decoration-none">
            <img src="../../../../public/assets/images/headerlogo.jpg" alt="Header Logo" class="img-fluid">
        </a>
    </div>

    <div class="menu-section">
        <ul class="nav nav-pills flex-column mb-4">
            <li class="nav-item mb-4">
                <a href="../home.php" class="nav-link">
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
                <a href="../tourhistory.php" class="nav-link">
                    <i class="bi bi-map"></i>
                    <span class="d-none d-sm-inline">Tour History</span>
                </a>
            </li>
            <li class="nav-item mb-4">
                <a href="../touristsites.php" class="nav-link">
                    <i class="bi bi-image"></i>
                    <span class="d-none d-sm-inline">Tourist Sites</span>
                </a>
            </li>
            <li class="nav-item mb-4">
                <a href="../accounts.php?usertype=mngr" class="nav-link">
                    <i class="bi bi-people"></i>
                    <span class="d-none d-sm-inline">Accounts</span>
                </a>
            </li>
            <li class="nav-item mb-4">
                <a href="../employeelogs.php" class="nav-link">
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
            <div class="title-container">
                <a href="../monthlyperformance.php" class="back-button">
                    <i class="bi bi-arrow-left-circle default-icon"></i>
                    <i class="bi bi-arrow-left-circle-fill hover-icon"></i>
                </a>
                <h2>Top Tourist Sites This Year</h2>
            </div>
            <span class="date">
                <h2>
                    <?php 
                        date_default_timezone_set('Asia/Manila');
                        echo date('M d, Y | h:i A'); 
                    ?>
                </h2>
            </span>
        </div>
        <div class="additional-container">
            <div class="inner-container">
                <div class="chart-container">
                    <canvas id="topTouristSitesChart"></canvas>
                </div>
            </div>
        </div>

        <div class="stats-container">
            <div class="stats-box">
                <h4>Total Visitors This Year</h4>
                <p><?php echo htmlspecialchars($totalVisitors); ?></p>
            </div>
        </div>

        <div class="button-container">
            <button class="btn-custom">
                <i class="bi bi-file-earmark-arrow-down-fill"></i> Download PDF Report
            </button>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/js/all.min.js"></script>
<script src="../../../../public/assets/scripts/main.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    var ctx = document.getElementById('topTouristSitesChart').getContext('2d');

    var sites = <?php echo json_encode($sites); ?>;
    var visitors = <?php echo json_encode($visitors); ?>;

    // Combine sites and visitors into an array of objects for sorting
    var siteData = sites.map((site, index) => ({ site, visitors: visitors[index] }));

    // Sort data from highest to lowest visitors
    siteData.sort((a, b) => b.visitors - a.visitors);

    // Extract sorted labels and data
    var sortedSites = siteData.map(item => item.site);
    var sortedVisitors = siteData.map(item => item.visitors);

    function generateColors(count) {
        var colors = [];
        for (var i = 0; i < count; i++) {
            var r = Math.floor(Math.random() * 255);
            var g = Math.floor(Math.random() * 255);
            var b = Math.floor(Math.random() * 255);
            colors.push(`rgba(${r}, ${g}, ${b}, 0.7)`);
        }
        return colors;
    }

    var backgroundColors = generateColors(sortedSites.length);

    var topTouristSitesChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: sortedSites,
            datasets: [
                {
                    label: 'Visitors',
                    data: sortedVisitors,
                    backgroundColor: backgroundColors,
                    borderColor: '#FFFFFF',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right', 
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