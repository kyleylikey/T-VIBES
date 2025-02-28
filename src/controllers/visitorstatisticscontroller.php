<?php

if (!isset($_SESSION['userid']) || $_SESSION['usertype'] !== 'mngr') {
    header('Location: ../frontend/login.php');
    exit();
}

require_once __DIR__ . '/../config/dbconnect.php';

//database connection instance using PDO
$db   = new Database();
$conn = $db->getConnection(); 

// Set current year
$currentYear = date('Y');

//array for companions per month (months 1 to 12)
$companionsPerMonth = array_fill(1, 12, 0);

// Query to sum the total number of companions for each month in the current year
$query = "SELECT MONTH(date) AS month, SUM(companions) AS total 
          FROM tour 
          WHERE YEAR(date) = :currentYear
          GROUP BY MONTH(date)
          ORDER BY month ASC";
$stmt = $conn->prepare($query);
$stmt->bindParam(':currentYear', $currentYear, PDO::PARAM_INT);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
$stmt->closeCursor();

// Populate the companions array with the query results
foreach ($results as $row) {
    $month = (int)$row['month'];
    $companionsPerMonth[$month] = (int)$row['total'];
}

// Build the labels for each month in "MM/YY" format
$labels = [];
for ($m = 1; $m <= 12; $m++) {
    $labels[] = sprintf("%02d/%s", $m, substr($currentYear, -2));
}

// Prepare the data for Chart.js by re-indexing the companions array
$data = array_values($companionsPerMonth);

// Optionally, calculate total visitors for the year
$totalVisitorsThisYear = array_sum($data);

// Now include the view file that will render the chart
include_once __DIR__ . '/../views/admin/statistics/visitor.php';
?>
