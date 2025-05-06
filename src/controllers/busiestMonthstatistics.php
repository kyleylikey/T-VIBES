<?php


// Include your Database configuration
require_once __DIR__ . '/../config/dbconnect.php';

// Create a database connection instance using PDO
$db   = new Database();
$conn = $db->getConnection();  // This should return a PDO connection

// Set the current year (you can change this to a range if needed)
$currentYear = date('Y');

// Prepare arrays for 12 months (January=1 to December=12)
$busiestMonthsData  = array_fill(1, 12, 0);
$busiestMonthsLabels = [];

// Query the number of accepted tours per month for the current year
$query = "SELECT MONTH(date) as month, COUNT(*) as total 
          FROM [taaltourismdb].[tour]  
          WHERE YEAR(date) = :currentYear AND status = 'accepted'
          GROUP BY MONTH(date)
          ORDER BY month ASC";
$stmt = $conn->prepare($query);
$stmt->bindParam(':currentYear', $currentYear, PDO::PARAM_INT);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
$stmt->closeCursor();

// Populate the data array with query results. If a month has no data, it remains 0.
foreach ($results as $row) {
    $month = (int)$row['month'];
    $busiestMonthsData[$month] = (int)$row['total'];
}

for ($m = 1; $m <= 12; $m++) {
    $busiestMonthsLabels[] = sprintf("%02d/%s", $m, substr($currentYear, -2));
}

$busiestMonthsData  = array_values($busiestMonthsData);

// Now include the view file that will render the chart. 
// In your view, you can use json_encode() to pass the PHP arrays to JavaScript.
include_once __DIR__ . '/../views/admin/statistics/busiestmonths.php';
?>
