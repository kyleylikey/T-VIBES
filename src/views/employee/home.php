<?php
require_once '../../controllers/helpers.php';
include '../../../includes/auth.php';
require_once '../../config/dbconnect.php';
require_once '../../controllers/homecontroller.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Dashboard - Overview</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;700&family=Raleway:wght@300;400;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        * {
            font-family: 'Nunito', sans-serif;
            box-sizing: border-box;
        }

        .sidebar {
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
            color: #102E47 !important;
            padding: 10px;
            border-radius: 5px;
            transition: background 0.3s ease;
        }

        .nav-link.active {
            background-color: #102E47 !important;
            color: white !important;
            font-weight: bold;
        }

        .nav-link i {
            color: inherit; 
        }

        .nav-link:hover {
            background-color: #102E47 !important; 
            color: white !important;
            transition: background 0.3s ease;
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

        .content-container h2 {
            font-weight: bold;
        }

        .info-box {
            position: relative;
            min-height: 150px;
            min-width: 200px; 
            background-color: #729AB8;
            border-radius: 10px;
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
            padding: 15px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: flex-start;
            text-align: left;
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

        .info-box h1 {
            font-weight: bold;
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
        }

        thead th {
            color: black;
            font-weight: bold;
            padding-bottom: 10px;
            background: none !important;
            border: none !important;
            box-shadow: none !important;
        }

        tbody tr {
            background: #E7EBEE;
            border-radius: 15px;
            box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1); 
        }

        tbody tr td {
            padding: 10px;
            border: none;
        }

        tbody tr td:first-child {
            border-top-left-radius: 15px;
            border-bottom-left-radius: 15px;
        }

        tbody tr td:last-child {
            border-top-right-radius: 15px;
            border-bottom-right-radius: 15px;
        }

        .btn-custom {
            padding: 10px 20px;
            font-size: 16px;
            font-weight: bold;
            border: 2px solid #102E47;
            border-radius: 25px;
            background-color: white;
            color: #102E47;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
            display: block;
            margin: 5px auto 0 auto;
        }

        .btn-custom:hover {
            background-color: #102E47;
            color: white;
        }

        .star-icon {
            font-size: 14px !important; 
            color: #434343 !important;
        }

        .swal2-icon {
            background: none !important;
            border: none !important;
            box-shadow: none !important;
        }

        .swal2-icon-custom {
            font-size: 10px; 
            color: #102E47; 
        }

        .swal2-title-custom {
            font-size: 24px !important;
            font-weight: bold;
            color: #434343 !important;
        }

        .swal-custom-popup {
            padding: 20px;
            border-radius: 25px;
        }

        .swal-custom-btn {
            padding: 10px 20px !important;
            font-size: 16px !important;
            font-weight: bold !important;
            border: 2px solid #102E47 !important;
            border-radius: 25px !important;
            background-color: white !important;
            color: #102E47 !important;
            cursor: pointer !important;
            transition: all 0.3s ease !important;
        }

        .swal-custom-btn:hover {
            background-color: #102E47 !important;
            color: white !important;
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

            .menu-section {
                margin-top: auto;
                margin-bottom: auto;
                padding: 10px 0; 
            }

            .main-content {
                margin-left: 200px;
                width: calc(100% - 200px);
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
                background-color: #102E47 !important;
                color: white !important;
                border-radius: 5px;
            }

            .nav-link:hover {
                background-color: #102E47 !important; 
                color: white !important;
                transition: background 0.3s ease;
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

            .nav-link:hover {
                background-color: #102E47 !important; 
                color: white !important;
                transition: background 0.3s ease;
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

            table {
                font-size: 12px;
                overflow-x: auto;
            }

            tbody tr td {
                padding: 5px; 
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
                    <a href="" class="nav-link active">
                        <i class="bi bi-grid"></i>
                        <span class="d-none d-sm-inline">Overview</span>
                    </a>
                </li>
                <li class="nav-item mb-4">
                    <a href="tourrequests.php" class="nav-link text-dark">
                        <i class="bi bi-map"></i>
                        <span class="d-none d-sm-inline">Tour Requests</span>
                    </a>
                </li>
                <li class="nav-item mb-4">
                    <a href="upcomingtourstoday.php" class="nav-link text-dark">
                        <i class="bi bi-geo"></i>
                        <span class="d-none d-sm-inline">Upcoming Tours</span>
                    </a>
                </li>
                <li class="nav-item mb-4">
                    <a href="reviews.php" class="nav-link text-dark">
                        <i class="bi bi-pencil-square"></i>
                        <span class="d-none d-sm-inline">Reviews</span>
                    </a>
                </li>
                <li class="nav-item mb-4">
                    <a href="touristsites.php" class="nav-link text-dark">
                        <i class="bi bi-image"></i>
                        <span class="d-none d-sm-inline">Tourist Sites</span>
                    </a>
                </li>
            </ul>
        </div>
        
        <ul class="nav nav-pills flex-column mb-4">
            <li class="nav-item mb-3">
                <a href="" class="nav-link active">
                    <i class="bi bi-person-circle"></i>
                    <span class="d-none d-sm-inline">Employee Name</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="javascript:void(0);" class="nav-link text-dark" onclick="logoutConfirm()">
                    <i class="bi bi-box-arrow-right"></i>
                    <span class="d-none d-sm-inline">Sign Out</span>
                </a>
            </li>
        </ul>
    </div>

    <div class="main-content">
        <div class="content-container">
            <div class="header">
                <h2>Overview</h2>
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
                <div class="col-lg-4 col-md-6 col-12 mb-3">
                    <div class="info-box">
                        <span>Pending Tours</span>
                        <i class="bi bi-bookmark-fill"></i>
                        <h1><?php echo $pendingTours; ?></h1>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-12 mb-3">
                    <div class="info-box">
                        <span>Upcoming Tours</span>
                        <i class="bi bi-signpost"></i>
                        <h1><?php echo $upcomingTours; ?></h1>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-12 mb-3">
                    <div class="info-box">
                        <span>Pending Reviews</span>
                        <i class="bi bi-pencil-fill"></i>
                        <h1><?php echo $pendingReviews; ?></h1>
                    </div>
                </div>
            </div>

            <div class="row mt-3 d-flex justify-content-center">
                <div class="col-lg-6 col-md-12 col-12 mb-3">
                    <div class="info-box">
                        <span>Latest Tour Requests</span>
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
                                    <?php if (empty($latestRequests)) : ?>
                                        <tr>
                                            <td colspan="5" class="text-center">No tour requests available</td>
                                        </tr>
                                    <?php else : ?>
                                        <?php foreach ($latestRequests as $request) : ?>
                                            <tr>
                                                <td><?php echo $request['name']; ?></td>
                                                <td><?php echo date('d M Y', strtotime($request['created_at'])); ?></td>
                                                <td><?php echo $request['destinations']; ?></td>
                                                <td><?php echo date('d M Y', strtotime($request['travel_date'])); ?></td>
                                                <td><?php echo $request['companions']; ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <button class="btn-custom" onclick="window.location.href='tourrequests.php'">See All</button>    
                    </div>
                </div>

                <div class="col-lg-6 col-md-12 col-12 mb-3">
                    <div class="info-box">
                        <span>Recent Reviews</span>
                        <div class="table-responsive"> 
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Author</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($recentReviews)) : ?>
                                        <tr>
                                            <td colspan="2" class="text-center">No reviews available</td>
                                        </tr>
                                    <?php else : ?>
                                        <?php foreach ($recentReviews as $review) : ?>
                                            <tr>
                                                <td><?php echo $review['author']; ?></td>
                                                <td><?php echo date('d M Y', strtotime($review['date'])); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <button class="btn-custom" onclick="window.location.href='reviews.php'">See All</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/js/all.min.js"></script>
<script src="../../../public/assets/scripts/main.js"></script>
</body>
</html>