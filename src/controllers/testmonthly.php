<?php
session_start();

// Only allow managers to access this page
if (!isset($_SESSION['userid']) || $_SESSION['usertype'] !== 'mngr') {
    header('Location: ../views/admin/login.php');
    exit();
}

// Include your Database configuration file
require_once  __DIR__ .'/../config/dbconnect.php';

// Create a PDO database connection instance
$db = new Database();
$conn = $db->getConnection(); // This should return a PDO connection

// Get current month and year
$currentMonth = date('n');  // Numeric month (1-12)
$currentYear  = date('Y');   // Full year (e.g., 2025)

// Query to count tours with status 'submitted' or 'accepted' in the current month
$query = "SELECT COUNT(*) as total FROM tour 
          WHERE (status = 'submitted' OR status = 'accepted')
          AND MONTH(date) = :currentMonth AND YEAR(date) = :currentYear";

$stmt = $conn->prepare($query);
$stmt->bindParam(':currentMonth', $currentMonth, PDO::PARAM_INT);
$stmt->bindParam(':currentYear', $currentYear, PDO::PARAM_INT);
$stmt->execute();

// Using fetchColumn() to get the count directly
$toursThisMonth = $stmt->fetchColumn();
$stmt->closeCursor();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tours This Month</title>
</head>
<body>
    <h1>Tours This Month: <?php echo $toursThisMonth; ?></h1>
</body>
</html>
