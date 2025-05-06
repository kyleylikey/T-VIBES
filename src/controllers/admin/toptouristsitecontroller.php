<?php


require_once __DIR__ . '/../../config/dbconnect.php';


$db   = new Database();
$conn = $db->getConnection(); 

// Set the current year
$currentYear = date('Y');

// Query to get top tourist sites
$query = "SELECT s.siteid, s.sitename, s.status, SUM(t.companions) as visitor_count 
          FROM [taaltourismdb].[sites] s 
          LEFT JOIN [taaltourismdb].[tour] t ON s.siteid = t.siteid AND t.status = 'accepted'
          WHERE YEAR(t.date) = :currentYear 
          GROUP BY s.siteid
          ORDER BY visitor_count DESC";
$stmt = $conn->prepare($query);
$stmt->bindParam(':currentYear', $currentYear, PDO::PARAM_INT);
$stmt->execute();
$topSites = $stmt->fetchAll(PDO::FETCH_ASSOC);
$stmt->closeCursor();

// Query to calculate total visitors for the year
$totalVisitorsQuery = "SELECT SUM(companions) as total_visitors 
                       FROM [taaltourismdb].[tour] 
                       WHERE YEAR(date) = :currentYear AND status = 'accepted'";
$totalVisitorsStmt = $conn->prepare($totalVisitorsQuery);
$totalVisitorsStmt->bindParam(':currentYear', $currentYear, PDO::PARAM_INT);
$totalVisitorsStmt->execute();
$totalVisitorsResult = $totalVisitorsStmt->fetch(PDO::FETCH_ASSOC);
$totalVisitors = $totalVisitorsResult['total_visitors'] ?: 0; // Use 0 if null
$totalVisitorsStmt->closeCursor();

// Prepare data for Chart.js
$siteNames = array_column($topSites, 'sitename');
$visitorCounts = array_column($topSites, 'visitor_count');

// Convert to JSON
$siteNamesJSON = json_encode($siteNames);
$visitorCountsJSON = json_encode($visitorCounts);

// Now include the view file that will render the chart. 
include_once __DIR__ . '/../../views/admin/statistics/toptouristsite.php';
?>