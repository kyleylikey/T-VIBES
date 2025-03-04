<?php
include '../../../includes/auth.php';
require_once '../../config/dbconnect.php';
require_once '../../controllers/employee/tourrequestscontroller.php';
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

        .content-container h2 {
            font-weight: bold;
        }

        .content-container h2, 
        .content-container .date h2 {
            color: #434343 !important;
        }

        .header {
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
            color: #102E47;
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
            min-width: 360px;
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

        .image-placeholder img {
            width: 100%;
            height: 100%;
            object-fit: cover;
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
            color: #434343;
        }

        .modal-footer {
            display: flex;
            justify-content: center;
            gap: 15px;
            padding: 20px;
            border-top: none;
        }

        .modal-footer p {
            color: #757575;
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
                max-width: 300px; 
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
                        <span class="d-none d-sm-inline">Overview</span>
                    </a>
                </li>
                <li class="nav-item mb-4">
                    <a href="" class="nav-link active">
                        <i class="bi bi-map"></i>
                        <span class="d-none d-sm-inline">Tour Requests</span>
                    </a>
                </li>
                <li class="nav-item mb-4">
                    <a href="upcomingtourstoday.php" class="nav-link">
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
                <a href="" class="nav-link employee-name active">
                    <i class="bi bi-person-circle"></i>
                    <span class="d-none d-sm-inline">Employee Name</span>
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

            <div class="row mt-3 d-flex justify-content-center">
                <div class="col-lg-12 col-md-12 col-12 mb-3">
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
                                    <?php if(!empty($requests)) {
                                        foreach ($requests as $request) {
                                            echo "<tr onclick='showModal(this)' style='cursor: pointer;' 
                                            data-tourid='" .htmlspecialchars($request['tourid'])."' 
                                            data-userid='" .htmlspecialchars($request['userid'])."' 
                                            >";
                                            echo "<td>".$request['name']."</td>";
                                            echo "<td>".$request['created_at']."</td>";
                                            echo "<td>".$request['total_sites']."</td>";
                                            echo "<td>".$request['date']."</td>";
                                            echo "<td>".$request['companions']."</td>";
                                            echo "</tr>";
                                        }
                                    } 
                                    else {
                                        echo "<tr><td colspan='5' class='text-center'>No requests found.</td></tr>";
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
                            <div class='step'>
                                <div class='circle'>1</div>
                                <div class='dashed-line'></div>
                            </div>
                        </div>
                        <div class="destination-container d-flex">
                            <div class="destination-card">
                                <div class="image-placeholder">
                                    <i class="bi bi-image"></i>
                                </div>
                                <div class="destination-info">
                                    <h6>Destination Name</h6>
                                    <p><i class="bi bi-calendar"></i> Date</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="summary-container">
                        <p><strong>Date Created</strong><br><span id="dateCreated">DD M YYYY</span></p>
                        <p><strong>Number of People</strong><br><span id="numberOfPeople">2</span></p>
                        <p><strong>Estimated Fees</strong></p>
                        <div class="estimated-fees">
                            <p>Destination Name: Price </p>
                            <p>Destination Name: Price </p>
                        </div>
                        <p class="total-price">₱ 0.00 x Pax = <strong id="estimatedFees">₱ 0.00*</strong></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn-custom accept" data-tourid="" data-userid="">Accept</button>
                    <button class="btn-custom decline" data-tourid="" data-userid="">Decline</button>
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
    <script src="../../../public/assets/scripts/employee/tourrequests.js"></script>
</body>

</html>