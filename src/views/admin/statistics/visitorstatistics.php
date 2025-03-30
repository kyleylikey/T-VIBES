<?php
include '../../../../includes/auth.php';
require_once '../../../config/dbconnect.php';

$database = new Database();
$conn = $database->getConnection();

$totalVisitors = 0;
$currentYear = date('Y');

try {
        $query = "SELECT SUM(companions) AS total_visitors 
            FROM (
                SELECT userid, tourid, MAX(companions) AS companions 
                FROM tour 
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

$monthlyVisitors = array_fill(0, 12, 0);

try {
    $query = "SELECT MONTH(date) AS month, SUM(companions) AS monthly_visitors FROM (
                SELECT userid, MONTH(date) AS month, companions, date
                FROM tour
                WHERE status = 'accepted' AND YEAR(date) = :currentYear
                GROUP BY tourid, userid
            ) AS distinct_tours
            GROUP BY MONTH(date)";
    
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':currentYear', $currentYear, PDO::PARAM_INT);
    $stmt->execute();
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $monthlyVisitors[$row['month'] - 1] = $row['monthly_visitors'];
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

$userid = $_SESSION['userid'];
$query = "SELECT name FROM Users WHERE userid = :userid LIMIT 1";
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
    <title>Admin Dashboard - Visitor Statistics</title>
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
                <h2>Visitor Statistics</h2>
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
                    <canvas id="visitorStatisticsChart"></canvas>
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
    var ctx = document.getElementById('visitorStatisticsChart').getContext('2d');
    var tourStatisticsChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Jan.', 'Feb.', 'Mar.', 'Apr.', 'May', 'Jun.', 'Jul.', 'Aug.', 'Sep.', 'Oct.', 'Nov.', 'Dec.'],
            datasets: [
                {
                    label: 'Total Visitors',
                    data: <?php echo json_encode($monthlyVisitors); ?>,
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
                        text: '2025',
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