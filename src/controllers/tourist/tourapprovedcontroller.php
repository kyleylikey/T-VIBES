<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/dbconnect.php';
require_once __DIR__ . '/../../models/Tour.php';

$database = new Database();
$db = $database->getConnection();

$tourModel = new Tour($db);
$userApprovedTour = $tourModel->getApprovedTourByUser($_SESSION['userid']);

?>