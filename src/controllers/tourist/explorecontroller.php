<?php
require_once __DIR__ . '/../../config/dbconnect.php';
require_once __DIR__ . '/../../models/Site.php';

$database = new Database();
$db = $database->getConnection();

// Create a Site model instance
$siteModel = new Site($db);

// Get the current year
$currentYear = date('Y');

// Fetch the top sites
$topSitesStmt = $siteModel->getTopSites($currentYear);
$topSites = $topSitesStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch the top sites
$displaySites= $siteModel->getSites();
?>