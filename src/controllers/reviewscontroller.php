<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once  __DIR__ .'/../config/dbconnect.php';
require_once __DIR__.'/../models/Logs.php';

$conn = (new Database())->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['review_id'], $_POST['status'])) {
    $review_id = $_POST['review_id'];
    $status = $_POST['status'];
    $stmt = $conn->prepare("UPDATE [taaltourismdb].[rev] SET status = ? WHERE revid = ?");
    if ($status === 'archived') {
        $logs = new Logs();
        $logs->logArchiveReview($_SESSION['userid'], $review_id);
    }
    else {
        $logs = new Logs();
        $logs->logDisplayReview($_SESSION['userid'], $review_id);
    }
    $stmt->execute([$status, $review_id]);
    exit;
}

$statusFilter = $_GET['status'] ?? 'submitted';
$stmt = $conn->prepare("SELECT rev.revid, rev.review, rev.date, rev.status, users.name, sites.sitename FROM [taaltourismdb].[rev] JOIN [taaltourismdb].[users] ON rev.userid = users.userid JOIN [taaltourismdb].[sites] ON rev.siteid = sites.siteid WHERE rev.status = ?");
$stmt->execute([$statusFilter]);
$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>