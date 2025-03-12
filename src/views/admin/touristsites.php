<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Tourist Sites</title>
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
            height: 290px;
            width: 375px;
            background-color: rgba(114, 154, 184, 0.2); 
            border-radius: 10px;
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
            padding: 15px;
            margin-top: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            cursor: pointer;
            font-family: 'Nunito', sans-serif !important;
        }

        .info-box span {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 4px;
        }

        .info-box p {
            font-size: 16px;
            color: #102E47;
            position: absolute;
            bottom: 10px;
            left: 15px;
            margin: 0;
            font-weight: bold;
        }

        .info-box:hover {
            transform: scale(1.03);
            transition: all 0.3s;
        }

        .info-box i {
            font-size: 36px;
            color: #939393;
        }

        .modal-body {
            display: flex;
            align-items: stretch; 
            gap: 20px;
        }

        .modal-dialog {
            max-width: 60%;
        }

        .modal-content {
            border-radius: 25px;
            font-family: 'Nunito', sans-serif !important; 
            background-color: #E7EBEE;
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
            color: #102E47;
        }

        .image-upload-container {
            flex: 1; 
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin-top: -18px;
        }

        .image-preview {
            width: 100%; 
            height: 250px; 
            max-height: 250px;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
            border-radius: 10px;
            background-color: #FFFFFF; 
        }

        .image-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        #imageUpload {
            width: 100%;
            max-width: 100%;
            text-align: center;
        }

        .image-preview i {
            font-size: 50px; 
            color: #939393; 
        }

        .modal-body p span {
            color: #757575;
        }
        .modal-body p strong {
            color: #434343;
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

        .btn-custom:hover {
            background-color: #102E47;
            color: #FFFFFF;
            font-weight: bold;
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
            .info-box {
                width: 320px; 
                height: 270px; 
                padding: 12px; 
            }

            .info-box i {
                font-size: 34px;
            }

            .info-box p {
                font-size: 16px; 
                bottom: 10px;
                left: 12px;
            }

            .row.justify-content-start {
                justify-content: center; 
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
                width: 300px; 
                height: 260px; 
                padding: 10px; 
            }

            .info-box i {
                font-size: 32px; 
            }

            .info-box p {
                font-size: 15px; 
                bottom: 8px;
                left: 12px;
            }

            .row.justify-content-start {
                justify-content: center; 
            }

            .modal-dialog {
                max-width: 75% !important; 
                margin: 0 auto;
            }

            .modal-body {
                flex-direction: column; 
                align-items: center;
                text-align: center;
                gap: 20px;
            }

            .image-upload-container {
                width: 100%;
                max-width: 350px; 
            }

            .image-preview {
                width: 100%;
                height: auto;
                height: 250px;
            }

            .w-50 {
                width: 100% !important; 
                text-align: center;
            }

            .modal-footer {
                flex-direction: column; 
                gap: 10px;
                padding: 15px;
            }

            .btn-custom {
                width: 100%; 
                font-size: 14px;
                padding: 12px;
            }

            .modal-title {
                font-size: 22px;
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

            .info-box {
                height: 250px; 
                width: 100%; 
                margin: 10px 0; 
            }

            .info-box i {
                font-size: 30px; 
            }

            .info-box p {
                font-size: 14px; 
                bottom: 5px;
                left: 10px;
            }

            .modal-dialog {
                max-width: 80% !important; 
                margin: 0 auto;
            }

            .modal-body {
                flex-direction: column; 
                align-items: center;
                text-align: center;
                gap: 20px;
            }

            .image-upload-container {
                width: 100%;
            }

            .image-preview {
                width: 100%;
                height: 200px; 
            }

            .w-50 {
                width: 100% !important; 
            }

            .modal-footer {
                flex-direction: column; 
                gap: 10px;
                padding: 15px;
            }

            .btn-custom {
                width: 100%; 
                font-size: 14px;
                padding: 12px;
            }

            .modal-title {
                font-size: 22px;
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

            .info-box {
                width: 100%; 
                height: 260px; 
                padding: 12px;
                font-size: 14px;
            }

            .info-box p {
                font-size: 16px; 
                bottom: 10px; 
                left: 12px;
            }

            .info-box i {
                font-size: 34px; 
            }

            .modal-dialog {
                max-width: 90% !important; 
                margin: 0 auto;
            }

            .modal-body {
                flex-direction: column; 
                align-items: center;
                text-align: center;
                gap: 15px;
            }

            .image-upload-container {
                width: 100%;
            }

            .image-preview {
                width: 100%;
                height: 200px; 
            }

            .w-50 {
                width: 100% !important; 
            }

            .modal-footer {
                flex-direction: column; 
                gap: 10px;
                padding: 15px;
            }

            .btn-custom {
                width: 100%; 
                font-size: 14px;
                padding: 10px;
            }

            .modal-title {
                font-size: 22px;
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
                width: 100%; 
                height: 260px; 
                padding: 12px; 
                font-size: 14px; 
            }

            .info-box p {
                font-size: 14px; 
                bottom: 8px; 
                left: 12px;
            }

            .info-box i {
                font-size: 32px; 
            }

            .modal-dialog {
                max-width: 90% !important; 
                margin: 0 auto;
            }

            .modal-content {
                padding: 15px; 
                border-radius: 20px; 
            }

            .modal-header {
                padding: 10px; 
            }

            .modal-title {
                font-size: 20px; 
            }

            .modal-body {
                flex-direction: column; 
                gap: 15px; 
                align-items: center;
                text-align: center;
            }

            .image-upload-container {
                width: 100%;
            }

            .image-preview {
                width: 100%;
                height: 200px; 
            }

            .w-50 {
                width: 100% !important; 
            }

            .modal-footer {
                flex-direction: column; 
                gap: 10px; 
                padding: 15px;
            }

            .btn-custom {
                width: 100%; 
                font-size: 14px; 
                padding: 10px;
            }
        }

        @media (max-width: 360px) {
            .info-box {
                width: 90%; 
                height: 250px; 
                padding: 10px; 
                font-size: 14px; 
            }

            .info-box p {
                font-size: 14px; 
                bottom: 8px; 
                left: 10px;
            }

            .info-box i {
                font-size: 30px; 
            }

            .modal-dialog {
                max-width: 90% !important; 
                margin: 0 auto;
            }

            .modal-content {
                padding: 15px; 
                border-radius: 15px; 
            }

            .modal-header {
                padding: 10px; 
            }

            .modal-title {
                font-size: 20px; 
            }

            .modal-body {
                flex-direction: column; 
                gap: 10px; 
                align-items: center; 
            }

            .image-preview {
                width: 100%;
                height: 200px; 
            }

            .w-50 {
                width: 100% !important;
                text-align: center;
            }

            .modal-footer {
                flex-direction: column; 
                gap: 10px; 
                padding: 15px;
            }

            .btn-custom {
                width: 100%; 
                font-size: 14px; 
                padding: 8px;
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
                <a href="tourhistory.php" class="nav-link">
                    <i class="bi bi-map"></i>
                    <span class="d-none d-sm-inline">Tour History</span>
                </a>
            </li>
            <li class="nav-item mb-4">
                <a href="" class="nav-link active">
                    <i class="bi bi-image"></i>
                    <span class="d-none d-sm-inline">Tourist Sites</span>
                </a>
            </li>
            <li class="nav-item mb-4">
                <a href="accounts.php" class="nav-link">
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
            <a href="" class="nav-link admin-name active">
                <i class="bi bi-person-circle"></i>
                <span class="d-none d-sm-inline">Manager Name</span>
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
            <h2>Tourist Sites</h2>
            <span class="date">
                <h2>
                    <?php 
                        date_default_timezone_set('Asia/Manila');
                        echo date('M d, Y | h:i A'); 
                    ?>
                </h2>
            </span>
        </div>
        <div class="mt-3 row justify-content-start">
            <div class="d-flex justify-content-center col-12 col-md-6 col-lg-3 mb-3">
                <div class="info-box siteitem" onclick="showModal()" style="cursor: pointer;">
                    <i class="bi bi-image"></i>
                    <p>Destination Name</p>
                </div>
            </div>
            <div class="d-flex justify-content-center col-12 col-md-6 col-lg-3 mb-3">
                <div class="info-box siteitem" onclick="showModal()" style="cursor: pointer;">
                    <i class="bi bi-image"></i>
                    <p>Destination Name</p>
                </div>
            </div>
            <div class="d-flex justify-content-center col-12 col-md-6 col-lg-3 mb-3">
                <div class="info-box siteitem" onclick="showModal()" style="cursor: pointer;">
                    <i class="bi bi-image"></i>
                    <p>Destination Name</p>
                </div>
            </div>
            <div class="d-flex justify-content-center col-12 col-md-6 col-lg-3 mb-3">
                <div class="info-box siteitem" onclick="showModal()" style="cursor: pointer;">
                    <i class="bi bi-image"></i>
                    <p>Destination Name</p>
                </div>
            </div>
            <div class="d-flex justify-content-center col-12 col-md-6 col-lg-3 mb-3">
                <div class="info-box siteitem" onclick="showModal()" style="cursor: pointer;">
                    <i class="bi bi-image"></i>
                    <p>Destination Name</p>
                </div>
            </div>
            <div class="d-flex justify-content-center col-12 col-md-6 col-lg-3 mb-3">
                <div class="info-box siteitem" onclick="showModal()" style="cursor: pointer;">
                    <i class="bi bi-image"></i>
                    <p>Destination Name</p>
                </div>
            </div>
            <div class="d-flex justify-content-center col-12 col-md-6 col-lg-3 mb-3">
                <div class="info-box siteitem" onclick="showModal()" style="cursor: pointer;">
                    <i class="bi bi-image"></i>
                    <p>Destination Name</p>
                </div>
            </div>
            <div class="d-flex justify-content-center col-12 col-md-6 col-lg-3 mb-3">
                <div class="info-box siteitem" onclick="showModal()" style="cursor: pointer;">
                    <i class="bi bi-image"></i>
                    <p>Destination Name</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="touristSitesModal" tabindex="-1" aria-labelledby="touristSitesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content p-4">
            <div class="modal-header border-0">
                <h5 class="modal-title w-100" id="touristSitesModalLabel">Destination Name</h5>
                <button type="button" class="btn-close position-absolute end-0 me-3" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body d-flex gap-4">
                <div class="image-upload-container d-flex flex-column align-items-center">
                    <div class="image-preview d-flex align-items-center justify-content-center">
                        <i class="bi bi-image" id="previewIcon"></i>
                    </div>
                </div>
                <div class="w-50">
                    <p><span>Location</span><br><strong>60 Calle Marcela Mariño Agoncillo, Taal, 4208 Batangas</strong></p>
                    <p><span>Price</span><br><strong>₱500.00</strong></p>
                    <p><span>Schedule</span><br><strong>10:00 AM - 5:00 PM<br>Tuesday - Sunday</strong></p>
                    <p><span>Description</span><br><strong>jdc_030993</strong></p>
                </div>
            </div>
            <div class="modal-footer border-0 d-flex justify-content-center">
                <button class="btn-custom">Delete</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/js/all.min.js"></script>
<script src="../../../public/assets/scripts/main.js"></script>
<script>
    function showModal() {
        var myModal = new bootstrap.Modal(document.getElementById('touristSitesModal'));
        myModal.show();
    }

    document.addEventListener("DOMContentLoaded", function () {
        document.querySelector(".modal-footer .btn-custom").addEventListener("click", function () {
            Swal.fire({
                iconHtml: '<i class="fas fa-thumbs-up"></i>',
                title: "Delete This Destination?",
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
                        title: "Tourist Site Deleted Successfully!",
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

        document.querySelector(".edit-modal-footer .btn-custom").addEventListener("click", function () {
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
                        title: "Account Updated Successfully!",
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
    });
</script>
</body>
</html>