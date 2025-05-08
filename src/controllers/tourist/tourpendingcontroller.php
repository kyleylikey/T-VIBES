<?php
echo "Checkpoint 1: Controller loaded.<br>";

require_once __DIR__ . '/../../config/dbconnect.php';
echo "Checkpoint 2: DB Connect included.<br>";

require_once __DIR__ . '/../../models/Tour.php';
echo "Checkpoint 3: Tour model included.<br>";

$database = new Database();
$db = $database->getConnection();
echo "Checkpoint 4: DB connection established.<br>";

$tourModel = new Tour($db);
echo "Checkpoint 5: Tour model instantiated.<br>";

if (isset($_SESSION['userid'])) {
    echo "Checkpoint 6: User ID is " . $_SESSION['userid'] . "<br>";
    $userPendingTour = $tourModel->getPendingTourByUser($_SESSION['userid']);
    echo "Checkpoint 7: Pending tour fetched.<br>";
} else {
    echo "Checkpoint 6: User ID not set in session.<br>";
}

?>