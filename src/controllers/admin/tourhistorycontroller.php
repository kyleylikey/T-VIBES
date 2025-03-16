<?php
require_once __DIR__ . '/../../config/dbconnect.php';
require_once  __DIR__ .'/../../models/Tour.php';

$database = new Database();
$conn = $database->getConnection();

$tourModel = new Tour($conn); 
$tours = $tourModel->getTourHistory();

$completed_tours = [];
$cancelled_tours = [];

$current_date = date('Y-m-d');

foreach ($tours as $tour) {
    if ($tour['status'] == 'cancelled') {
        $cancelled_tours[$tour['tourid']][] = $tour;
    } elseif ($tour['status'] == 'accepted' && $tour['travel_date'] < $current_date) {
        $completed_tours[$tour['tourid']][] = $tour;
    }
}
?>