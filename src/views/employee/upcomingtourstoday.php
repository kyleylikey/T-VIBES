<?php
include '../../../includes/auth.php';
require_once '../../config/dbconnect.php';
require_once '../../controllers/upcomingtourscontroller.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Dashboard - Upcoming Tours</title>
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

        .tour-container {
            margin-top: 30px;
            background-color: #FFFFFF;
            padding: 20px;
            border-radius: 10px;
            display: flex;
            gap: 10px;
            font-family: 'Nunito', sans-serif !important;
        }

        .tour-list {
            display: flex;
            flex-direction: column;
            gap: 20px;
            width: 50%;
            margin: 20px;
            max-height: 350px;
            overflow-y: auto; 
            padding-right: 50px;
            scrollbar-width: thin;
            scrollbar-color: #102E47 #E7EBEE; 
        }

        .tour-list::-webkit-scrollbar {
            width: 6px; 
            border-radius: 6px;
            transition: opacity 0.3s ease-in-out;
            opacity: 0; 
        }

        .tour-list::-webkit-scrollbar-track {
            background: #E7EBEE; 
            border-radius: 6px;
        }

        .tour-list::-webkit-scrollbar-thumb {
            background: #102E47;
            border-radius: 6px;
            border: 2px solid #E7EBEE;
            transition: background 0.3s ease-in-out;
        }

        .tour-list:hover::-webkit-scrollbar,
        .tour-list:focus-within::-webkit-scrollbar {
            opacity: 1;
        }

        .tour-list:hover::-webkit-scrollbar-thumb,
        .tour-list:focus-within::-webkit-scrollbar-thumb {
            background: #729AB8;
        }

        .tour-list:hover {
            scrollbar-color: #102E47 #E7EBEE;
        }

        .tour-details {
            background-color: #E7EBEE;
            border-radius: 8px;
            padding: 20px;
            width: 60%;
            flex-grow: 1;
            margin: 20px;
            display: flex;
            flex-direction: column; 
            justify-content: space-between; 
            height: 100%; 
        }

        .tour-details strong {
            color: #434343;
        }

        .tour-item {
            background-color: #E7EBEE;
            color: #434343;
            border-radius: 8px;
            padding: 20px;
            transition: background-color 0.3s, color 0.3s;
            height: 120px;
        }

        .tour-item:hover {
            background-color: rgba(114, 154, 184, 0.2);
            color: #102E47;
        }

        .tour-item.active {
            background-color: rgba(114, 154, 184, 0.2);
            color: #102E47;
            border: 2px solid #102E47;
        }

        .tour-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            width: 100%;
        }

        .tour-locations {
            margin-left: 20px;
            padding-left: 10px;
            display: block;
            flex-grow: 1; 
            margin-top: -15px;
            color: #757575;
        }

        .button-container {
            display: flex;
            justify-content: center;
            margin-top: auto; 
            gap: 10px;
        }

        .tour-info {
            display: flex;
            justify-content: space-between; 
            width: 40%; 
            margin-top: 10px;
        }

        .tour-info div {
            display: flex;
            flex-direction: column;
            text-align: left;
        }

        .tour-info strong {
            white-space: nowrap;
            color: #434343;
        }

        .tour-info span {
            margin-top: 5px;
            color: #757575;
        }

        .modal-body {
            display: flex;
            align-items: center; 
            gap: 20px;
            color: #434343;
            font-weight: bold;
        }

        .modal-dialog {
            max-width: 50%;
        }

        .modal-content {
            border-radius: 25px;
            font-family: 'Nunito', sans-serif !important; 
            background-color: #E7EBEE;
        }

        .modal-sm-custom {
            max-width: 35%; 
        }

        .form-control {
            resize: none; 
        }

        .modal-footer {
            display: flex;
            justify-content: center;
            gap: 15px;
            padding: 20px;
            border-top: none;
        }

        .modal-title {
            font-size: 24px;
            font-weight: bold;
            color: #434343;
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

        .no-tours-message {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            color: #434343;
            padding: 10px;
            border-radius: 8px;
            width: fit-content;
            margin: 20px auto;
            font-family: 'Nunito', sans-serif !important;
        }

        @media (max-width: 1280px) {
            .btn-custom {
                padding: 8px 16px;
                font-size: 15px;
            }

            .tour-info div {
                margin-right: 20px;
            }

            .modal-sm-custom {
                max-width: 50%;
            }

            .modal-dialog {
                max-width: 60%;
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

            .btn-custom {
                padding: 8px 14px;
                font-size: 14px;
                min-width: 70px;
            }

            .tour-details {
                width: 40%;
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

            .btn-custom {
                padding: 6px 12px;
                font-size: 14px;
            }

            .tour-list, .tour-details {
                width: 100%;
                margin: 10px;
            }

            .tour-info {
                display: flex;
                flex-direction: row;  
                justify-content: space-between; 
                width: 50%;
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
        }

        @media (max-width: 600px) {
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

            .tour-list {
                display: flex;
                flex-direction: row; 
                overflow-x: auto; 
                white-space: nowrap; 
                padding: 10px;
                gap: 15px;
                width: 100%;
                max-width: 100%;
            }

            .tour-list::-webkit-scrollbar {
                height: 6px; 
            }

            .tour-list::-webkit-scrollbar-thumb {
                background: rgba(16, 46, 71, 0.7);
                border-radius: 6px;
            }

            .tour-item {
                min-width: 100%; 
                padding: 15px;
                background: white;
                border-radius: 8px;
                box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
            }

            .tour-info {
                flex-direction: column;
                align-items: flex-start; 
                width: 100%;
            }

            .tour-info div {
                display: flex;
                justify-content: space-between; 
                width: 100%;
                margin-bottom: 20px;
            }

            .tour-info strong {
                flex: 1;
                text-align: left; 
            }

            .tour-info span {
                flex: 1;
                text-align: left;
            }

            .button-container {
                flex-direction: column;
                gap: 10px;
            }

            .tour-container {
                flex-direction: column;
                align-items: center;
            }

            .modal-sm-custom {
                max-width: 100%;
            }

            .modal-dialog {
                max-width: 100%;
            }
        }

        @media (max-width: 360px) {
            .btn-custom {
                font-size: 13px;
                padding: 6px;
            }

            .tour-container {
                padding: 10px;
                flex-direction: column;
                align-items: center;
            }

            .tour-item, .tour-details {
                padding: 15px;
                width: 100%;
            }

            .button-container button {
                width: 100%;
            }
            
            .tour-info {
                flex-direction: column;
                align-items: center;
            }

            .tour-info div{
                margin-left: 20px;
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
                    <a href="tourrequests.php" class="nav-link">
                        <i class="bi bi-map"></i>
                        <span class="d-none d-sm-inline">Tour Requests</span>
                    </a>
                </li>
                <li class="nav-item mb-4">
                    <a href="" class="nav-link active">
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
                <h2>Upcoming Tours</h2>
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
                <button type="button" class="btn-custom active">Today</button>
                <button type="button" class="btn-custom" onclick="window.location.href='upcomingtoursall.php';">All</button>
            </div>

            <?php if (empty($toursToday)): ?>
                <div class="no-tours-message">
                    No upcoming tours today.
                </div>
            <?php else: ?>
                <div class="tour-container">
                    <div class="tour-list">
                        <?php foreach ($toursToday as $tour): ?>
                            <div class="tour-item <?php echo $tour === reset($toursToday) ? 'active' : ''; ?>" 
                                data-tourid="<?php echo htmlspecialchars($tour['tourid']); ?>" 
                                data-sites="<?php echo htmlspecialchars($tour['sites']); ?>" 
                                data-date="<?php echo htmlspecialchars($tour['date']); ?>" 
                                data-pax="<?php echo htmlspecialchars($tour['companions']); ?>">
                                <strong><?php echo htmlspecialchars($tour['name']); ?></strong><br>
                                <?php echo count(explode(', ', $tour['sites'])); ?> Sites
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="tour-details">
                        <?php $firstTour = reset($toursToday); ?>
                        <strong><?php echo htmlspecialchars($firstTour['name']); ?></strong><br>
                        <span class="tour-locations">
                            <?php echo nl2br(htmlspecialchars($firstTour['sites'])); ?>
                        </span>
                        <br><br>

                        <div class="tour-info">
                            <div>
                                <strong>Tour Date:</strong>
                                <span><?php echo htmlspecialchars($firstTour['date']); ?></span>
                            </div>
                            <div>
                                <strong>Pax:</strong>
                                <span><?php echo htmlspecialchars($firstTour['companions']); ?></span>
                            </div>
                        </div>

                        <br><br>

                        <div class="button-container">
                            <button class="btn-custom" onclick="showModal()" style="cursor: pointer;">Edit</button>
                            <button class="btn-custom">Cancel</button>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Edit Tour Modal -->
    <div class="modal fade" id="upcomingToursModal" tabindex="-1" aria-labelledby="upcomingToursModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content p-4">
                <div class="modal-header border-0 text-center">
                    <h5 class="modal-title w-100">Edit Tour</h5>
                    <button type="button" class="btn-close position-absolute end-0 me-3" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body d-flex flex-column align-items-center">
                    <form id="editTourForm" class="w-75">
                        <input type="hidden" id="editTourId"> 
                        <div class="mb-3">
                            <label for="tourSites" class="form-label">Sites</label>
                            <input type="text" class="form-control w-100" id="tourSites" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="tourDate" class="form-label">Tour Date</label>
                            <input type="date" class="form-control w-100" id="tourDate">
                        </div>
                        <div class="mb-3">
                            <label for="tourPax" class="form-label">Pax</label>
                            <input type="number" class="form-control w-100" id="tourPax" placeholder="Number of People">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button id="saveTourChanges" class="btn-custom">Save Changes</button>
                    <button type="button" class="btn-custom" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Cancellation Reason Modal -->
    <div class="modal fade" id="cancelReasonModal" tabindex="-1" aria-labelledby="cancelReasonLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm-custom"> 
            <div class="modal-content swal-custom-popup text-center"> 
                <div class="modal-header border-0">
                    <h5 class="modal-title swal2-title-custom w-100" id="cancelReasonLabel">Reason for Cancellation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="cancelTourId"> 
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
<script src="../../../public/assets/scripts/empupcomingtours.js"></script>

</body>
</html>