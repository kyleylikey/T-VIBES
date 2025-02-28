<?php

// Only allow managers to access this controller
if (!isset($_SESSION['userid']) || $_SESSION['usertype'] !== 'mngr') {
    header('Location: ../views/admin/login.php');
    exit();
}

// Include your Database configuration
require_once __DIR__ .'/../config/dbconnect.php';

// Create a database connection instance using PDO
$db = new Database();
$conn = $db->getConnection();  // This should return a PDO connection

// Get current month and year
$currentMonth = date('n'); // Numeric representation without leading zeros
$currentYear  = date('Y');

// Determine last month (account for January)
if ($currentMonth == 1) {
    $lastMonth = 12;
    $lastYear  = $currentYear - 1;
} else {
    $lastMonth = $currentMonth - 1;
    $lastYear  = $currentYear;
}

// 1. Tours This Month: Count tours with status 'accepted'
$query = "SELECT COUNT(*) as total FROM tour 
          WHERE status = 'accepted'
          AND MONTH(date) = :currentMonth AND YEAR(date) = :currentYear";
$stmt = $conn->prepare($query);
$stmt->bindParam(':currentMonth', $currentMonth, PDO::PARAM_INT);
$stmt->bindParam(':currentYear', $currentYear, PDO::PARAM_INT);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$toursThisMonth = $row['total'];
$stmt->closeCursor();

// 2. Approved Tours (Current Month): Count tours with status 'accepted'
$query = "SELECT COUNT(*) as total FROM tour 
          WHERE status = 'accepted' 
          AND MONTH(date) = :currentMonth AND YEAR(date) = :currentYear";
$stmt = $conn->prepare($query);
$stmt->bindParam(':currentMonth', $currentMonth, PDO::PARAM_INT);
$stmt->bindParam(':currentYear', $currentYear, PDO::PARAM_INT);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$approvedToursCurrent = $row['total'];
$stmt->closeCursor();

// Approved Tours (Last Month)
$query = "SELECT COUNT(*) as total FROM tour 
          WHERE status = 'accepted' 
          AND MONTH(date) = :lastMonth AND YEAR(date) = :lastYear";
$stmt = $conn->prepare($query);
$stmt->bindParam(':lastMonth', $lastMonth, PDO::PARAM_INT);
$stmt->bindParam(':lastYear', $lastYear, PDO::PARAM_INT);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$approvedToursLast = $row['total'];
$stmt->closeCursor();

// 3. Cancelled Tours (Current Month)
$query = "SELECT COUNT(*) as total FROM tour 
          WHERE status = 'cancelled' 
          AND MONTH(date) = :currentMonth AND YEAR(date) = :currentYear";
$stmt = $conn->prepare($query);
$stmt->bindParam(':currentMonth', $currentMonth, PDO::PARAM_INT);
$stmt->bindParam(':currentYear', $currentYear, PDO::PARAM_INT);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$cancelledToursCurrent = $row['total'];
$stmt->closeCursor();

// Cancelled Tours (Last Month)
$query = "SELECT COUNT(*) as total FROM tour 
          WHERE status = 'cancelled' 
          AND MONTH(date) = :lastMonth AND YEAR(date) = :lastYear";
$stmt = $conn->prepare($query);
$stmt->bindParam(':lastMonth', $lastMonth, PDO::PARAM_INT);
$stmt->bindParam(':lastYear', $lastYear, PDO::PARAM_INT);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$cancelledToursLast = $row['total'];
$stmt->closeCursor();

// 4. Completed Tours (Current Month): Count tours with status 'accepted' and tour date in the past
$query = "SELECT COUNT(*) as total FROM tour 
          WHERE status = 'accepted' AND date < CURDATE() 
          AND MONTH(date) = :currentMonth AND YEAR(date) = :currentYear";
$stmt = $conn->prepare($query);
$stmt->bindParam(':currentMonth', $currentMonth, PDO::PARAM_INT);
$stmt->bindParam(':currentYear', $currentYear, PDO::PARAM_INT);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$completedToursCurrent = $row['total'];
$stmt->closeCursor();

// Completed Tours (Last Month)
$query = "SELECT COUNT(*) as total FROM tour 
          WHERE status = 'accepted' AND date < CURDATE() 
          AND MONTH(date) = :lastMonth AND YEAR(date) = :lastYear";
$stmt = $conn->prepare($query);
$stmt->bindParam(':lastMonth', $lastMonth, PDO::PARAM_INT);
$stmt->bindParam(':lastYear', $lastYear, PDO::PARAM_INT);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$completedToursLast = $row['total'];
$stmt->closeCursor();

// 5. Calculate Percentage Differences
function calculatePercentageDiff($current, $last) {
    if ($last == 0) {
        return 0; // Avoid division by zero; adjust if needed
    }
    return round((($current - $last) / $last) * 100, 2);
}

$approvedDiff  = calculatePercentageDiff($approvedToursCurrent, $approvedToursLast);
$cancelledDiff = calculatePercentageDiff($cancelledToursCurrent, $cancelledToursLast);
$completedDiff = calculatePercentageDiff($completedToursCurrent, $completedToursLast);

// 6. Busiest Days: Get top 3 days in current month with most accepted tours
$query = "SELECT DAY(date) as day, COUNT(*) as count FROM tour 
          WHERE status = 'accepted' 
          AND MONTH(date) = :currentMonth AND YEAR(date) = :currentYear 
          GROUP BY DAY(date) ORDER BY count DESC LIMIT 3";
$stmt = $conn->prepare($query);
$stmt->bindParam(':currentMonth', $currentMonth, PDO::PARAM_INT);
$stmt->bindParam(':currentYear', $currentYear, PDO::PARAM_INT);
$stmt->execute();
$busiestDays = $stmt->fetchAll(PDO::FETCH_ASSOC);
$stmt->closeCursor();

// 7. Top Tourist Sites: Get details and count accepted tours per site
$query = "SELECT s.siteid, s.sitename, s.siteimage, s.description, s.opdays, s.rating as ratings, s.price, s.status, COUNT(t.tourid) as tour_count 
          FROM sites s 
          LEFT JOIN tour t ON s.siteid = t.siteid AND t.status = 'accepted'
          GROUP BY s.siteid
          ORDER BY tour_count DESC
          LIMIT 3";
$stmt = $conn->prepare($query);
$stmt->execute();
$topSites = $stmt->fetchAll(PDO::FETCH_ASSOC);
$stmt->closeCursor();

// 8. Visitor Chart Data: Get the total number of companions per month for the current year
$query = "SELECT MONTH(date) as month, SUM(companions) as total 
          FROM tour 
          WHERE YEAR(date) = :currentYear 
          GROUP BY MONTH(date) 
          ORDER BY month ASC";
$stmt = $conn->prepare($query);
$stmt->bindParam(':currentYear', $currentYear, PDO::PARAM_INT);
$stmt->execute();
$chartResults = $stmt->fetchAll(PDO::FETCH_ASSOC);
$stmt->closeCursor();

// Initialize an array for 12 months (1 to 12) with 0 values
$visitorChartData = array_fill(1, 12, 0);

// Populate the visitorChartData array using query results
foreach ($chartResults as $row) {
    $month = (int)$row['month'];
    $visitorChartData[$month] = (int)$row['total'];
}
// Re-index the array so it starts from index 0 (for use in Chart.js)
$visitorChartData = array_values($visitorChartData);

// Now include the view file which will use these variables:
include_once __DIR__ . '/../views/admin/monthlyperformance.php';
?>
