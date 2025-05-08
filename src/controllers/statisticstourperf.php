<?php

if (!isset($_SESSION['userid']) || $_SESSION['usertype'] !== 'mngr') {
    header("Location: ../../frontend/login.php");
    exit();
}

require_once __DIR__ . '/../config/dbconnect.php';

// Create a PDO connection
$db = new Database();
$conn = $db->getConnection();

$currentYear = date('Y');

// ----- Additional Queries for Tour Performance (Yearly) -----

// Get current year
$currentYear = date('Y');

// Total Approved Tours This Year: using 'submitted' status
$query = "SELECT COUNT(*) as total FROM [taaltourismdb].[tour] 
          WHERE status = 'accepted' 
          AND YEAR(date) = :currentYear";
$stmt = $conn->prepare($query);
$stmt->bindParam(':currentYear', $currentYear, PDO::PARAM_INT);
$stmt->execute();
$totalApprovedYear = $stmt->fetchColumn();
$stmt->closeCursor();

// Total Completed Tours This Year: using 'accepted' and tour date in the past
$query = "SELECT COUNT(*) as total FROM [taaltourismdb].[tour] 
          WHERE status = 'accepted' AND date < CONVERT(DATE, GETDATE())
          AND YEAR(date) = :currentYear";
$stmt = $conn->prepare($query);
$stmt->bindParam(':currentYear', $currentYear, PDO::PARAM_INT);
$stmt->execute();
$totalCompletedYear = $stmt->fetchColumn();
$stmt->closeCursor();

// Total Cancelled Tours This Year
$query = "SELECT COUNT(*) as total FROM [taaltourismdb].[tour] 
          WHERE status = 'cancelled'
          AND YEAR(date) = :currentYear";
$stmt = $conn->prepare($query);
$stmt->bindParam(':currentYear', $currentYear, PDO::PARAM_INT);
$stmt->execute();
$totalCancelledYear = $stmt->fetchColumn();
$stmt->closeCursor();

// ----- Chart Data: Monthly Breakdown for the Current Year -----
// Initialize arrays for each month (months 1 to 12) with zero values
$approvedChartData  = array_fill(1, 12, 0);
$completedChartData = array_fill(1, 12, 0);
$cancelledChartData = array_fill(1, 12, 0);

// Approved Tours per month
$query = "SELECT MONTH(t.date) AS month, COUNT(DISTINCT t.tourid) AS total 
          FROM [taaltourismdb].[tour] t
          JOIN [taaltourismdb].[users] u ON t.userid = u.userid
          WHERE t.status = 'accepted' 
          AND YEAR(t.date) = :currentYear 
          GROUP BY MONTH(t.date)
          ORDER BY MONTH(t.date)";

$stmt = $conn->prepare($query);
$stmt->bindParam(':currentYear', $currentYear, PDO::PARAM_INT);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
$stmt->closeCursor();


foreach ($results as $row) {
    $approvedChartData[(int)$row['month']] = (int)$row['total'];
}

// Completed Tours per month
$query = "SELECT MONTH(t.date) AS month, COUNT(DISTINCT t.tourid) AS total 
          FROM [taaltourismdb].[tour] t
          JOIN [taaltourismdb].[users] u ON t.userid = u.userid
          WHERE t.status = 'accepted' 
          AND t.date < CONVERT(DATE, GETDATE()) 
          AND YEAR(t.date) = :currentYear 
          GROUP BY MONTH(t.date)
          ORDER BY MONTH(t.date)";

$stmt = $conn->prepare($query);
$stmt->bindParam(':currentYear', $currentYear, PDO::PARAM_INT);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
$stmt->closeCursor();

foreach ($results as $row) {
    $completedChartData[(int)$row['month']] = (int)$row['total'];
}

// Cancelled Tours per month
$query = "SELECT MONTH(date) as month, COUNT(*) as total 
          FROM [taaltourismdb].[tour] 
          WHERE status = 'cancelled' AND YEAR(date) = :currentYear 
          GROUP BY MONTH(date)";
$stmt = $conn->prepare($query);
$stmt->bindParam(':currentYear', $currentYear, PDO::PARAM_INT);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
$stmt->closeCursor();
foreach($results as $row){
    $cancelledChartData[(int)$row['month']] = (int)$row['total'];
}

// Optionally, prepare labels for the 12 months (or simply use abbreviated month names)
$monthLabels = ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];

// Now, all these new variables are available for your view:
//   $totalApprovedYear, $totalCompletedYear, $totalCancelledYear
//   $approvedChartData, $completedChartData, $cancelledChartData, $monthLabels

// Then, include your view file (using an absolute path based on __DIR__ if needed)
include_once __DIR__ . '/../views/admin/statistics/tourperformance.php';
