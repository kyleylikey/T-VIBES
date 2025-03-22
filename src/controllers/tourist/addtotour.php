<?php
header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/dbconnect.php';
require_once __DIR__ . '/../../models/Tour.php';

// Check if user is logged in
if (!isset($_SESSION['userid']) || $_SESSION['usertype'] !== 'trst') {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Get siteid from POST data
$siteid = isset($_POST['siteid']) ? (int)$_POST['siteid'] : 0;

if ($siteid <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid site ID']);
    exit;
}

$database = new Database();
$db = $database->getConnection();
$tourModel = new Tour($db);
$userid = $_SESSION['userid'];

// Check if user already has a tour request
$checkTourRequest = $tourModel->doesTourRequestExist($userid);
$tourid = null;

if ($checkTourRequest) {
    // Add to existing tour
    $tourid = $tourModel->getExistingTourRequestId($userid);
    $result = $tourModel->addToExistingTour($tourid, $siteid, $userid);
} else {
    // Create new tour
    $result = $tourModel->addToNewTour($userid, $siteid);
    if ($result) {
        $tourid = $tourModel->getExistingTourRequestId($userid);
    }
}

if ($result) {
    echo json_encode(['success' => true, 'message' => 'Site added to your tour successfully', 'tourid' => $tourid]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to add site to tour']);
}
?>