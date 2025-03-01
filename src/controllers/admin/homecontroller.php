<?php

require_once __DIR__ . '/../../config/dbconnect.php';
require_once  __DIR__ .'/../../models/Tour.php';
require_once  __DIR__ .'/../../models/User.php';
require __DIR__ . '/../../../vendor/autoload.php';


$database = new Database();
$db = $database->getConnection();
$tourModel = new Tour($db);
$requests = $tourModel->getTourRequestList();

// Get current month and year
$currentMonth = date('n'); // Numeric representation without leading zeros
$currentYear  = date('Y');

// Determine last month (account for January)
if ($currentMonth == 1) {
    $lastMonth = 12;
    $lastYear  = $currentYear - 1;
} else {
    $lastMonth = $currentMonth - 1;
    $lastYear  = $currentYear;
}


$requestcount = $requests->rowCount();

$monthlyVisits = getMonthlyVisitCount($currentYear, $currentMonth);
$totalVisits = getTotalVisitCount();

$userModel = new User($db);
$employees = $userModel->getActiveEmpList();
$activeempcount = $employees->rowCount();

// Busiest Days: Get top 3 days in current month with most accepted tours
$query = "SELECT DAY(date) as day, COUNT(*) as count FROM tour 
          WHERE status = 'accepted' 
          AND MONTH(date) = :currentMonth AND YEAR(date) = :currentYear 
          GROUP BY DAY(date) ORDER BY count DESC LIMIT 3";
$stmt = $db->prepare($query);
$stmt->bindParam(':currentMonth', $currentMonth, PDO::PARAM_INT);
$stmt->bindParam(':currentYear', $currentYear, PDO::PARAM_INT);
$stmt->execute();
$busiestDays = $stmt->fetchAll(PDO::FETCH_ASSOC);
$stmt->closeCursor();

// Top Tourist Sites: Get details and count accepted tours per site
$query = "SELECT s.siteid, s.sitename, s.siteimage, s.description, s.opdays, s.rating as ratings, s.price, s.status, COUNT(t.tourid) as tour_count 
          FROM sites s 
          LEFT JOIN tour t ON s.siteid = t.siteid AND t.status = 'accepted'
          GROUP BY s.siteid
          ORDER BY tour_count DESC
          LIMIT 3";
$stmt = $db->prepare($query);
$stmt->execute();
$topSites = $stmt->fetchAll(PDO::FETCH_ASSOC);
$stmt->closeCursor();


?>