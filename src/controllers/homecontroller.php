<?php
require_once  __DIR__ .'/../config/dbconnect.php';
require_once  __DIR__ .'/../models/Tour.php';
require_once  __DIR__ .'/../models/Review.php';

$database = new Database();
$conn = $database->getConnection();

$tourModel = new Tour($conn);
$reviewModel = new Review($conn);

$pendingTours = $tourModel->getPendingToursCount();
$upcomingTours = $tourModel->getUpcomingToursCount();
$latestRequests = $tourModel->getLatestTourRequests();

$pendingReviews = $reviewModel->getPendingReviewsCount();
$recentReviews = $reviewModel->getRecentReviews();
?>