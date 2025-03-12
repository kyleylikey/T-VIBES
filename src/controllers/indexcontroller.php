<?php
require_once __DIR__ . '/../config/dbconnect.php';
require_once __DIR__ . '/../models/Site.php';
require_once __DIR__ . '/../models/Review.php';

$database = new Database();
$db = $database->getConnection();

// Create a Site model instance
$siteModel = new Site($db);

// Get the current year
$currentYear = date('Y');

// Fetch the top sites
$topSitesStmt = $siteModel->getTopSites($currentYear);
$topSites = $topSitesStmt->fetchAll(PDO::FETCH_ASSOC);

// Create a Review model instance
$reviewModel = new Review($db);

// Fetch recent reviews
$recentReviews = $reviewModel->getRecentReviews();

foreach ($recentReviews as &$review) {
    $review['date'] = date('F j, Y', strtotime($review['date']));
}
?>