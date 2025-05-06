<?php
include '../../../../includes/auth.php';
require_once '../../../config/dbconnect.php';

$database = new Database();
$conn = $database->getConnection();

$currentYear = date('Y');

$query = "SELECT MONTH(date) AS month, COUNT(DISTINCT tourid) AS total 
          FROM tour 
          WHERE status = 'accepted' AND YEAR(date) = :currentYear 
          GROUP BY MONTH(date)";
$stmt = $conn->prepare($query);
$stmt->bindParam(':currentYear', $currentYear, PDO::PARAM_INT);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

$monthlyCounts = array_fill(1, 12, 0);
foreach ($results as $row) {
    $monthlyCounts[$row['month']] = $row['total'];
}

usort($results, function ($a, $b) {
    return ($b['total'] === $a['total']) ? $a['month'] - $b['month'] : $b['total'] - $a['total'];
});

$busiestMonths = array_slice($results, 0, 3);

usort($results, function ($a, $b) {
    return ($a['total'] === $b['total']) ? $a['month'] - $b['month'] : $a['total'] - $b['total'];
});
$leastBusyMonths = array_slice($results, 0, 3);

$totalAcceptedToursQuery = "SELECT COUNT(DISTINCT tourid) AS total FROM tour WHERE status = 'accepted' AND YEAR(date) = :currentYear";
$totalStmt = $conn->prepare($totalAcceptedToursQuery);
$totalStmt->bindParam(':currentYear', $currentYear, PDO::PARAM_INT);
$totalStmt->execute();
$totalAcceptedTours = $totalStmt->fetch(PDO::FETCH_ASSOC)['total'];

function formatMonthYear($month, $year) {
    return str_pad($month, 2, '0', STR_PAD_LEFT) . '/' . substr($year, -2);
}

$userid = $_SESSION['userid'];
$query = "SELECT name FROM Users WHERE userid = :userid SELECT TOP 1";
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
    <title>Admin Dashboard - Busiest Month Statistics</title>
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
                <h2>Busiest Month Statistics</h2>
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
                    <canvas id="busiestMonthChart"></canvas>
                </div>
            </div>
        </div>

        <div class="stats-container">
            <div class="stats-box">
                <h4>Busiest Months</h4>
                <?php foreach ($busiestMonths as $month) : ?>
                    <p><?php echo formatMonthYear($month['month'], $currentYear); ?></p>
                <?php endforeach; ?>
            </div>
            <div class="stats-box">
                <h4>Least Busy Months</h4>
                <?php foreach ($leastBusyMonths as $month) : ?>
                    <p><?php echo formatMonthYear($month['month'], $currentYear); ?></p>
                <?php endforeach; ?>
            </div>
            <div class="stats-box">
                <h4>Total Accepted Tours This Year</h4>
                <p><?php echo $totalAcceptedTours; ?></p> 
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
    var ctx = document.getElementById('busiestMonthChart').getContext('2d');
    var busiestMonthChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Jan.', 'Feb.', 'Mar.', 'Apr.', 'May', 'Jun.', 'Jul.', 'Aug.', 'Sep.', 'Oct.', 'Nov.', 'Dec.'],
            datasets: [
                {
                    label: 'Accepted Tours',
                    data: <?php echo json_encode(array_values($monthlyCounts)); ?>,
                    backgroundColor: '#102E47',
                    borderColor: '#102E47',
                    borderWidth: 1
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
                        text: '<?php echo $currentYear; ?>',
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
                    beginAtZero: true,
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