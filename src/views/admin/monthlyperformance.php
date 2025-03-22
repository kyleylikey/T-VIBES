<?php
include '../../../includes/auth.php';
require_once '../../config/dbconnect.php';

$database = new Database();
$conn = $database->getConnection();

$toursThisMonthQuery = "
    SELECT COUNT(DISTINCT tourid) AS total_tours 
    FROM tour 
    WHERE status = 'accepted' 
    AND MONTH(date) = MONTH(CURRENT_DATE()) 
    AND YEAR(date) = YEAR(CURRENT_DATE())";

$toursThisMonthStmt = $conn->prepare($toursThisMonthQuery);
$toursThisMonthStmt->execute();
$toursThisMonth = $toursThisMonthStmt->fetch(PDO::FETCH_ASSOC);
$totalTours = $toursThisMonth['total_tours'];

function getTourCount($conn, $status, $dateCondition) {
    $query = "
        SELECT COUNT(DISTINCT tourid) AS total_tours 
        FROM tour 
        WHERE status = :status AND $dateCondition";
        
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':status', $status);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['total_tours'] ?? 0;
}

$thisMonthCondition = "MONTH(date) = MONTH(CURRENT_DATE()) AND YEAR(date) = YEAR(CURRENT_DATE())";
$lastMonthCondition = "MONTH(date) = MONTH(CURRENT_DATE() - INTERVAL 1 MONTH) AND YEAR(date) = YEAR(CURRENT_DATE() - INTERVAL 1 MONTH)";

$acceptedThisMonth = getTourCount($conn, 'accepted', $thisMonthCondition);
$acceptedLastMonth = getTourCount($conn, 'accepted', $lastMonthCondition);

$cancelledThisMonth = getTourCount($conn, 'cancelled', $thisMonthCondition);
$cancelledLastMonth = getTourCount($conn, 'cancelled', $lastMonthCondition);

$completedThisMonth = getTourCount($conn, 'accepted', "$thisMonthCondition AND date < CURRENT_DATE()");
$completedLastMonth = getTourCount($conn, 'accepted', "$lastMonthCondition AND date < CURRENT_DATE()");

function calculatePercentageChange($current, $previous) {
    if ($previous == 0) {
        return $current > 0 ? 100 : 0;
    }
    return round((($current - $previous) / $previous) * 100, 2);
}

$acceptedChange = calculatePercentageChange($acceptedThisMonth, $acceptedLastMonth);
$cancelledChange = calculatePercentageChange($cancelledThisMonth, $cancelledLastMonth);
$completedChange = calculatePercentageChange($completedThisMonth, $completedLastMonth);

$busiestDaysQuery = "
    SELECT DATE(date) AS tour_date, COUNT(DISTINCT tourid) AS total_tours 
    FROM tour 
    WHERE status = 'accepted' 
    AND MONTH(date) = MONTH(CURRENT_DATE()) 
    AND YEAR(date) = YEAR(CURRENT_DATE()) 
    GROUP BY DATE(date) 
    ORDER BY total_tours DESC 
    LIMIT 3";

$busiestDaysStmt = $conn->prepare($busiestDaysQuery);
$busiestDaysStmt->execute();
$busiestDays = $busiestDaysStmt->fetchAll(PDO::FETCH_ASSOC);

$topSitesQuery = "
    SELECT s.siteid, s.sitename, s.siteimage, SUM(t.companions) AS total_visitors
    FROM tour t
    JOIN sites s ON t.siteid = s.siteid
    WHERE t.status = 'accepted'
    AND MONTH(t.date) = MONTH(CURRENT_DATE()) 
    AND YEAR(t.date) = YEAR(CURRENT_DATE())
    GROUP BY t.siteid
    ORDER BY total_visitors DESC
    LIMIT 3";

$topSitesStmt = $conn->prepare($topSitesQuery);
$topSitesStmt->execute();
$topSites = $topSitesStmt->fetchAll(PDO::FETCH_ASSOC);

$today = new DateTime();
$currentYear = $today->format('Y');
$currentMonth = $today->format('m');
$daysInMonth = cal_days_in_month(CAL_GREGORIAN, $currentMonth, $currentYear);

$visitorData = array_fill(1, $daysInMonth, 0);

$query = "SELECT DAY(date) AS day, SUM(companions) AS total_visitors 
          FROM (SELECT userid, date, companions FROM tour 
                WHERE YEAR(date) = :year AND MONTH(date) = :month AND status = 'accepted' GROUP BY tourid, userid
            ) AS distinct_tours
          GROUP BY DAY(date)";

$stmt = $conn->prepare($query);
$stmt->bindParam(':year', $currentYear, PDO::PARAM_INT);
$stmt->bindParam(':month', $currentMonth, PDO::PARAM_INT);
$stmt->execute();

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $visitorData[$row['day']] = $row['total_visitors'];
}

$userid = $_SESSION['userid'];
$query = "SELECT name FROM Users WHERE userid = :userid LIMIT 1";
$stmt = $conn->prepare($query);
$stmt->bindParam(':userid', $userid);
$stmt->execute();
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

$adminName = $admin ? htmlspecialchars($admin['name']) : "Admin";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Monthly Performance</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;700&family=Raleway:wght@300;400;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        * {
            box-sizing: border-box;
        }
        
        .sidebar {
            font-family: 'Raleway', sans-serif;
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 250px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 20px;
            background-color: #FFFFFF;
            z-index: 1000;
            transition: all 0.3s ease-in-out;
        }

        .sidebar img {
            max-width: 100%; 
            height: auto;
            display: block; 
            margin-top: auto;
            margin-bottom: 25%;
            transition: all 0.3s ease-in-out; 
        }

        .menu-section {
            margin-top: auto;
            margin-bottom: auto;
        }

        .nav-link {
            color: #434343 !important;
            padding: 10px;
            border-radius: 5px;
            font-weight: bold; 
            transition: background 0.3s ease, color 0.3s ease;
        }

        .nav-link.active {
            background-color: #EC6350 !important;
            color: #FFFFFF !important;
            font-weight: bold;
        }

        .nav-link:hover {
            background-color: #EC6350 !important; 
            color: #FFFFFF !important;
        }

        .nav-link i {
            color: inherit; 
        }
        
        .admin-name.active {
            background-color: #102E47 !important;
            color: #FFFFFF !important;
            font-weight: bold;
        }

        .sign-out.active {
            background-color: #E7EBEE !important;
            color: #102E47 !important;
            font-weight: bold;
        }

        .content-container {
            background-color: #E7EBEE;
            padding: 20px;
            border-radius: 10px;
        }

        .main-content {
            margin-left: 260px;
            padding: 20px;
            transition: all 0.3s ease-in-out;
            width: calc(100% - 260px); 
        }

        .header {
            font-family: 'Raleway', sans-serif;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap; 
            gap: 10px; 
        }

        .date {
            text-align: right; 
            min-width: 150px; 
            flex-shrink: 0; 
        }

        .content-container h2, 
        .content-container .date h2 {
            color: #102E47;
            font-weight: bold;
        }

        .info-box {
            position: relative;
            min-height: 150px;
            width: 100%; 
            background-color: #729AB8;
            border-radius: 10px;
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
            padding: 15px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: flex-start;
            text-align: left;
            color: #FFFFFF;
        }

        .info-box:hover,
        .busy-days-box:hover,
        .tour-sites-box:hover,
        .visitors-box:hover {
            transform: scale(1.03);
            transition: all 0.3s;
        }

        .info-box span {
            font-size: 18px;
            font-weight: bold; 
            color: #FFFFFF; 
        }

        .info-box h1 {
            font-weight: 900;
            align-self: flex-end;
            color: #FFFFFF; 
        }

        .info-box i {
            font-size: 40px;
            opacity: 0.3;
            align-self: flex-end;
            color: inherit; 
        }

        .info-box span,
        .info-box h1,
        .info-box i,
        .table,
        .btn-custom,
        .swal2-title-custom,
        .swal-custom-btn {
            font-family: 'Nunito', sans-serif !important;
        }

        .stats-container {
            min-height: 150px;
            background-color: rgba(114, 154, 184, 0.2);
            border-radius: 10px;
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
            height: 100%;
            display: flex;
            align-items: center;
            padding: 15px;
            gap: 15px; 
        }

        .stat-box {
            flex: 1; 
            height: 100%;
            background-color: #FFFFFF;
            border-radius: 10px;
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
            padding: 15px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            font-family: 'Nunito', sans-serif !important;
            position: relative;
        }

        .stat-header {
            display: flex;
            justify-content: space-between;
            width: 100%;
        }

        .stat-title {
            font-size: 18px;
            font-weight: bold;
            color: #102E47;
        }

        .stat-percentage {
            font-size: 14px;
            font-weight: bold;
        }

        .stat-value {
            font-weight: 900;
            color: #102E47;
            text-align: left;
        }

        .stat-footer {
            display: flex;
            justify-content: space-between;
            width: 100%;
            align-items: center;
        }

        .stat-compare {
            font-size: 14px;
            color: #102E47;
        }

        .stat-last-value {
            font-size: 14px;
            font-weight: bold;
            color: #102E47;
        }

        .busy-days-box,
        .tour-sites-box {
            background-color: rgba(114, 154, 184, 0.2);
            border-radius: 10px;
            padding: 15px;
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
            font-family: 'Nunito', sans-serif;
            color: #434343;
        }

        .tour-sites-box i {
            color: #939393;
            font-size: 32px;
        }

        .section-title {
            font-size: 18px;
            font-weight: bold;
            display: block;
            margin-bottom: 15px;
        }

        .inner-box {
            background-color: #E7EBEE;
            border-radius: 25px;
            width: 150px;
            height: 150px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            padding: 10px;
            position: relative;
            margin-bottom: 10px;
        }

        .inner-box i {
            color: #939393;
            font-size: 32px;
        }

        .destination-name {
            font-size: 14px;
            font-weight: bold;
            white-space: nowrap; 
            overflow: hidden;
            text-overflow: ellipsis; 
            width: 100%;
            color: #434343;
        }

        .rating {
            font-size: 12px;
            display: flex;
            align-items: center;
            gap: 3px;
        }

        .rating i {
            color: #A9221C; 
            font-size: 12px;
        }

        .rating span {
            color: #757575;
            font-size: 12px;
        }

        .inner-box .month {
            font-size: 18px;
            color: #102E47; 
            position: absolute;
            top: 15px;
            left: 15px;
            font-weight: bold;
        }

        .inner-box .day {
            font-size: 32px;
            font-weight: bold;
            color: #102E47;
            margin-top: 20px; 
            margin-bottom: 20px;
        }

        .inner-box .tours {
            font-size: 12px;
            color: #434343;
        }

        .tour-sites-box .d-flex {
            justify-content: space-evenly; 
            flex-wrap: wrap;
        }

        .tour-card {
            flex: 1 1 150px; 
            max-width: 150px; 
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .visitors-box {
            background-color: rgba(114, 154, 184, 0.2);
            border-radius: 10px;
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
            height: calc(100% - 95px); 
            min-height: 350px;
            padding: 15px;
            color: #434343;
            font-family: 'Nunito', sans-serif;
            margin-bottom: 5%;
        }

        .inner-container {
            background-color: #FFFFFF; 
            border-radius: 10px;
            width: 100%;
            height: 88%; 
            margin-top: 10px;
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
            padding: 15px;
        }

        .btn-custom {
            padding: 10px 20px;
            font-size: 16px;
            font-weight: bold;
            border: 2px solid #102E47;
            border-radius: 25px;
            background-color: #FFFFFF; 
            color: #434343; 
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
            display: block;
            margin: 5px auto 0 auto;
            width: 100%;
        }

        .btn-custom:hover {
            background-color: #102E47;
            color: #FFFFFF;
            border: 2px solid #102E47;
            font-weight: bold;
        }

        .chart-container {
            width: 100%;
            height: 100%;
        }

        .swal2-icon {
            background: none !important;
            border: none !important;
            box-shadow: none !important;
        }

        .swal2-icon-custom {
            font-size: 10px; 
            color: #EC6350; 
        }

        .swal2-title-custom {
            font-size: 24px !important;
            font-weight: bold;
            color: #434343 !important;
        }

        .swal-custom-popup {
            padding: 20px;
            border-radius: 25px;
            font-family: 'Nunito', sans-serif !important;
        }

        .swal-custom-btn {
            padding: 10px 20px !important;
            font-size: 16px !important;
            font-weight: bold !important;
            border: 2px solid #102E47 !important;
            border-radius: 25px !important;
            background-color: #FFFFFF !important;
            color: #434343 !important;
            cursor: pointer !important;
            transition: all 0.3s ease !important;
        }

        .swal-custom-btn:hover {
            background-color: #102E47 !important;
            color: #FFFFFF !important;
        }

        @media (max-width: 1280px) {
            .busy-days-box .d-flex,
            .tour-sites-box .d-flex {
                flex-wrap: wrap;
                justify-content: center;
                gap: 20px;
            }

            .inner-box {
                width: 140px;
                height: 140px;
            }

            .tour-card {
                flex: 1 1 calc(33.33% - 20px); 
                max-width: 160px;
                text-align: center;
            }

            .inner-box .month {
                font-size: 16px;
            }

            .inner-box .day {
                font-size: 28px;
            }

            .rating i {
                font-size: 12px; 
            }

            .col-lg-6 {
                width: 100%;
            }

            .col-lg-6:first-child {
                display: flex;
                flex-wrap: wrap;
                justify-content: center;
                gap: 10px;
            }

            .busy-days-box {
                width: 100%;
            }

            .tour-sites-box {
                width: 100%;
            }

            .visitors-box {
                width: 100%;
            }
        }

        @media (max-width: 1024px) {
            .sidebar {
                width: 250px; 
                padding: 15px;
            }

            .sidebar img {
                margin-bottom: 0;
            }

            .nav-link {
                font-size: 14px; 
                margin-bottom: -5%;
            }

            .menu-section {
                padding: 5px 0;
                margin-top: auto;
                margin-bottom: auto;
            }

            .main-content {
                margin-left: 250px;
                width: calc(100% - 250px); 
            }

            .info-box {
                display: flex;
                flex-direction: column;
                justify-content: space-between;
                align-items: flex-start;
                height: 100%; 
                width: 100%;
            }

            .col-lg-4 {
                display: flex;
            }

            .stats-container {
                flex-direction: column; 
                align-items: center; 
                gap: 10px; 
                padding: 10px; 
            }

            .stat-box {
                width: 100%;
                max-width: 320px; 
                padding: 12px; 
                text-align: center; 
            }

            .stat-header {
                flex-direction: column; 
                text-align: center;
            }

            .stat-value {
                font-size: 24px; 
                text-align: center;
            }

            .stat-footer {
                flex-direction: column; 
                text-align: center;
            }

            .stat-percentage {
                font-size: 14px; 
            }

            .stat-last-value {
                font-size: 14px;
            }
        }

        @media (max-width: 912px) {
            .sidebar {
                width: 200px; 
                padding: 15px;
                background-color: #FFFFFF; 
            }

            .nav-link {
                font-size: 14px; 
                padding: 8px; 
                color: #434343 !important; 
                font-weight: bold; 
                transition: background 0.3s ease, color 0.3s ease;
            }

            .nav-link.active {
                background-color: #EC6350 !important;
                color: #FFFFFF !important;
                font-weight: bold;
            }

            .nav-link:hover {
                background-color: #EC6350 !important; 
                color: #FFFFFF !important;
            }

            .admin-name.active {
                background-color: #102E47 !important;
                color: #FFFFFF !important;
                font-weight: bold;
            }

            .sign-out.active {
                background-color: #E7EBEE !important;
                color: #102E47 !important;
                font-weight: bold;
            }

            .menu-section {
                margin-top: auto;
                margin-bottom: auto;
                padding: 10px 0; 
            }

            .main-content {
                margin-left: 200px;
                width: calc(100% - 200px);
            }

            .busy-days-box .d-flex,
            .tour-sites-box .d-flex {
                flex-wrap: wrap; 
                justify-content: center; 
                gap: 15px; 
            }

            .inner-box {
                width: 130px; 
                height: 130px;
            }

            .tour-card {
                flex: 1 1 calc(33.33% - 20px); 
                max-width: 140px;
                text-align: center;
            }

            .inner-box .month {
                font-size: 16px;
            }

            .inner-box .day {
                font-size: 26px;
            }

            .rating i {
                font-size: 10px; 
            }

            .stats-container {
                flex-direction: column; 
                align-items: stretch; 
                gap: 10px; 
                padding: 10px; 
            }

            .stat-box {
                width: 100%; 
                padding: 12px; 
                text-align: center; 
            }

            .stat-header {
                flex-direction: column; 
                text-align: center;
            }

            .stat-value {
                font-size: 24px; 
                text-align: center;
            }

            .stat-footer {
                flex-direction: column; 
                text-align: center;
            }

            .stat-percentage {
                font-size: 14px; 
            }

            .stat-last-value {
                font-size: 14px;
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 100px;
                padding: 10px;
                background-color: #FFFFFF;
            }

            .sidebar img {
                max-width: 70px; 
            }

            .nav-link {
                text-align: center;
                padding: 10px;
                color: #434343 !important; 
                font-weight: bold;
                font-size: 12px;
            }

            .nav-link span {
                display: none; 
            }

            .nav-link.active {
                background-color: #EC6350 !important;
                color: #FFFFFF !important;
                font-weight: bold;
            }

            .nav-link:hover {
                background-color: #EC6350 !important; 
                color: #FFFFFF !important;
            }

            .admin-name.active {
                background-color: #102E47 !important;
                color: #FFFFFF !important;
                font-weight: bold;
            }

            .sign-out.active {
                background-color: #E7EBEE !important;
                color: #102E47 !important;
                font-weight: bold;
            }

            .main-content {
                margin-left: 100px; 
                width: calc(100% - 100px); 
            }

            .header {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }

            .date {
                text-align: center; 
                width: 100%; 
            }

            .row {
                justify-content: center !important;
            }

            .info-box {
                width: 100% !important; 
                max-width: 300px; 
                text-align: center;
                align-items: center;
                margin: 0 auto; 
            }

            .info-box i, 
            .info-box h1 {
                align-self: center;
            }

            .busy-days-box .d-flex,
            .tour-sites-box .d-flex {
                flex-wrap: wrap; 
                justify-content: center; 
                gap: 10px; 
            }

            .inner-box {
                width: 120px; 
                height: 120px;
                margin-bottom: 10px; 
            }

            .tour-card {
                flex: 1 1 calc(33.33% - 20px); 
                max-width: 140px; 
                text-align: center;
            }

            .inner-box .month {
                font-size: 16px;
            }

            .inner-box .day {
                font-size: 26px;
            }

            .rating i {
                font-size: 10px;
            }

            .stats-container {
                flex-direction: column; 
                align-items: stretch; 
                gap: 10px; 
                padding: 10px; 
            }

            .stat-box {
                width: 100%; 
                padding: 12px; 
                text-align: center; 
            }

            .stat-title {
                font-size: 16px; 
            }

            .stat-value {
                font-size: 24px; 
            }

            .stat-footer {
                flex-direction: column; 
                text-align: center; 
            }

            .stat-percentage {
                font-size: 12px; 
            }
        }

        @media (max-width: 600px) {
            .sidebar {
                width: 80px;
                padding: 5px;
                background-color: #FFFFFF;
            }

            .nav-link {
                color: #434343 !important;
                font-weight: bold;
            }

            .nav-link i {
                font-size: 20px;
            }

            .nav-link span {
                display: none;
            }

            .nav-link.active {
                background-color: #EC6350 !important;
                color: #FFFFFF !important;
                font-weight: bold;
            }

            .nav-link:hover {
                background-color: #EC6350 !important; 
                color: #FFFFFF !important;
            }

            .admin-name.active {
                background-color: #102E47 !important;
                color: #FFFFFF !important;
                font-weight: bold;
            }

            .sign-out.active {
                background-color: #E7EBEE !important;
                color: #102E47 !important;
                font-weight: bold;
            }

            .main-content {
                margin-left: 80px;
                width: calc(100% - 80px);
            }

            .info-box {
                min-height: 120px;
                min-width: auto;
                padding: 10px;
            }

            .info-box h1 {
                font-size: 20px;
            }

            .info-box i {
                font-size: 30px;
            }

            .busy-days-box .d-flex,
            .tour-sites-box .d-flex {
                flex-direction: column; 
                align-items: center; 
            }

            .inner-box {
                width: 120px; 
                height: 120px;
                margin-bottom: 10px;
            }

            .tour-card {
                width: 100%; 
                text-align: center;
                margin-bottom: 10px;
            }

            .inner-box .month {
                font-size: 14px;
            }

            .inner-box .day {
                font-size: 28px;
            }

            .rating i {
                font-size: 10px; 
            }

            .busy-days-box span,
            .tour-sites-box span,
            .visitors-box span {
                text-align: center;
            }

            .stats-container {
                flex-direction: column;
                align-items: center; 
                justify-content: center;
                gap: 10px;
                padding: 10px;
                width: 100%; 
            }

            .stat-box {
                width: 100%; 
                max-width: 400px; 
                text-align: center;
            }

            .stat-title {
                font-size: 16px;
            }

            .stat-value {
                font-size: 22px; 
            }

            .stat-footer {
                flex-direction: column; 
                text-align: center; 
            }

            .stat-percentage {
                font-size: 12px; 
            }
        }

        @media (max-width: 360px) {
            .stats-container {
                flex-direction: column; 
                gap: 10px; 
                padding: 10px; 
            }

            .stat-box {
                width: 100%; 
                padding: 10px; 
                text-align: center; 
            }

            .stat-title {
                font-size: 16px; 
            }

            .stat-value {
                font-size: 20px; 
            }

            .stat-footer {
                flex-direction: column; 
                text-align: center;
            }

            .stat-percentage {
                font-size: 12px; 
            }

            .visitors-box {
                height: auto; 
                min-height: 250px; 
            }

            .inner-container {
                height: auto; 
                min-height: 250px;
            }
        }
    </style>
</head>
<body>
    
<div class="sidebar">
    <div class="pt-4 pb-1 px-2 text-center">
        <a href="#" class="text-decoration-none">
            <img src="../../../public/assets/images/headerlogo.jpg" alt="Header Logo" class="img-fluid">
        </a>
    </div>

    <div class="menu-section">
        <ul class="nav nav-pills flex-column mb-4">
            <li class="nav-item mb-4">
                <a href="home.php" class="nav-link">
                    <i class="bi bi-grid"></i>
                    <span class="d-none d-sm-inline">Overview</span>
                </a>
            </li>
            <li class="nav-item mb-4">
                <a href="" class="nav-link active">
                    <i class="bi bi-bar-chart-line"></i>
                    <span class="d-none d-sm-inline">Monthly Performance</span>
                </a>
            </li>
            <li class="nav-item mb-4">
                <a href="tourhistory.php" class="nav-link">
                    <i class="bi bi-map"></i>
                    <span class="d-none d-sm-inline">Tour History</span>
                </a>
            </li>
            <li class="nav-item mb-4">
                <a href="touristsites.php" class="nav-link">
                    <i class="bi bi-image"></i>
                    <span class="d-none d-sm-inline">Tourist Sites</span>
                </a>
            </li>
            <li class="nav-item mb-4">
                <a href="accounts.php?usertype=mngr" class="nav-link">
                    <i class="bi bi-people"></i>
                    <span class="d-none d-sm-inline">Accounts</span>
                </a>
            </li>
            <li class="nav-item mb-4">
                <a href="employeelogs.php" class="nav-link">
                    <i class="bi bi-person-vcard"></i>
                    <span class="d-none d-sm-inline">Employee Logs</span>
                </a>
            </li>
        </ul>
    </div>
        
    <ul class="nav nav-pills flex-column mb-4">
        <li class="nav-item mb-3">
            <a href="javascript:void(0);" class="nav-link admin-name active">
                <i class="bi bi-person-circle"></i>
                <span class="d-none d-sm-inline"><?= $adminName; ?></span>
            </a>
        </li>
        <li class="nav-item">
            <a href="javascript:void(0);" class="nav-link sign-out active" onclick="logoutConfirm()">
                <i class="bi bi-box-arrow-right"></i>
                <span class="d-none d-sm-inline">Sign Out</span>
            </a>
        </li>
    </ul>
</div>

<div class="main-content">
    <div class="content-container">
        <div class="header">
            <h2>Monthly Performance</h2>
            <span class="date">
                <h2>
                    <?php 
                        date_default_timezone_set('Asia/Manila');
                        echo date('M d, Y | h:i A'); 
                    ?>
                </h2>
            </span>
        </div>

        <div class="row mt-3">
            <div class="col-lg-4 col-md-6 col-12 mb-3">
                <div class="info-box" onclick="window.location.href='statistics/tourstatistics.php';" style="cursor: pointer;">
                    <span>Tours This Month</span>
                    <i class="bi bi-map-fill"></i>
                    <h1><?php echo $totalTours; ?></h1>
                </div>
            </div>
            <div class="col-lg-8 col-md-6 col-12 mb-3">
                <div class="stats-container">
                    <!-- Accepted Tours -->
                    <div class="stat-box">
                        <div class="stat-header">
                            <span class="stat-title">Accepted</span>
                            <span class="stat-percentage <?= $acceptedChange >= 0 ? 'text-success' : 'text-danger' ?>">
                                <?= abs($acceptedChange) ?>% <?= $acceptedChange >= 0 ? '▲' : '▼' ?>
                            </span>
                        </div>
                        <h1 class="stat-value"><?= $acceptedThisMonth ?></h1>
                        <div class="stat-footer">
                            <span class="stat-compare"><b>vs. last month</b></span>
                            <span class="stat-last-value"><?= $acceptedLastMonth ?></span>
                        </div>
                    </div>

                    <!-- Cancelled Tours -->
                    <div class="stat-box">
                        <div class="stat-header">
                            <span class="stat-title">Cancelled</span>
                            <span class="stat-percentage <?= $cancelledChange >= 0 ? 'text-danger' : 'text-success' ?>">
                                <?= abs($cancelledChange) ?>% <?= $cancelledChange >= 0 ? '▲' : '▼' ?>
                            </span>
                        </div>
                        <h1 class="stat-value"><?= $cancelledThisMonth ?></h1>
                        <div class="stat-footer">
                            <span class="stat-compare"><b>vs. last month</b></span>
                            <span class="stat-last-value"><?= $cancelledLastMonth ?></span>
                        </div>
                    </div>

                    <!-- Completed Tours -->
                    <div class="stat-box">
                        <div class="stat-header">
                            <span class="stat-title">Completed</span>
                            <span class="stat-percentage <?= $completedChange >= 0 ? 'text-warning' : 'text-danger' ?>">
                                <?= abs($completedChange) ?>% <?= $completedChange >= 0 ? '▲' : '▼' ?>
                            </span>
                        </div>
                        <h1 class="stat-value"><?= $completedThisMonth ?></h1>
                        <div class="stat-footer">
                            <span class="stat-compare"><b>vs. last month</b></span>
                            <span class="stat-last-value"><?= $completedLastMonth ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div> 
        <div class="row mt-3 d-flex justify-content-start">
            <div class="col-lg-6 col-md-6 col-12">
                <div class="row">

                <div class="col-lg-12 mb-3">
                        <div class="busy-days-box" onclick="window.location.href='statistics/busiestmonthstatistics.php';" style="cursor: pointer;">
                            <span class="section-title">Busiest Days This Month</span>
                            <div class="d-flex justify-content-around">
                                <?php foreach ($busiestDays as $day): ?>
                                    <div class="inner-box">
                                        <span class="month"><?php echo date('M', strtotime($day['tour_date'])); ?></span>
                                        <span class="day"><?php echo date('d', strtotime($day['tour_date'])); ?></span>
                                        <span class="tours"><?php echo $day['total_tours']; ?> Tour/s</span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-12 mb-3">
                        <div class="tour-sites-box" onclick="window.location.href='statistics/toptouristsites.php';" style="cursor: pointer;">
                            <span class="section-title">Top Tourist Sites This Month</span>
                            <div class="d-flex justify-content-around">
                                <?php foreach ($topSites as $site): ?>
                                    <div class="tour-card">
                                        <div class="inner-box">
                                            <img src="/T-VIBES/public/uploads/<?php echo htmlspecialchars($site['siteimage']); ?>" alt="<?php echo htmlspecialchars($site['sitename']); ?>" style="width: 100%; height: 100%; object-fit: cover; border-radius: 25px;">
                                        </div>
                                        <span class="destination-name"><?php echo htmlspecialchars($site['sitename']); ?></span>
                                        <div class="rating">
                                            <span>Total Visitors: <?php echo $site['total_visitors']; ?></span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="col-lg-6 col-md-6 col-12 d-flex flex-column">
                <div class="visitors-box" onclick="window.location.href='statistics/visitorstatistics.php';" style="cursor: pointer;">
                    <span class="section-title">Visitors This Month</span>
                    <div class="inner-container">
                        <div class="chart-container">
                            <canvas id="visitorStatisticsChart"></canvas>
                        </div>
                    </div>
                </div>
                <button class="btn-custom"><i class="bi bi-file-earmark-arrow-down-fill"></i> Download PDF Report</button>
            </div>

        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/js/all.min.js"></script>
<script src="../../../public/assets/scripts/main.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    var daysArray = <?php echo json_encode(range(1, $daysInMonth)); ?>;
    var visitorData = <?php echo json_encode(array_values($visitorData)); ?>;

    var ctx = document.getElementById('visitorStatisticsChart').getContext('2d');
    var visitorStatisticsChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: daysArray.map(day => day.toString()),
            datasets: [
                {
                    label: 'Total Visitors',
                    data: visitorData,
                    borderColor: '#102E47',
                    borderWidth: 2,
                    fill: false
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: {
                    title: {
                        display: true,
                        text: '<?php echo date("F Y"); ?>',
                        font: {
                            family: 'Nunito'
                        }
                    },
                    ticks: {
                        font: {
                            family: 'Nunito'
                        }
                    }
                },
                y: {
                    ticks: {
                        font: {
                            family: 'Nunito'
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    labels: {
                        font: {
                            family: 'Nunito'
                        }
                    }
                }
            }
        }
    });
</script>
</body>
</html>