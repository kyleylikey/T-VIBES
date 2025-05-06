<?php
include '../../../includes/auth.php';
require_once '../../config/dbconnect.php';
require_once '../../controllers/employee/tourrequestscontroller.php';

$userid = $_SESSION['userid'];
$query = "SELECT TOP 1 name FROM [taaltourismdb].[users] WHERE userid = :userid";
$stmt = $conn->prepare($query);
$stmt->bindParam(':userid', $userid);
$stmt->execute();
$employee = $stmt->fetch(PDO::FETCH_ASSOC);

$employeeName = $employee ? htmlspecialchars($employee['name']) : "Employee";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Dashboard - Tour Requests</title>
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
            background-color: white;
            z-index: 1000;
            transition: all 0.3s ease-in-out;
        }

        .sidebar img {
            max-width: 100%; 
            height: auto;
            display: block; 
            margin: auto;
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
        
        .employee-name.active {
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

        .content-container h2, 
        .content-container .date h2 {
            color: #102E47;
            font-weight: bold;
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
            table-layout: fixed;
        }

        thead th {
            color: #434343;
            font-weight: bold;
            padding-bottom: 10px;
            background: none !important;
            border: none !important;
            box-shadow: none !important;
            vertical-align: middle;
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
            vertical-align: middle;
        }

        tbody tr td:first-child {
            border-top-left-radius: 15px;
            border-bottom-left-radius: 15px;
        }

        tbody tr td:last-child {
            border-top-right-radius: 15px;
            border-bottom-right-radius: 15px;
        }

        .table th,
        .table td {
            white-space: nowrap; 
            overflow: hidden;
            text-overflow: ellipsis; 
        }

        .table th:nth-child(1), 
        .table td:nth-child(1) {
            width: 5%; 
        }

        .table th:nth-child(2), 
        .table td:nth-child(2) {
            width: 20%; 
        }

        .table th:nth-child(3), 
        .table td:nth-child(3) {
            width: 15%; 
        }

        .table th:nth-child(4), 
        .table td:nth-child(4) {
            width: 10%; 
        }

        .table th:nth-child(5), 
        .table td:nth-child(5) {
            width: 15%; 
        }

        .table th:nth-child(6), 
        .table td:nth-child(6) {
            width: 5%; 
        }

        .table th:nth-child(7), 
        .table td:nth-child(7) {
            width: 30%; 
        }

        .btn-action {
            display: inline-block;
            width: 90px; 
            margin: 3px;
            padding: 8px 0;
            text-align: center;
            font-size: 14px;
            border-radius: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
            background-color: #FFFFFF;
            font-weight: bold;
            font-family: 'Nunito', sans-serif !important;
        }

        .btn-view {
            border: 2px solid #102E47;
            color: #102E47;
        }

        .btn-view:hover {
            background-color: #102E47;
            color: #FFFFFF;
        }

        .btn-display {
            border: 2px solid #28a745;
            color: #28a745;
        }

        .btn-display:hover {
            background-color: #28a745;
            color: #FFFFFF;
        }

        .btn-archive {
            border: 2px solid #EC6350;
            color: #EC6350;
        }

        .btn-archive:hover {
            background-color: #EC6350;
            color: #FFFFFF;
        }

        .btn-group {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 15px;
        }

        .filter-search-container {
            display: flex;
            align-items: center;
            margin-left: auto;
            gap: 10px;
        }

        .btn-filter {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: white;
            border: 2px solid #102E47;
            color: #434343;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-filter:hover {
            background-color: #102E47;
            color: white;
        }

        .search-container {
            position: relative;
            width: 250px;
        }

        .search-input {
            width: 100%;
            padding: 10px 40px 10px 15px;
            border: 2px solid #102E47;
            border-radius: 25px;
            font-family: 'Nunito', sans-serif;
            font-size: 14px;
            color: #434343;
        }

        .search-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #434343;
            pointer-events: none;
        }

        .modal-title {
            font-size: 24px;
            font-weight: bold;
            color: #102E47;
            font-family: 'Raleway', sans-serif;
        }

        .stepper {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
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
            height: 120px;
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
            margin-bottom: 10px;
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

        .image-placeholder img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 8px;
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
            align-items: flex-start;
            gap: 20px;
        }

        .d-flex {
            display: flex;
            gap: 15px;
        }

        .destination-container {
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
        }

        .modal-dialog {
            max-width: 50%;
        }

        .modal-content {
            background-color: #E7EBEE;
            border-radius: 25px;
            min-height: 50%;
            font-family: 'Nunito', sans-serif !important;
        }

        .summary-container {
            flex-grow: 1;
            min-height: 250px;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
        }

        .summary-container p:nth-child(1),
        .summary-container p:nth-child(2),
        .summary-container p:nth-child(3) {
            color: #102E47;
        }

        .summary-container p:nth-child(3) {
            margin-bottom: 5px;
        }

        .summary-container p:nth-child(1) span,
        .summary-container p:nth-child(2) span {
            color: #757575;
        }

        .estimated-fees {
            display: flex;
            flex-direction: column;
            gap: 2px;
            margin-bottom: 10px;
        }

        .estimated-fees p {
            display: flex;
            justify-content: space-between;
            width: 100%;
            margin-bottom: 3px;
            color: #757575 !important;
        }

        .total-price {
            font-size: 16px;
            font-weight: bold;
            text-align: right;
            color: #434343;
            margin-top: 10px;
        }

        .modal-footer {
            display: flex;
            justify-content: center;
            padding: 15px;
            border-top: none;
        }

        .modal-footer p {
            color: #757575;
            text-align: center;
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
        }

        .btn-custom:hover {
            background-color: #102E47;
            border-color: #102E47;
            color: #FFFFFF;
            font-weight: bold;
        }

        .modal-sm-custom {
            max-width: 35%; 
        }

        .form-control {
            resize: none; 
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

        .no-filter-search {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            color: #434343;
            padding: 10px;
            border-radius: 8px;
            width: fit-content;
            margin: 20px auto;
            font-family: 'Nunito', sans-serif !important;
            margin-bottom: 30px !important;
        }

        @media (max-width: 1280px) {
            .modal-sm-custom {
                max-width: 50%;
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

            .table {
                table-layout: auto;
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
            }

            .nav-link {
                font-size: 14px; 
                padding: 8px; 
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

            .employee-name.active {
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

            .modal-sm-custom {
                max-width: 65%;
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
                width: 80px;
                padding: 10px;
            }

            .sidebar img {
                max-width: 60px; 
            }

            .main-content {
                margin-left: 90px; 
                width: calc(100% - 90px); 
            }

            .nav-link {
                text-align: center;
                padding: 10px;
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

            .employee-name.active {
                background-color: #102E47 !important;
                color: #FFFFFF !important;
                font-weight: bold;
            }

            .sign-out.active {
                background-color: #E7EBEE !important;
                color: #102E47 !important;
                font-weight: bold;
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
                text-align: center;
                align-items: center;
                margin: 0 auto; 
            }

            .info-box i {
                align-self: center;
            }

            .table-responsive {
                overflow-x: auto; 
            }

            table {
                font-size: 14px; 
            }

            thead th, tbody td {
                white-space: nowrap; 
                padding: 5px; 
            }

            .modal-sm-custom {
                max-width: 70%;
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

            .btn-custom {
                width: 100%;
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

            .estimated-fees span {
                margin-left: 120px;
                text-align: right;
            }

            .total-price {
                text-align: right;
                width: 100%;
                font-weight: bold;
            }

            .filter-search-container {
                width: 100%;
                margin-top: 10px;
                justify-content: center;
            }
            
            .search-container {
                width: 100%;
            }
        }

        @media (max-width: 600px) {
            .circle {
                border: 2px solid #102E47;
            }
            .dashed-line {
                border-left: 2px dashed #102E47;
            }
            .sidebar {
                width: 70px;
                padding: 5px;
            }

            .main-content {
                margin-left: 75px;
                width: calc(100% - 75px);
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

            .employee-name.active {
                background-color: #102E47 !important;
                color: #FFFFFF !important;
                font-weight: bold;
            }

            .sign-out.active {
                background-color: #E7EBEE !important;
                color: #102E47 !important;
                font-weight: bold;
            }

            .info-box {
                min-height: 120px;
                min-width: auto;
                padding: 10px;
            }

            .info-box i {
                font-size: 30px;
            }

            table {
                font-size: 12px;
                overflow-x: auto;
            }

            tbody tr td {
                padding: 5px; 
            }

            .modal-sm-custom {
                max-width: 100%;
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

            .btn-custom {
                width: 100%;
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
        }

        @media (max-width: 360px) {
            .estimated-fees span {
                margin-left: 70px;
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
                        <span class="d-none d-sm-inline">Dashboard</span>
                    </a>
                </li>
                <li class="nav-item mb-4">
                    <a href="" class="nav-link active">
                        <i class="bi bi-map"></i>
                        <span class="d-none d-sm-inline">Tour Requests</span>
                    </a>
                </li>
                <li class="nav-item mb-4">
                    <a href="upcomingtours.php" class="nav-link">
                        <i class="bi bi-geo"></i>
                        <span class="d-none d-sm-inline">Upcoming Tours</span>
                    </a>
                </li>
                <li class="nav-item mb-4">
                    <a href="reviews.php" class="nav-link">
                        <i class="bi bi-pencil-square"></i>
                        <span class="d-none d-sm-inline">Reviews</span>
                    </a>
                </li>
                <li class="nav-item mb-4">
                    <a href="touristsites.php" class="nav-link">
                        <i class="bi bi-image"></i>
                        <span class="d-none d-sm-inline">Tourist Sites</span>
                    </a>
                </li>
            </ul>
        </div>
        
        <ul class="nav nav-pills flex-column mb-4">
            <li class="nav-item mb-3">
                <a href="javascript:void(0);" class="nav-link employee-name active">
                    <i class="bi bi-person-circle"></i>
                    <span class="d-none d-sm-inline"><?= $employeeName; ?></span>
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
                <h2>Tour Requests</h2>
                <span class="date">
                    <h2>
                        <?php
                        date_default_timezone_set('Asia/Manila');
                        echo date('M d, Y | h:i A');
                        ?>
                    </h2>
                </span>
            </div>

            <div class="btn-group mt-2 mb-4" role="group">
                <div class="filter-search-container">
                    <button type="button" class="btn-filter" id="sortButton">
                        <i class="fas fa-filter"></i>
                    </button>
                    <div class="search-container">
                        <input type="text" class="search-input" id="searchInput" placeholder="Search">
                        <i class="fas fa-search search-icon"></i>
                    </div>
                </div>
            </div>

            <div class="row mt-3 d-flex justify-content-center">
                <div class="col-lg-12 col-md-12 col-12 mb-3">
                    <div class="info-box">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Submitted On</th>
                                        <th>Destinations</th>
                                        <th>Travel Date</th>
                                        <th>Pax</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(!empty($requests)) {
                                        $rowNumber = 1;
                                        foreach ($requests as $request) {
                                            echo "<tr>";
                                            echo "<td>".$rowNumber."</td>";
                                            echo "<td>".$request['name']."</td>";
                                            echo "<td>".date('d M Y', strtotime($request['created_at']))."</td>";
                                            echo "<td>".$request['total_sites']."</td>";
                                            echo "<td>".date('d M Y', strtotime($request['date']))."</td>"; 
                                            echo "<td>".$request['companions']."</td>";
                                            echo "<td>";
                                            echo "<button class='btn-action btn-view' onclick='showModal(this)' 
                                                data-tourid='".htmlspecialchars($request['tourid'])."' 
                                                data-userid='".htmlspecialchars($request['userid'])."'>
                                                <i class='fas fa-eye'></i> View
                                            </button>";
                                            echo "<button class='btn-action btn-display' onclick='acceptRequest(this)' 
                                                data-tourid='".htmlspecialchars($request['tourid'])."' 
                                                data-userid='".htmlspecialchars($request['userid'])."'>
                                                <i class='fas fa-check'></i> Accept
                                            </button>";
                                            echo "<button class='btn-action btn-archive' onclick='declineRequest(this)' 
                                                data-tourid='".htmlspecialchars($request['tourid'])."' 
                                                data-userid='".htmlspecialchars($request['userid'])."'>
                                                <i class='fas fa-times'></i> Decline
                                            </button>";
                                            echo "</td>";
                                            echo "</tr>";
                                            $rowNumber++;
                                        }
                                    } 
                                    else {
                                        echo "<tr><td colspan='7' class='text-center'>No requests found.</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <div class="modal fade" id="tourRequestModal" tabindex="-1" aria-labelledby="tourRequestModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content p-4">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold" id="tourRequestModalLabel">Tour Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body d-flex">
                    <div class="d-flex">
                        <div class="stepper">
                            
                        </div>
                        <div class="destination-container d-flex">
                            
                        </div>
                    </div>

                    <div class="summary-container">
                        <p><strong>Date Created</strong><br><span id="dateCreated">DD M YYYY</span></p>
                        <p><strong>Number of People</strong><br><span id="numberOfPeople">2</span></p>
                        <p><strong>Estimated Fees</strong></p>
                        <div class="estimated-fees">
                            
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <p>*Fee is only an estimate and subject to change if the destination can accommodate special discounts.</p>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="cancelReasonModal" tabindex="-1" aria-labelledby="cancelReasonLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm-custom">
            <div class="modal-content swal-custom-popup text-center">
                <div class="modal-header border-0">
                    <h5 class="modal-title swal2-title-custom w-100" id="cancelReasonLabel">Reason for Cancellation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <textarea id="cancelReasonInput" class="form-control" rows="4" placeholder="Type here..."></textarea>
                </div>
                <div class="modal-footer justify-content-center">
                    <button id="submitCancelReason" class="swal-custom-btn">Submit</button>
                </div>
            </div>
        </div>
    </div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/js/all.min.js"></script>
<script src="../../../public/assets/scripts/main.js"></script>
<script>
function acceptRequest(button) {
    const tourid = button.getAttribute("data-tourid");
    const userid = button.getAttribute("data-userid");

    Swal.fire({
        iconHtml: '<i class="fas fa-thumbs-up"></i>',
        title: "Accept Tour Request?",
        showCancelButton: true,
        confirmButtonText: "Yes",
        cancelButtonText: "No",
        customClass: {
            title: "swal2-title-custom",
            icon: "swal2-icon-custom",
            popup: "swal-custom-popup",
            confirmButton: "swal-custom-btn",
            cancelButton: "swal-custom-btn"
        }
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Processing Request',
                html: 'Sending confirmation email... Do not close this window.',
                allowOutsideClick: false,
                customClass: {
                    title: "swal2-title-custom",
                    icon: "swal2-icon-custom",
                    popup: "swal-custom-popup"
                },
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            fetch("/T-VIBES/src/controllers/employee/tourrequestscontroller.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({ action: "accept", tourid: tourid, userid: userid }),
            })
            .then(response => response.text()) 
            .then(text => {
                console.log("Raw Response:", text);  
                return JSON.parse(text); 
            })
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        iconHtml: '<i class="fas fa-circle-check"></i>',
                        title: "Successfully Accepted Tour Request!",
                        timer: 3000,
                        showConfirmButton: false,
                        customClass: {
                            title: "swal2-title-custom",
                            icon: "swal2-icon-custom",
                            popup: "swal-custom-popup"
                        }
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        iconHtml: '<i class="fas fa-exclamation-circle"></i>',
                        title: "Failed to Accept Tour Request. Please try again.",
                        timer: 3000,
                        showConfirmButton: false,
                        customClass: {
                            title: "swal2-title-custom",
                            icon: "swal2-icon-custom",
                            popup: "swal-custom-popup"
                        }
                    });
                }
            })
            .catch(error => console.error("Error:", error));
        }
    });
}

function declineRequest(button) {
    const tourid = button.getAttribute("data-tourid");
    const userid = button.getAttribute("data-userid");

    Swal.fire({
        iconHtml: '<i class="fas fa-thumbs-down"></i>',
        title: "Decline Tour Request?",
        showCancelButton: true,
        confirmButtonText: "Yes",
        cancelButtonText: "No",
        customClass: {
            title: "swal2-title-custom",
            icon: "swal2-icon-custom",
            popup: "swal-custom-popup",
            confirmButton: "swal-custom-btn",
            cancelButton: "swal-custom-btn"
        }
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById("cancelReasonModal").setAttribute("data-tourid", tourid);
            document.getElementById("cancelReasonModal").setAttribute("data-userid", userid);
            
            var cancelModal = new bootstrap.Modal(document.getElementById("cancelReasonModal"));
            cancelModal.show();
        }
    });

    document.getElementById("submitCancelReason").addEventListener("click", function () {
        let reason = document.getElementById("cancelReasonInput").value.trim();

        const tourid = document.getElementById("cancelReasonModal").getAttribute("data-tourid");
        const userid = document.getElementById("cancelReasonModal").getAttribute("data-userid");

        if (reason.trim() === "") {
            Swal.fire({
                iconHtml: '<i class="fas fa-exclamation-circle"></i>',
                title: "Please enter a reason!",
                timer: 3000,
                showConfirmButton: false,
                customClass: {
                    title: "swal2-title-custom",
                    icon: "swal2-icon-custom",
                    popup: "swal-custom-popup"
                }
            });
            return;
        }
        Swal.fire({
            title: 'Processing Request',
            html: 'Sending decline notification... Do not close this window.',
            allowOutsideClick: false,
            customClass: {
                title: "swal2-title-custom",
                icon: "swal2-icon-custom",
                popup: "swal-custom-popup"
            },
            didOpen: () => {
                Swal.showLoading();
            }
        });
        fetch("/T-VIBES/src/controllers/employee/tourrequestscontroller.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify({ action: "decline", tourid: tourid, userid: userid, reason: reason }),
        })
        .then(response => response.text()) 
        .then(text => {
            console.log("Raw Response:", text);  
            return JSON.parse(text); 
        })
        .then(data => {
            if (data.success) {
                Swal.fire({
                    iconHtml: '<i class="fas fa-circle-check"></i>',
                    title: "Successfully Declined Tour Request.",
                    timer: 3000,
                    showConfirmButton: false,
                    customClass: {
                        title: "swal2-title-custom",
                        icon: "swal2-icon-custom",
                        popup: "swal-custom-popup"
                    }
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire({
                    iconHtml: '<i class="fas fa-exclamation-circle"></i>',
                    title: "Failed to Decline Tour Request. Please try again.",
                    timer: 3000,
                    showConfirmButton: false,
                    customClass: {
                        title: "swal2-title-custom",
                        icon: "swal2-icon-custom",
                        popup: "swal-custom-popup"
                    }
                });
            }
        })

        var cancelModal = bootstrap.Modal.getInstance(document.getElementById("cancelReasonModal"));
        cancelModal.hide();

    });
}

const formatDateTime = (dateTimeString) => {
    const dateObj = new Date(dateTimeString);
    const dateOptions = { day: '2-digit', month: 'short', year: 'numeric' };
    return dateObj.toLocaleDateString('en-GB', dateOptions);
};

function showModal(button) {
    var tourid = button.getAttribute('data-tourid');
    var userid = button.getAttribute('data-userid');

    fetch('/T-VIBES/src/controllers/employee/tourrequestscontroller.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ tourid: tourid, userid: userid })
    })
    .then(response => response.text()) 
    .then(text => {
        console.log("Raw Response:", text);  
        return JSON.parse(text); 
    })
    .then(data => {
        document.getElementById('tourRequestModalLabel').innerText = 'Tour Request of ' + data.name;
        
        document.getElementById('dateCreated').innerText = formatDateTime(data.created_at);
        document.getElementById('numberOfPeople').innerText = data.companions;

        const destinationContainer = document.querySelector('.destination-container');
        destinationContainer.innerHTML = '';

        const stepper = document.querySelector('.stepper');
        stepper.innerHTML = '';

        const estimatedFeesContainer = document.querySelector('.estimated-fees');
        estimatedFeesContainer.innerHTML = '';

        let totalPrice = 0; 
        const pax = parseInt(data.companions) || 1; 

        if (data.sites && data.sites.length > 0) {
            data.sites.forEach((site, index) => {
                const step = document.createElement('div');
                step.classList.add('step');
                step.innerHTML = `
                    <div class="circle">${index + 1}</div>
                    ${index < data.sites.length - 1 ? '<div class="dashed-line"></div>' : ''}`
                ;
                stepper.appendChild(step);

                const formatDate = (dateString) => {
                    const options = { day: '2-digit', month: 'short', year: 'numeric' };
                    return new Date(dateString).toLocaleDateString('en-GB', options);
                };

                const card = document.createElement('div');
                card.classList.add('destination-card');
                card.innerHTML = `
                    <div class="image-placeholder">
                        <img src="/T-VIBES/public/uploads/${site.siteimage}"></img>
                    </div>
                    <div class="destination-info">
                        <h6>${site.sitename}</h6>
                        <p style="color: #757575; font-size: 16px;">
                            <i class="bi bi-calendar"></i> ${formatDate(data.date)}
                        </p>
                    </div>`;
                destinationContainer.appendChild(card);

                totalPrice += parseFloat(site.price);
                
                const feeItem = document.createElement('p');
                feeItem.innerHTML = `${site.sitename} <span style="float: right;">₱${parseFloat(site.price).toFixed(2)}</span>`;
                estimatedFeesContainer.appendChild(feeItem);
            });

            const subtotalItem = document.createElement('p');
            subtotalItem.style.display = 'flex';
            subtotalItem.style.justifyContent = 'flex-end';
            subtotalItem.innerHTML = `<strong>₱${totalPrice.toFixed(2)}</strong>`;
            estimatedFeesContainer.appendChild(subtotalItem);

            const totalFeesTitle = document.createElement('p');
            totalFeesTitle.innerHTML = `<strong style="color: #102E47;">Total Fees:</strong>`;
            totalFeesTitle.style.marginTop = '15px';
            estimatedFeesContainer.appendChild(totalFeesTitle);

            const totalCalcItem = document.createElement('p');
            totalCalcItem.innerHTML = `₱${totalPrice.toFixed(2)} x ${pax} <span style="float: right; color: #EC6350;"><strong>₱${(totalPrice * pax).toFixed(2)}</strong></span>`;
            estimatedFeesContainer.appendChild(totalCalcItem);

        } else {
            stepper.innerHTML = "<p>No destinations found.</p>";
            estimatedFeesContainer.innerHTML = "<p>No fees available.</p>";
            document.querySelector('.total-price').innerHTML = `<strong id="estimatedFees">₱0.00</strong>`;
        }

        var modal = new bootstrap.Modal(document.getElementById('tourRequestModal'));
        modal.show();
    })
    .catch(error => console.error('Error:', error));
}

document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase().trim();
        const tableRows = document.querySelectorAll('tbody tr');
        const tableHeader = document.querySelector('thead');
        
        let visibleRowCount = 0;
        
        tableRows.forEach(row => {
            let matchFound = false;
            if (row.cells.length >= 7) {
                const rowNumber = row.cells[0].textContent.toLowerCase();
                const name = row.cells[1].textContent.toLowerCase();
                const submittedDate = row.cells[2].textContent.toLowerCase();
                const destinations = row.cells[3].textContent.toLowerCase();
                const travelDate = row.cells[4].textContent.toLowerCase();
                const pax = row.cells[5].textContent.toLowerCase();
                
                if (rowNumber.includes(searchTerm) ||
                    name.includes(searchTerm) ||
                    submittedDate.includes(searchTerm) ||
                    destinations.includes(searchTerm) ||
                    travelDate.includes(searchTerm) ||
                    pax.includes(searchTerm)) {
                    matchFound = true;
                }
            }
            
            if (matchFound) {
                row.style.display = '';
                visibleRowCount++;
            } else {
                row.style.display = 'none';
            }
        });
        
        if (tableHeader) {
            tableHeader.style.display = visibleRowCount > 0 ? '' : 'none';
        }
        
        const noToursMessage = document.querySelector('.no-filter-search');
        if (noToursMessage) {
            if (visibleRowCount === 0) {
                noToursMessage.textContent = 'No matching requests found.';
                noToursMessage.style.display = '';
            } else {
                noToursMessage.style.display = 'none';
            }
        } else if (visibleRowCount === 0) {
            const tableContainer = document.querySelector('.table-responsive');
            if (tableContainer) {
                const noResults = document.createElement('div');
                noResults.className = 'no-filter-search';
                noResults.textContent = 'No matching requests found.';
                noResults.style.textAlign = 'center';
                noResults.style.padding = '20px';
                tableContainer.parentNode.appendChild(noResults);
            }
        }
    });
    
    const searchContainer = document.querySelector('.search-container');
    if (searchContainer) {
        const clearButton = document.createElement('i');
        clearButton.className = 'fas fa-times clear-search';
        clearButton.style.display = 'none';
        clearButton.style.position = 'absolute';
        clearButton.style.right = '30px';
        clearButton.style.top = '50%';
        clearButton.style.transform = 'translateY(-50%)';
        clearButton.style.cursor = 'pointer';
        clearButton.style.color = '#666';

        searchContainer.appendChild(clearButton);
        searchInput.addEventListener('input', function() {
            clearButton.style.display = this.value ? 'block' : 'none';
        });
        clearButton.addEventListener('click', function() {
            searchInput.value = '';
            searchInput.dispatchEvent(new Event('input'));
            this.style.display = 'none';
        });
    }
});

document.addEventListener('DOMContentLoaded', function() {
    const sortButton = document.getElementById('sortButton');
    
    const modalOverlay = document.createElement('div');
    modalOverlay.className = 'modal-overlay';
    modalOverlay.id = 'modalOverlay';
    modalOverlay.style.display = 'none';
    modalOverlay.style.position = 'fixed';
    modalOverlay.style.top = '0';
    modalOverlay.style.left = '0';
    modalOverlay.style.width = '100%';
    modalOverlay.style.height = '100%';
    modalOverlay.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
    modalOverlay.style.zIndex = '9999';
    document.body.appendChild(modalOverlay);
    
    const filterModal = document.createElement('div');
    filterModal.className = 'filter-modal';
    filterModal.id = 'filterModal';
    filterModal.style.display = 'none';
    filterModal.style.position = 'fixed';
    filterModal.style.backgroundColor = '#E7EBEE';
    filterModal.style.border = '1px solid #ddd';
    filterModal.style.borderRadius = '25px';
    filterModal.style.padding = '30px';
    filterModal.style.boxShadow = '0 4px 8px rgba(0, 0, 0, 0.1)';
    filterModal.style.zIndex = '10000';
    filterModal.style.width = '350px'; 
    filterModal.style.maxWidth = '90%'; 
    filterModal.style.top = '50%';
    filterModal.style.left = '50%';
    filterModal.style.transform = 'translate(-50%, -50%)';
    filterModal.style.maxHeight = '100vh'; 
    filterModal.innerHTML = `
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
            <h4 style="margin: 0; color: #102E47; font-family: 'Raleway', sans-serif !important; font-weight: bold;">Filter Requests</h4>
            <button id="closeModal" style="background: none; border: none; cursor: pointer; font-size: 18px; color: #102E47;">&times;</button>
        </div>
        <div style="margin-bottom: 15px;">
            <label style="display: block; margin-bottom: 5px; font-weight: bold; color: #434343; font-family: 'Nunito', sans-serif !important;">Submission Date Range:</label>
            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                <div style="flex: 1; min-width: 120px;">
                    <label for="startDate" style="display: block; margin-bottom: 3px; font-weight: bold; color: #757575; font-family: 'Nunito', sans-serif !important;">From:</label>
                    <input type="date" id="startDate" class="filter-input" style="padding: 5px; border: 1px solid #ccc; border-radius: 4px; width: 100%;">
                </div>
                <div style="flex: 1; min-width: 120px;">
                    <label for="endDate" style="display: block; margin-bottom: 3px; font-weight: bold; color: #757575; font-family: 'Nunito', sans-serif !important;">To:</label>
                    <input type="date" id="endDate" class="filter-input" style="padding: 5px; border: 1px solid #ccc; border-radius: 4px; width: 100%;">
                </div>
            </div>
        </div>
        <div style="margin-bottom: 15px;">
            <label style="display: block; margin-bottom: 5px; font-weight: bold; color: #434343; font-family: 'Nunito', sans-serif !important;">Travel Date Range:</label>
            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                <div style="flex: 1; min-width: 120px;">
                    <label for="travelStartDate" style="display: block; margin-bottom: 3px; font-weight: bold; color: #757575; font-family: 'Nunito', sans-serif !important;">From:</label>
                    <input type="date" id="travelStartDate" class="filter-input" style="padding: 5px; border: 1px solid #ccc; border-radius: 4px; width: 100%;">
                </div>
                <div style="flex: 1; min-width: 120px;">
                    <label for="travelEndDate" style="display: block; margin-bottom: 3px; font-weight: bold; color: #757575; font-family: 'Nunito', sans-serif !important;">To:</label>
                    <input type="date" id="travelEndDate" class="filter-input" style="padding: 5px; border: 1px solid #ccc; border-radius: 4px; width: 100%;">
                </div>
            </div>
        </div>
        <div style="margin-bottom: 15px;">
            <label style="display: block; margin-bottom: 5px; font-weight: bold; color: #434343; font-family: 'Nunito', sans-serif !important;">Number of Destinations:</label>
            <div style="display: flex; gap: 10px;">
                <div style="flex: 1;">
                    <label for="minDestinations" style="display: block; margin-bottom: 3px; font-weight: bold; color: #757575; font-family: 'Nunito', sans-serif !important;">Min:</label>
                    <input type="number" id="minDestinations" min="1" class="filter-input" style="padding: 5px; border: 1px solid #ccc; border-radius: 4px; width: 100%;">
                </div>
                <div style="flex: 1;">
                    <label for="maxDestinations" style="display: block; margin-bottom: 3px; font-weight: bold; color: #757575; font-family: 'Nunito', sans-serif !important;">Max:</label>
                    <input type="number" id="maxDestinations" min="1" class="filter-input" style="padding: 5px; border: 1px solid #ccc; border-radius: 4px; width: 100%;">
                </div>
            </div>
        </div>
        <div style="margin-bottom: 15px;">
            <label style="display: block; margin-bottom: 5px; font-weight: bold; color: #434343; font-family: 'Nunito', sans-serif !important;">Number of Pax:</label>
            <div style="display: flex; gap: 10px;">
                <div style="flex: 1;">
                    <label for="minPax" style="display: block; margin-bottom: 3px; font-weight: bold; color: #757575; font-family: 'Nunito', sans-serif !important;">Min:</label>
                    <input type="number" id="minPax" min="1" class="filter-input" style="padding: 5px; border: 1px solid #ccc; border-radius: 4px; width: 100%;">
                </div>
                <div style="flex: 1;">
                    <label for="maxPax" style="display: block; margin-bottom: 3px; font-weight: bold; color: #757575; font-family: 'Nunito', sans-serif !important;">Max:</label>
                    <input type="number" id="maxPax" min="1" class="filter-input" style="padding: 5px; border: 1px solid #ccc; border-radius: 4px; width: 100%;">
                </div>
            </div>
        </div>
        <div style="margin-bottom: 15px;">
            <label for="nameFilter" style="display: block; margin-bottom: 5px; font-weight: bold; color: #434343; font-family: 'Nunito', sans-serif !important;">Tourist Name:</label>
            <input type="text" id="nameFilter" placeholder="Enter Tourist Name" class="filter-input" style="padding: 5px; border: 1px solid #ccc; border-radius: 4px; width: 100%;">
        </div>
        <div style="display: flex; justify-content: space-between; margin-top: 20px;">
            <button id="clearFilters" class="filter-btn" style="font-weight: bold;">Clear All</button>
            <button id="applyFilters" class="filter-btn" style="font-weight: bold;">Apply</button>
        </div>
    `;
    document.querySelector('.filter-search-container').appendChild(filterModal);
    
    const style = document.createElement('style');
    style.textContent = `
        .modal-overlay {
            transition: opacity 0.3s ease;
            opacity: 0;
        }
        .modal-overlay.visible {
            opacity: 1;
        }
        .filter-modal {
            transition: opacity 0.3s ease, transform 0.3s ease;
            opacity: 0;
            border-radius: 25px;
            z-index: 1001; /* Higher than overlay */
        }
        .filter-modal.visible {
            opacity: 1;
        }
        .active-filter {
            background-color: #102E47 !important;
            color: white !important;
        }
        .filter-btn {
            font-family: Nunito, sans-serif !important;
            background-color: #FFFFFF;
            color: #434343;
            border: 1px solid #102E47;
            padding: 8px 15px;
            border-radius: 25px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: bold;
        }
        .filter-btn:hover {
            background-color: #102E47;
            color: #FFFFFF;
        }
        /* Improved input styles */
        .filter-input {
            padding: 8px !important;
            border: 1px solid #ccc !important;
            border-radius: 4px !important;
            width: 100% !important;
            box-sizing: border-box !important;
        }
        .filter-input:focus {
            border-color: #102E47 !important;
            outline: none !important;
            box-shadow: 0 0 3px rgba(16, 46, 71, 0.3) !important;
        }
        /* Mobile responsiveness improvements */
        @media (max-width: 768px) {
            .filter-modal {
                width: 90% !important;
                max-width: 350px;
                padding: 20px !important;
            }
            .filter-btn {
                padding: 8px 12px !important;
            }
        }
        /* Handle very small screens */
        @media (max-width: 400px) {
            .filter-modal {
                padding: 15px !important;
            }
            .filter-btn {
                padding: 6px 10px !important;
                font-size: 14px !important;
            }
        }
    `;
    document.head.appendChild(style);
    
    function openModal() {
        const modal = document.getElementById('filterModal');
        const overlay = document.getElementById('modalOverlay');
        
        overlay.style.display = 'block';
        modal.style.display = 'block';
        document.body.style.overflow = 'hidden'; 
        
        setTimeout(() => {
            modal.classList.add('visible');
            overlay.classList.add('visible');
        }, 10);
    }
    
    function closeModal() {
        const modal = document.getElementById('filterModal');
        const overlay = document.getElementById('modalOverlay');
        
        modal.classList.remove('visible');
        overlay.classList.remove('visible');
        document.body.style.overflow = ''; 
        
        setTimeout(() => {
            modal.style.display = 'none';
            overlay.style.display = 'none';
        }, 300);
    }
    
    sortButton.addEventListener('click', function(e) {
        e.stopPropagation();
        const modal = document.getElementById('filterModal');
        
        if (modal.style.display === 'none') {
            openModal();
        } else {
            closeModal();
        }
    });
    
    modalOverlay.addEventListener('click', function() {
        closeModal();
    });
    
    document.getElementById('closeModal').addEventListener('click', function() {
        closeModal();
    });
    
    document.getElementById('filterModal').addEventListener('click', function(e) {
        e.stopPropagation();
    });
    
    document.getElementById('filterModal').addEventListener('wheel', function(e) {
        e.stopPropagation();
    });
    
    document.getElementById('applyFilters').addEventListener('click', function() {
        applyFilters();
        closeModal();
        
        const hasActiveFilters = checkIfFiltersActive();
        if (hasActiveFilters) {
            sortButton.classList.add('active-filter');
        } else {
            sortButton.classList.remove('active-filter');
        }
    });
    
    document.getElementById('clearFilters').addEventListener('click', function() {
        clearFilters();
        applyFilters();
        sortButton.classList.remove('active-filter');
    });
    
    function checkIfFiltersActive() {
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;
        const travelStartDate = document.getElementById('travelStartDate').value;
        const travelEndDate = document.getElementById('travelEndDate').value;
        const minDestinations = document.getElementById('minDestinations').value;
        const maxDestinations = document.getElementById('maxDestinations').value;
        const minPax = document.getElementById('minPax').value;
        const maxPax = document.getElementById('maxPax').value;
        const nameFilter = document.getElementById('nameFilter').value.trim();
        
        return startDate || endDate || travelStartDate || travelEndDate || 
               minDestinations || maxDestinations || minPax || maxPax || nameFilter;
    }
    
    function clearFilters() {
        document.getElementById('startDate').value = '';
        document.getElementById('endDate').value = '';
        document.getElementById('travelStartDate').value = '';
        document.getElementById('travelEndDate').value = '';
        document.getElementById('minDestinations').value = '';
        document.getElementById('maxDestinations').value = '';
        document.getElementById('minPax').value = '';
        document.getElementById('maxPax').value = '';
        document.getElementById('nameFilter').value = '';
    }
    
    function parseCustomDate(dateStr) {
        if (!dateStr) return null;
        
        const months = {
            'Jan': 0, 'Feb': 1, 'Mar': 2, 'Apr': 3, 'May': 4, 'Jun': 5,
            'Jul': 6, 'Aug': 7, 'Sep': 8, 'Oct': 9, 'Nov': 10, 'Dec': 11
        };
        
        const parts = dateStr.trim().split(' ');
        if (parts.length !== 3) return null;
        
        const day = parseInt(parts[0], 10);
        const month = months[parts[1]];
        const year = parseInt(parts[2], 10);
        
        if (isNaN(day) || month === undefined || isNaN(year)) return null;
        
        return new Date(year, month, day);
    }
    
    function applyFilters() {
        const startDate = document.getElementById('startDate').value ? new Date(document.getElementById('startDate').value) : null;
        const endDate = document.getElementById('endDate').value ? new Date(document.getElementById('endDate').value) : null;
        const travelStartDate = document.getElementById('travelStartDate').value ? new Date(document.getElementById('travelStartDate').value) : null;
        const travelEndDate = document.getElementById('travelEndDate').value ? new Date(document.getElementById('travelEndDate').value) : null;
        const minDestinations = document.getElementById('minDestinations').value ? parseInt(document.getElementById('minDestinations').value) : null;
        const maxDestinations = document.getElementById('maxDestinations').value ? parseInt(document.getElementById('maxDestinations').value) : null;
        const minPax = document.getElementById('minPax').value ? parseInt(document.getElementById('minPax').value) : null;
        const maxPax = document.getElementById('maxPax').value ? parseInt(document.getElementById('maxPax').value) : null;
        const nameFilter = document.getElementById('nameFilter').value.trim().toLowerCase();
        
        const tableRows = document.querySelectorAll('tbody tr');
        const tableHeader = document.querySelector('thead');
        let visibleRowCount = 0;
        
        tableRows.forEach(row => {
            let shouldShow = true;
            
            if (row.cells.length < 7) {
                row.style.display = '';
                return;
            }
            
            if (shouldShow && (startDate || endDate)) {
                const submittedDateCell = row.cells[2].textContent.trim();
                const submittedDate = parseCustomDate(submittedDateCell);
                
                if (submittedDate) {
                    if (startDate && submittedDate < startDate) {
                        shouldShow = false;
                    }
                    if (endDate && submittedDate > endDate) {
                        shouldShow = false;
                    }
                }
            }
            
            if (shouldShow && (travelStartDate || travelEndDate)) {
                const travelDateCell = row.cells[4].textContent.trim();
                const travelDate = parseCustomDate(travelDateCell);
                
                if (travelDate) {
                    if (travelStartDate && travelDate < travelStartDate) {
                        shouldShow = false;
                    }
                    if (travelEndDate && travelDate > travelEndDate) {
                        shouldShow = false;
                    }
                }
            }
            
            if (shouldShow && (minDestinations || maxDestinations)) {
                const numDestinations = parseInt(row.cells[3].textContent.trim());
                if (!isNaN(numDestinations)) {
                    if (minDestinations && numDestinations < minDestinations) {
                        shouldShow = false;
                    }
                    if (maxDestinations && numDestinations > maxDestinations) {
                        shouldShow = false;
                    }
                }
            }
            
            if (shouldShow && (minPax || maxPax)) {
                const pax = parseInt(row.cells[5].textContent.trim());
                if (!isNaN(pax)) {
                    if (minPax && pax < minPax) {
                        shouldShow = false;
                    }
                    if (maxPax && pax > maxPax) {
                        shouldShow = false;
                    }
                }
            }
            
            if (shouldShow && nameFilter) {
                const nameCell = row.cells[1].textContent.toLowerCase();
                if (!nameCell.includes(nameFilter)) {
                    shouldShow = false;
                }
            }
            
            if (shouldShow) {
                row.style.display = '';
                visibleRowCount++;
            } else {
                row.style.display = 'none';
            }
        });
        
        if (tableHeader) {
            tableHeader.style.display = visibleRowCount > 0 ? '' : 'none';
        }
        
        const noRequestsMessage = document.querySelector('.no-filter-search');
        if (visibleRowCount === 0) {
            if (noRequestsMessage) {
                noRequestsMessage.textContent = 'No requests match your filter criteria.';
                noRequestsMessage.style.display = '';
            } else {
                const tableContainer = document.querySelector('.table-responsive');
                if (tableContainer) {
                    const noResults = document.createElement('div');
                    noResults.className = 'no-filter-search';
                    noResults.textContent = 'No requests match your filter criteria.';
                    noResults.style.textAlign = 'center';
                    noResults.style.padding = '20px';
                    tableContainer.parentNode.appendChild(noResults);
                }
            }
        } else if (noRequestsMessage) {
            noRequestsMessage.style.display = 'none';
        }
    }
});

document.addEventListener('DOMContentLoaded', function() {
    const rowsPerPage = 10;
    let currentPage = 1;
    let filteredRows = [];
    
    function initPagination() {
        const tableRows = document.querySelectorAll('tbody tr');
        filteredRows = Array.from(tableRows).filter(row => row.style.display !== 'none');
        const totalPages = Math.ceil(filteredRows.length / rowsPerPage);
        
        const noRequestsMessage = document.querySelector('.no-filter-search');
        const noRequestsVisible = noRequestsMessage && noRequestsMessage.style.display !== 'none';
        
        if (!document.querySelector('.pagination-controls')) {
            const tableContainer = document.querySelector('.table-responsive');
            if (tableContainer) {
                const paginationControls = document.createElement('div');
                paginationControls.className = 'pagination-controls';
                paginationControls.style.display = noRequestsVisible ? 'none' : 'flex';
                paginationControls.style.justifyContent = 'center';
                paginationControls.style.alignItems = 'center';
                paginationControls.style.marginTop = '15px';
                paginationControls.style.userSelect = 'none';
                
                const prevBtn = document.createElement('button');
                prevBtn.innerHTML = '<strong>&lt;</strong>';
                prevBtn.className = 'pagination-btn';
                prevBtn.id = 'prevPage';
                prevBtn.style.backgroundColor = 'transparent';
                prevBtn.style.color = '#102E47';
                prevBtn.style.border = '1px solid #102E47';
                prevBtn.style.borderRadius = '50%';
                prevBtn.style.width = '32px';
                prevBtn.style.height = '32px';
                prevBtn.style.margin = '0 10px';
                prevBtn.style.cursor = 'pointer';
                prevBtn.style.display = 'flex';
                prevBtn.style.justifyContent = 'center';
                prevBtn.style.alignItems = 'center';
                prevBtn.style.fontSize = '16px';
                prevBtn.style.fontWeight = 'bold';
                prevBtn.style.transition = 'all 0.2s ease';
                
                prevBtn.addEventListener('mouseover', function() {
                    if (!this.disabled) {
                        this.style.backgroundColor = '#102E47';
                        this.style.color = '#FFFFFF';
                    }
                });
                
                prevBtn.addEventListener('mouseout', function() {
                    if (!this.disabled) {
                        this.style.backgroundColor = 'transparent';
                        this.style.color = '#102E47';
                    }
                });
                
                const pageInfo = document.createElement('div');
                pageInfo.id = 'pageInfo';
                pageInfo.style.margin = '0 15px';
                pageInfo.style.fontFamily = 'Nunito, sans-serif';
                pageInfo.style.color = '#434343';
                
                const nextBtn = document.createElement('button');
                nextBtn.innerHTML = '<strong>&gt;</strong>';
                nextBtn.className = 'pagination-btn';
                nextBtn.id = 'nextPage';
                nextBtn.style.backgroundColor = 'transparent';
                nextBtn.style.color = '#102E47';
                nextBtn.style.border = '1px solid #102E47';
                nextBtn.style.borderRadius = '50%';
                nextBtn.style.width = '32px';
                nextBtn.style.height = '32px';
                nextBtn.style.margin = '0 10px';
                nextBtn.style.cursor = 'pointer';
                nextBtn.style.display = 'flex';
                nextBtn.style.justifyContent = 'center';
                nextBtn.style.alignItems = 'center';
                nextBtn.style.fontSize = '16px';
                nextBtn.style.fontWeight = 'bold';
                nextBtn.style.transition = 'all 0.2s ease';
                
                nextBtn.addEventListener('mouseover', function() {
                    if (!this.disabled) {
                        this.style.backgroundColor = '#102E47';
                        this.style.color = '#FFFFFF';
                    }
                });
                
                nextBtn.addEventListener('mouseout', function() {
                    if (!this.disabled) {
                        this.style.backgroundColor = 'transparent';
                        this.style.color = '#102E47';
                    }
                });
                
                paginationControls.appendChild(prevBtn);
                paginationControls.appendChild(pageInfo);
                paginationControls.appendChild(nextBtn);
                
                tableContainer.parentNode.appendChild(paginationControls);
                
                prevBtn.addEventListener('click', function() {
                    if (currentPage > 1) {
                        currentPage--;
                        showPage(currentPage);
                    }
                });
                
                nextBtn.addEventListener('click', function() {
                    if (currentPage < totalPages) {
                        currentPage++;
                        showPage(currentPage);
                    }
                });
            }
        } else {
            const paginationControls = document.querySelector('.pagination-controls');
            if (paginationControls) {
                paginationControls.style.display = noRequestsVisible ? 'none' : 'flex';
            }
        }
        
        if (currentPage > totalPages && totalPages > 0) {
            currentPage = 1;
        }
        
        showPage(currentPage);
    }
    
    function showPage(page) {
        const startIndex = (page - 1) * rowsPerPage;
        const endIndex = startIndex + rowsPerPage;
        const totalPages = Math.ceil(filteredRows.length / rowsPerPage);
        
        filteredRows.forEach(row => {
            row.style.display = 'none';
        });
        
        for (let i = startIndex; i < endIndex && i < filteredRows.length; i++) {
            filteredRows[i].style.display = '';
        }
        
        const pageInfo = document.getElementById('pageInfo');
        if (pageInfo) {
            pageInfo.textContent = page + ' / ' + (totalPages || 1);
        }
        
        const prevBtn = document.getElementById('prevPage');
        const nextBtn = document.getElementById('nextPage');
        
        if (prevBtn) {
            prevBtn.disabled = page === 1;
            prevBtn.style.opacity = page === 1 ? '0.5' : '1';
            prevBtn.style.cursor = page === 1 ? 'default' : 'pointer';
            
            if (page === 1) {
                prevBtn.style.backgroundColor = 'transparent';
                prevBtn.style.color = '#EC6350';
                prevBtn.style.border = '1px solid #EC6350';
            }
        }
        
        if (nextBtn) {
            nextBtn.disabled = page === totalPages || totalPages === 0;
            nextBtn.style.opacity = (page === totalPages || totalPages === 0) ? '0.5' : '1';
            nextBtn.style.cursor = (page === totalPages || totalPages === 0) ? 'default' : 'pointer';
            
            if (page === totalPages || totalPages === 0) {
                nextBtn.style.backgroundColor = 'transparent';
                nextBtn.style.color = '#EC6350';
                nextBtn.style.border = '1px solid #EC6350';
            }
        }
        
        updateRowNumbers();
    }
    
    function updateRowNumbers() {
        const visibleRows = Array.from(document.querySelectorAll('tbody tr')).filter(row => row.style.display !== 'none');
        const startIndex = (currentPage - 1) * rowsPerPage;
        
        visibleRows.forEach((row, index) => {
            const rowNumberCell = row.cells[0];
            if (rowNumberCell) {
                rowNumberCell.textContent = startIndex + index + 1;
            }
        });
    }
    
    function updatePaginationVisibility() {
        currentPage = 1;
        
        setTimeout(() => {
            const noRequestsMessage = document.querySelector('.no-filter-search');
            const noRequestsVisible = noRequestsMessage && noRequestsMessage.style.display !== 'none';
            const paginationControls = document.querySelector('.pagination-controls');
            
            if (paginationControls) {
                paginationControls.style.display = noRequestsVisible ? 'none' : 'flex';
            }
            
            initPagination();
        }, 100);
    }
    
    initPagination();
    
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', updatePaginationVisibility);
    }
    
    document.getElementById('applyFilters')?.addEventListener('click', updatePaginationVisibility);
    document.getElementById('clearFilters')?.addEventListener('click', updatePaginationVisibility);
    
    const filterButton = document.getElementById('sortButton');
    if (filterButton) {
        filterButton.addEventListener('click', function() {
        });
    }
    
    const originalCreateElement = document.createElement;
    document.createElement = function(tag) {
        const element = originalCreateElement.call(document, tag);
        if (tag.toLowerCase() === 'div') {
            const originalSetAttribute = element.setAttribute;
            element.setAttribute = function(name, value) {
                originalSetAttribute.call(this, name, value);
                if (name === 'class' && value === 'no-filter-search') {
                    setTimeout(updatePaginationVisibility, 100);
                }
                return this;
            };
        }
        return element;
    };
    
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('btn-display') || e.target.classList.contains('btn-archive') || 
            (e.target.parentElement && (e.target.parentElement.classList.contains('btn-display') || 
                                       e.target.parentElement.classList.contains('btn-archive')))) {
            setTimeout(updatePaginationVisibility, 300);
        }
    });
    
    const tableBody = document.querySelector('tbody');
    if (tableBody) {
        const observer = new MutationObserver(function(mutations) {
            updatePaginationVisibility();
        });
        
        observer.observe(tableBody, {
            childList: true,
            subtree: true,
            attributes: true,
            attributeFilter: ['style']
        });
    }
});
</script>
</body>
</html>