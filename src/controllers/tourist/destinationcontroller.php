<?php
require_once __DIR__ . '/../../config/dbconnect.php';
require_once __DIR__ . '/../../models/Site.php';
require_once __DIR__ . '/../../models/Review.php';


$database = new Database();
$db = $database->getConnection();

// Create a Site model instance
$siteModel = new Site($db);
$siteid = $_GET['siteid'];

// Fetch the top sites
$siteDetails= $siteModel->getSiteDetails($siteid);

$reviewModel = new Review($db);
$siteReviews = $reviewModel->getSiteReviews($siteid);
?>