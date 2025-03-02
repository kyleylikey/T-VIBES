<?php
require_once  __DIR__ .'/../config/dbconnect.php';
require_once  __DIR__ .'/../models/Tour.php';

date_default_timezone_set('Asia/Manila');

$database = new Database();
$db = $database->getConnection();
$tourModel = new Tour($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = ["status" => "error", "message" => "Invalid request"];

    if (isset($_POST['editTour']) && !empty($_POST['tourId']) && !empty($_POST['tourDate']) && isset($_POST['tourPax'])) {
        $tourId = (int) $_POST['tourId'];
        $date = $_POST['tourDate'];
        $companions = (int) $_POST['tourPax'];

        if ($tourModel->updateTour($tourId, $date, $companions)) {
            $response = ["status" => "success", "message" => "Tour updated successfully."];
        } else {
            $response = ["status" => "error", "message" => "Failed to update tour."];
        }
    }

    if (isset($_POST['cancelTour']) && !empty($_POST['tourId'])) {
        $tourId = (int) $_POST['tourId'];
        
        if ($tourModel->cancelTour($tourId)) {
            $response = ["status" => "success", "message" => "Tour successfully cancelled."];
        } else {
            $response = ["status" => "error", "message" => "Failed to cancel tour."];
        }
    }

    echo json_encode($response);
    exit;
}

$toursToday = $tourModel->getToursForToday();
$allTours = $tourModel->getAllUpcomingTours();
?>
