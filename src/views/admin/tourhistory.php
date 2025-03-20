<?php
include '../../../includes/auth.php';
require_once '../../config/dbconnect.php';
require_once '../../controllers/admin/tourhistorycontroller.php';

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
    <title>Admin Dashboard - Tour History</title>
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

        .btn-custom {
            padding: 10px 20px;
            font-size: 16px;
            font-weight: bold;
            border: 2px solid #102E47;
            border-radius: 25px;
            background-color: white;
            color: #434343;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-right: 10px;
            margin-top: 10px;
            min-width: 80px;
            text-align: center;
            font-family: 'Nunito', sans-serif !important;
        }

        .btn-custom.active {
            background-color: #102E47 !important;
            color: #FFFFFF !important;
            font-weight: bold;
        }

        .btn-custom:hover {
            background-color: #102E47;
            color: #FFFFFF;
            font-weight: bold;
        }

        .info-box {
            position: relative;
            min-height: 150px;
            min-width: 200px; 
            background-color: rgba(114, 154, 184, 0.2); 
            border-radius: 10px;
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
            padding: 15px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: flex-start;
            text-align: left;
            font-family: 'Nunito', sans-serif;
        }

        .info-box span {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 4px;
        }

        .info-box i {
            font-size: 40px;
            opacity: 0.3;
            align-self: flex-end;
        }

        .table-responsive {
            overflow-x: auto; 
            width: 100%;
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 10px; 
            margin: auto;
            text-align: center;
            overflow-x: auto;
            min-width: 500px;
            font-family: 'Nunito', sans-serif;
        }

        thead th {
            color: #434343; 
            font-weight: bold;
            padding-bottom: 10px;
            background: none !important;
            border: none !important;
            box-shadow: none !important;
        }

        tbody tr {
            background: #FFFFFF; 
            border-radius: 15px;
            box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1); 
        }

        tbody tr td {
            padding: 10px;
            border: none;
            color: #434343; 
        }

        tbody tr td:first-child {
            border-top-left-radius: 15px;
            border-bottom-left-radius: 15px;
        }

        tbody tr td:last-child {
            border-top-right-radius: 15px;
            border-bottom-right-radius: 15px;
        }

        .modal-title {
            font-size: 24px;
            font-weight: bold;
            color: #434343;
        }

        .stepper {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start; 
            padding-top: 10px; 
            margin-top: 50px;
        }

        .step {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .circle {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background-color: #FFFFFF;
            border: 4px solid #102E47;
            color: #434343;
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: bold;
        }

        .dashed-line {
            width: 2px;
            height: 100px; 
            border-left: 4px dashed #434343;
        }

        .destination-card {
            display: flex;
            align-items: center;
            padding: 12px;
            width: 100%;
            max-width: 350px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            background-color: #FFFFFF;
        }

        .image-placeholder {
            width: 100px;
            height: 100px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #E7EBEE;
            border-radius: 8px;
        }

        .image-placeholder i {
            font-size: 24px;
            color: #939393;
        }

        .destination-info {
            margin-left: 12px;
        }

        .destination-info h6 {
            margin-bottom: 4px;
            font-weight: bold;
            color: #102E47;
        }

        .destination-info p {
            margin-bottom: 0;
            color: #757575;
        }

        .modal-body {
            display: flex;
            align-items: center; 
            gap: 20px;
        }

        .d-flex {
            display: flex;
            gap: 20px;
        }

        .destination-container {
            display: flex;
            flex-direction: column;
            justify-content: center; 
        }

        .first-card {
            margin-top: -30px; 
        }

        .second-card {
            margin-top: 20px; 
        }

        .modal-dialog {
            max-width: 50%;
        }

        .modal-content {
            background-color: #E7EBEE;
            min-height: 50%; 
            border-radius: 25px;
            font-family: 'Nunito', sans-serif !important;
        }

        .summary-container {
            flex-grow: 1; 
            min-height: 250px; 
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .summary-container p:nth-child(1) { 
            margin-bottom: 20px; 
            color: #102E47;
        }

        .summary-container p:nth-child(2) { 
            margin-bottom: 20px; 
            color: #102E47;
        }

        .summary-container p:nth-child(3) { 
            margin-bottom: 25px; 
            color: #102E47;
        } 

        .summary-container p:nth-child(1) span,
        .summary-container p:nth-child(2) span,
        .summary-container .estimated-fees p,
        .summary-container .total-price {
            color: #757575;
        }

        .summary-container p {
            margin-bottom: 5px; 
        }

        .estimated-fees {
            display: flex;
            flex-direction: column;
            gap: 4px; 
            justify-content: flex-end;
            text-align: right;
        }

        .estimated-fees p {
            display: flex;
            justify-content: space-between;
            width: 100%;
            margin-top: -20px;
        }

        .total-price {
            font-size: 16px;
            font-weight: bold;
            text-align: right;
        }

        .modal-content {
            border-radius: 25px; 
        }

        .tour-status {
            font-weight: bold;
            color: #102E47;
            font-size: 18px;
        }

        .no-completed-tours {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            color: #434343;
            padding: 8px;
            border-radius: 8px;
            width: fit-content;
            margin-left: auto;
            margin-right: auto;
            margin-top: 30px;
            font-family: 'Nunito', sans-serif !important;
        }

        .no-cancelled-tours {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            color: #434343;
            padding: 10px;
            border-radius: 8px;
            width: fit-content;
            margin: 10px auto;
            font-family: 'Nunito', sans-serif !important;
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
            .btn-custom {
                padding: 8px 16px;
                font-size: 15px;
            }

            .modal-dialog {
                max-width: 60%;
            }

            .destination-card {
                max-width: 300px;
            }

            .circle {
                width: 25px;
                height: 25px;
                font-size: 14px;
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

            .modal-dialog {
                max-width: 80%; 
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

            .btn-custom {
                padding: 8px 14px;
                font-size: 14px;
                min-width: 70px;
            }

            .modal-dialog {
                max-width: 65%;
            }

            .modal-body {
                flex-direction: column;
                align-items: center;
                margin-top: 20px;
            }

            .summary-container {
                text-align: left; 
                align-items: flex-start;
                margin-top: 20px;
            }

            .destination-container {
                align-items: center;
            }

            .estimated-fees {
                display: flex;
                flex-direction: column;
                width: 100%;
            }

            .estimated-fees p {
                display: flex;
                justify-content: space-between;
                width: 100%;
            }

            .estimated-fees span {
                margin-left: 120px;
                text-align: right;
            }

            .total-price {
                text-align: right;
                width: 100%;
                font-weight: bold;
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

            .btn-custom {
                padding: 6px 12px;
                font-size: 14px;
            }

            .modal-dialog {
                max-width: 70%;
            }

            .modal-body {
                flex-direction: column;
                align-items: center;
            }

            .destination-card {
                max-width: 250px;
            }

            .summary-container {
                text-align: left; 
                align-items: flex-start;
                margin-top: 20px;
            }

            .estimated-fees {
                display: flex;
                flex-direction: column;
                width: 100%;
            }

            .estimated-fees p {
                display: flex;
                justify-content: space-between;
                text-align: justify;
                width: 100%;
            }

            .estimated-fees p span:first-child {
                flex: 1;
                text-align: left;
            }

            .estimated-fees p span:last-child {
                text-align: right;
            }

            .total-price {
                text-align: right;
                width: 100%;
                font-weight: bold;
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

            .btn-group {
                flex-direction: column;
                align-items: center;
                display: flex;
                justify-content: center;
            }
            
            .btn-custom {
                width: 90%;
                padding: 8px;
                font-size: 14px;
            }

            .modal-dialog {
                max-width: 100%;
            }

            .destination-card {
                max-width: 100%;
            }

            .summary-container {
                text-align: left; 
                align-items: flex-start;
                margin-top: 20px;
            }

            .circle {
                width: 20px;
                height: 20px;
                font-size: 12px;
            }

            .estimated-fees {
                display: flex;
                flex-direction: column;
                width: 100%;
            }

            .estimated-fees p {
                display: flex;
                justify-content: space-between;
                width: 100%;
            }

            .estimated-fees span {
                margin-left: 120px;
                text-align: right;
            }

            .total-price {
                text-align: right;
                width: 100%;
                font-weight: bold;
            }

            .tour-status {
                text-align: center;
            }
        }

        @media (max-width: 360px) {
            .btn-custom {
                font-size: 13px;
                padding: 6px;
            }

            .info-box {
                min-width: 100%;
                padding: 10px;
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
                <a href="monthlyperformance.php" class="nav-link">
                    <i class="bi bi-bar-chart-line"></i>
                    <span class="d-none d-sm-inline">Monthly Performance</span>
                </a>
            </li>
            <li class="nav-item mb-4">
                <a href="" class="nav-link active">
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
            <h2>Tour History</h2>
            <span class="date">
                <h2>
                    <?php 
                        date_default_timezone_set('Asia/Manila');
                        echo date('M d, Y | h:i A'); 
                    ?>
                </h2>
            </span>
        </div>

        <div class="btn-group" role="group">
            <button type="button" id="completed-btn" class="btn-custom active" onclick="showCompleted()">Completed</button>
            <button type="button" id="cancelled-btn" class="btn-custom" onclick="showCancelled()">Cancelled</button>
        </div>

        <div class="row mt-3 d-flex justify-content-center">
            <div class="col-lg-12 col-md-12 col-12 mb-3" id="completed-tours">
                <?php if (count($completed_tours) > 0): ?>
                <div class="info-box">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Submitted On</th>
                                    <th>Destinations</th>
                                    <th>Travel Date</th>
                                    <th>Pax</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($completed_tours as $tourid => $tourGroup): 
                                    $firstTour = $tourGroup[0];
                                    $destinationCount = count($tourGroup);
                                    $totalPrice = array_sum(array_column($tourGroup, 'price')) * $firstTour['companions'];
                                    $tourData = htmlspecialchars(json_encode($tourGroup)); 
                                ?>
                                    <tr onclick="showModal(<?php echo $tourData; ?>)" style="cursor: pointer;">
                                        <td><?php echo htmlspecialchars($firstTour['name']); ?></td>
                                        <td><?php echo date('d M Y', strtotime($firstTour['submitted_on'])); ?></td>
                                        <td><?php echo $destinationCount; ?></td>
                                        <td><?php echo date('d M Y', strtotime($firstTour['travel_date'])); ?></td>
                                        <td><?php echo $firstTour['companions']; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php else: ?>
                    <div class="no-completed-tours text-center">No completed tours found.</div>
                <?php endif; ?>
            </div>
        </div>
        <div class="row mt-3 d-flex justify-content-center">
            <div class="col-lg-12 col-md-12 col-12 mb-3" id="cancelled-tours" style="display: none;">
                <?php if (count($cancelled_tours) > 0): ?>
                <div class="info-box">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Submitted On</th>
                                    <th>Destinations</th>
                                    <th>Travel Date</th>
                                    <th>Pax</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($cancelled_tours as $tourid => $tourGroup): 
                                    $firstTour = $tourGroup[0];
                                    $destinationCount = count($tourGroup);
                                    $tourData = htmlspecialchars(json_encode($tourGroup)); 
                                ?>
                                    <tr onclick="showModal(<?php echo $tourData; ?>)" style="cursor: pointer; background-color: #E7EBEE;">
                                        <td><?php echo htmlspecialchars($firstTour['name']); ?></td>
                                        <td><?php echo date('d M Y', strtotime($firstTour['submitted_on'])); ?></td>
                                        <td><?php echo $destinationCount; ?></td>
                                        <td><?php echo date('d M Y', strtotime($firstTour['travel_date'])); ?></td>
                                        <td><?php echo $firstTour['companions']; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php else: ?>
                    <div class="no-cancelled-tours text-center">No cancelled tours found.</div>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>

<div class="modal fade" id="tourHistoryModal" tabindex="-1" aria-labelledby="tourHistoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content p-4">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold" id="tourHistoryModalLabel">Tour Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body d-flex">
                <div class="d-flex">
                    <div class="stepper" id="stepper-container"></div>
                    <div class="destination-container" id="destination-container"></div>
                </div>

                <div class="summary-container">
                    <p><strong>Date Created</strong><br><span id="date-created"></span></p>
                    <p><strong>Number of People</strong><br><span id="num-people"></span></p>
                    <p><strong>Estimated Fees</strong></p>
                    <div class="estimated-fees" id="estimated-fees"></div>
                    <p class="total-price"><strong>₱ <span id="total-price"></span></strong></p>
                </div>
            </div>
            
            <p class="tour-status" id="tour-status"></p>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/js/all.min.js"></script>
<script src="../../../public/assets/scripts/main.js"></script>
<script src="../../../public/assets/scripts/admtourhistory.js"></script>
</body>
</html>