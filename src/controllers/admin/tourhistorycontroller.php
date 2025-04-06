<?php
include '../../../../includes/auth.php';
require_once '../../../config/dbconnect.php';

// Check if user is logged in
if(!isset($_SESSION['userid'])) {
    header("Location: ../../../auth/login.php");
    exit;
}

$userid = $_SESSION['userid']; 

// Handle POST request for ratings separately
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the site ID and rating from the form data
    $siteId = $_POST['siteId'] ?? null;
    $rating = $_POST['rating'] ?? null;
    
    // Validate inputs
    if(!$siteId || !$rating || !is_numeric($siteId) || !is_numeric($rating) || $rating < 1 || $rating > 5) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
        exit;
    }
    
    try {
        // Initialize database connection
        $database = new Database();
        $conn = $database->getConnection();
        
        // Check if user has already rated this site
        $checkQuery = "SELECT id FROM user_ratings WHERE user_id = :user_id AND site_id = :site_id";
        $checkStmt = $conn->prepare($checkQuery);
        $checkStmt->bindParam(':user_id', $userid, PDO::PARAM_INT);
        $checkStmt->bindParam(':site_id', $siteId, PDO::PARAM_INT);
        $checkStmt->execute();
        
        if($checkStmt->rowCount() > 0) {
            // User has already rated this site
            echo json_encode(['status' => 'error', 'message' => 'You have already rated this site', 'alreadyRated' => true]);
            exit;
        }
        
        // Begin transaction
        $conn->beginTransaction();
        
        // Insert new rating into user_ratings table
        $insertQuery = "INSERT INTO user_ratings (user_id, site_id, rating) VALUES (:user_id, :site_id, :rating)";
        $insertStmt = $conn->prepare($insertQuery);
        $insertStmt->bindParam(':user_id', $userid, PDO::PARAM_INT);
        $insertStmt->bindParam(':site_id', $siteId, PDO::PARAM_INT);
        $insertStmt->bindParam(':rating', $rating, PDO::PARAM_INT);
        $insertResult = $insertStmt->execute();
        
        // Update site rating
        $updateQuery = "UPDATE sites SET rating = rating + :rating, rating_cnt = rating_cnt + 1 WHERE siteid = :siteid";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bindParam(':rating', $rating, PDO::PARAM_INT);
        $updateStmt->bindParam(':siteid', $siteId, PDO::PARAM_INT);
        $updateResult = $updateStmt->execute();
        
        if($insertResult && $updateResult) {
            $conn->commit();
            echo json_encode(['status' => 'success', 'message' => 'Rating submitted successfully']);
        } else {
            $conn->rollBack();
            echo json_encode(['status' => 'error', 'message' => 'Failed to submit rating']);
        }
    } catch(PDOException $e) {
        if(isset($conn) && $conn->inTransaction()) {
            $conn->rollBack();
        }
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    }
    exit; // Important: stop execution after responding to POST
}


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