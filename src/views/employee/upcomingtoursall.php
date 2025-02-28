<?php
session_start();
require_once '../../controllers/helpers.php';

if (!isset($_SESSION['userid'])) {
    header('Location: ../frontend/login.php'); 
    exit();
}

if ($_SESSION['usertype'] !== 'emp') {
    echo "<!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Access Denied</title>
        <link rel='stylesheet' href='../../../public/assets/styles/main.css'>
        <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css'>
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        <script>
            setTimeout(function() {
                Swal.fire({
                    iconHtml: '<i class=\"fas fa-exclamation-circle\"></i>',
                    customClass: {
                        icon: 'swal2-icon swal2-error-icon',
                    },
                    html: '<p style=\"font-size: 24px; font-weight: bold;\">Access Denied! You do not have permission to access this page.</p>',
                    showConfirmButton: false,
                    timer: 3000
                }).then(() => {
                    window.location.href = '../frontend/login.php';
                });
            }, 100);
        </script>
        <style>
            .swal2-popup {
                border-radius: 12px;
                padding: 20px;
            }
            .swal2-icon.swal2-error-icon {
                border: none;
                font-size: 10px;
                display: flex;
                align-items: center;
                justify-content: center;
                width: 60px;
                height: 60px;
                color: #102E47;
            }
        </style>
    </head>
    <body></body>
    </html>";
    exit();
}
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
    <script src="../../../public/assets/scripts/main.js"></script>
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

        .content-container h2 {
            font-weight: bold;
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
            color: #102E47;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-right: 10px;
            margin-top: 10px;
            min-width: 80px;
            text-align: center;
        }

        .btn-custom.active {
            background-color: #102E47 !important;
            color: white !important;
            font-weight: bold;
        }

        .btn-custom:hover {
            background-color: #102E47;
            color: white;
        }

        .tour-container {
            margin-top: 30px;
            background-color: #729AB8; 
            padding: 20px;
            border-radius: 10px;
            display: flex;
            gap: 10px;
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
            scrollbar-color: transparent transparent; 
        }

        .tour-list::-webkit-scrollbar {
            width: 6px; 
            border-radius: 6px;
            transition: opacity 0.3s ease-in-out;
            opacity: 0; 
        }

        .tour-list::-webkit-scrollbar-track {
            background: rgba(231, 235, 238, 0.3); 
            border-radius: 6px;
        }

        .tour-list::-webkit-scrollbar-thumb {
            background: rgba(16, 46, 71, 0.7);
            border-radius: 6px;
            border: 2px solid rgba(231, 235, 238, 0.5);
            transition: background 0.3s ease-in-out;
        }

        .tour-list:hover::-webkit-scrollbar,
        .tour-list:focus-within::-webkit-scrollbar {
            opacity: 1;
        }

        .tour-list:hover::-webkit-scrollbar-thumb,
        .tour-list:focus-within::-webkit-scrollbar-thumb {
            background: #102E47;
        }

        .tour-list:hover {
            scrollbar-color: rgba(16, 46, 71, 0.7) rgba(231, 235, 238, 0.3);
        }

        .tour-details {
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            width: 60%;
            flex-grow: 1;
            margin: 20px;
            display: flex;
            flex-direction: column; 
            justify-content: space-between; 
            min-height: 100%; 
        }

        .tour-item {
            background-color: white;
            border: 2px solid #102E47;
            border-radius: 8px;
            padding: 20px;
            transition: background-color 0.3s, color 0.3s;
            height: 120px;
            width: 90%;
            margin-left: 10%;
        }

        .tour-item:hover {
            background-color: #102E47;
            color: white;
        }

        .tour-item.active {
            background-color: #102E47;
            color: white;
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
            margin-top: -15px;
        }

        .button-container {
            display: flex;
            justify-content: center; 
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
        }

        .tour-info span {
            margin-top: 5px; 
        }

        .tour-date-header {
            display: flex;
            align-items: center;
        }

        .tour-date {
            text-align: left;
            font-weight: bold;
            margin-right: 10px;
        }

        .tour-line {
            flex-grow: 1;
            border: none;
            border-top: 2px solid black; 
        }

        .modal-body {
            display: flex;
            align-items: center; 
            gap: 20px;
        }

        .modal-dialog {
            max-width: 50%;
        }

        .modal-content {
            border-radius: 25px; 
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

            .nav-link:hover {
                background-color: #102E47 !important; 
                color: white !important;
                transition: background 0.3s ease;
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
                    <a href="home.php" class="nav-link text-dark">
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
                    <a href="" class="nav-link active">
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
                <button type="button" class="btn-custom" onclick="window.location.href='upcomingtourstoday.php';">Today</button>
                <button type="button" class="btn-custom active">All</button>
            </div>

            <div class="tour-container">
                <div class="tour-list">
                    <div class="tour-date-header">
                        <div class="tour-date">
                            <strong>11/29/24</strong><br>
                            <strong>Friday</strong>
                        </div>
                        <hr class="tour-line">
                    </div>

                    <div class="tour-item active">
                        <strong>Tourist Name</strong><br>
                        No. of Sites
                    </div>
                    <div class="tour-item">
                        <strong>Tourist Name</strong><br>
                        No. of Sites
                    </div>
                    <div class="tour-date-header">
                        <div class="tour-date">
                            <strong>Date</strong><br>
                            <strong>Day</strong>
                        </div>
                        <hr class="tour-line">
                    </div>

                    <div class="tour-item">
                        <strong>Tourist Name</strong><br>
                        No. of Sites
                    </div>
                    <div class="tour-item">
                        <strong>Tourist Name</strong><br>
                        No. of Sites
                    </div>
                </div> 

                <div class="tour-details">
                    <strong>Tourist Name</strong><br>
                    <span class="tour-locations">
                        Tour List
                    </span>
                    <br><br>

                    <div class="tour-info">
                        <div>
                            <strong>Tour Date:</strong>
                            <span>11/12/2024</span>
                        </div>
                        <div>
                            <strong>Pax:</strong>
                            <span>2</span>
                        </div>
                    </div>

                    <br><br>

                    <div class="button-container">
                        <button class="btn-custom" onclick="showModal()" style="cursor: pointer;">Edit</button>
                        <button class="btn-custom">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="upcomingToursModal" tabindex="-1" aria-labelledby="upcomingToursModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content p-4">
                <div class="modal-header border-0 text-center">
                    <h5 class="modal-title w-100">Edit Tour</h5>
                    <button type="button" class="btn-close position-absolute end-0 me-3" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body d-flex flex-column align-items-center">
                    <form class="w-75">
                        <div class="mb-3">
                            <label for="tourSites" class="form-label">Sites</label>
                            <input type="text" class="form-control w-100" id="tourSites" placeholder="Enter Sites">
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
                    <button class="btn-custom">Save Changes</button>
                    <button class="btn-custom">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="cancelReasonModal" tabindex="-1" aria-labelledby="cancelReasonLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm-custom"> 
            <div class="modal-content swal-custom-popup text-center"> 
                <div class="modal-header border-0">
                    <h5 class="modal-title swal2-title-custom w-100" id="cancelReasonLabel">Write Reason</h5>
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

<script>
    function showModal() {
        var modal = new bootstrap.Modal(document.getElementById('upcomingToursModal'));
        modal.show();
    }

    document.addEventListener("DOMContentLoaded", function () {
        document.querySelector(".modal-footer .btn-custom:nth-child(1)").addEventListener("click", function () {
            Swal.fire({
                iconHtml: '<i class="fas fa-thumbs-up"></i>',
                title: "Confirm Changes?",
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
                        iconHtml: '<i class="fas fa-circle-check"></i>',
                        title: "Tour Successfully Edited!",
                        timer: 3000,
                        showConfirmButton: false,
                        customClass: {
                            title: "swal2-title-custom",
                            icon: "swal2-icon-custom",
                            popup: "swal-custom-popup"
                        }
                    });
                }
            });
        });

        document.querySelector(".modal-footer .btn-custom:nth-child(2)").addEventListener("click", function () {
            var modal = bootstrap.Modal.getInstance(document.getElementById('upcomingToursModal'));
            modal.hide(); 
        });

        document.querySelector(".button-container .btn-custom:nth-child(2)").addEventListener("click", function () {
            Swal.fire({
                iconHtml: '<i class="fas fa-thumbs-up"></i>',
                title: "Confirm Cancel?",
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
                    var cancelModal = new bootstrap.Modal(document.getElementById("cancelReasonModal"));
                    cancelModal.show();
                }
            });
        });

        document.getElementById("submitCancelReason").addEventListener("click", function () {
            let reason = document.getElementById("cancelReasonInput").value.trim();

            if (reason === "") {
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

            var cancelModal = bootstrap.Modal.getInstance(document.getElementById("cancelReasonModal"));
            cancelModal.hide();

            Swal.fire({
                iconHtml: '<i class="fas fa-circle-check"></i>',
                title: "Tour Successfully Cancelled!",
                timer: 3000,
                showConfirmButton: false,
                customClass: {
                    title: "swal2-title-custom",
                    icon: "swal2-icon-custom",
                    popup: "swal-custom-popup"
                }
            });
        });
    });
</script>

</body>
</html>