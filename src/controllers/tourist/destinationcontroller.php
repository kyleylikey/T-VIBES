<?php
require_once __DIR__ . '/../../config/dbconnect.php';
require_once __DIR__ . '/../../models/Site.php';
require_once __DIR__ . '/../../models/Review.php';


$database = new Database();
$db = $database->getConnection();

$siteModel = new Site($db);
$siteid = $_GET['siteid'];

$siteDetails = $siteModel->getSiteDetails($siteid);

$reviewModel = new Review($db);
$siteReviews = $reviewModel->getSiteReviews($siteid);

$reviewCount = $reviewModel->countSiteReviews($siteid);

$ratingDistribution = $reviewModel->getRatingDistribution($siteid);
?>