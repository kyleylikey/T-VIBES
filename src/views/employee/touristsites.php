<?php
require_once '../../controllers/helpers.php';
include '../../../includes/auth.php';
require_once '../../config/dbconnect.php';
require_once '../../controllers/sitecontroller.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Dashboard - Tourist Sites</title>
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

        .add-site-box {
            height: 290px;
            width: 375px;
            background-color: #E7EBEE;
            border-radius: 10px;
            border: 2px dashed #939393;
            position: relative;
            cursor: pointer;
            padding: 15px;
            margin-top: 15px;
            transition: background-color 0.3s ease, border-color 0.3s ease;
            font-family: 'Nunito', sans-serif !important;
        }

        .plus-sign {
            font-size: 75px;
            color: #939393;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .add-text {
            font-size: 16px;
            color: #939393;
            position: absolute;
            bottom: 10px;
            left: 15px;
            margin: 0;
            font-weight: bold;
            transition: color 0.3s ease;
        }

        .add-site-box:hover {
            background-color: rgba(114, 154, 184, 0.2);  
            border-color: #729AB8;
        }

        .add-site-box:hover .plus-sign,
        .add-site-box:hover .add-text {
            color: #729AB8; 
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

        .destination-image {
            width: 100%;
            height: 85%;
            object-fit: cover;
            border-radius: 10px 10px 0 0; 
            position: absolute;
            top: 0;
            left: 0;
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
            color: #434343;
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
            background-color: #E7EBEE;
            border: 2px solid #939393;
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

        .form-control {
            resize: none;
        }
        
        .input-group .btn-outline-primary {
            color: #434343;
            border-color: #102E47;
            font-weight: bold;
            padding: 8px 15px;
            text-align: center;
        }

        .input-group .btn-outline-primary.active,
        .input-group .btn-outline-primary:hover,
        .input-group .btn-check:checked + .btn-outline-primary {
            background-color: #102E47;
            color: #FFFFFF;
            border-color: transparent; 
            box-shadow: none;
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

        .edit-modal-footer {
            display: flex;
            justify-content: center;
            gap: 15px;
            padding: 20px;
            border-top: none;
        }

        @media (max-width: 1280px) {
            .add-site-box,
            .info-box {
                width: 100%;
                height: 290px;
            }

            .destination-image {
                height: 82%;
            }

            .modal-dialog {
                max-width: 60%;
            }

            .add-text {
                font-size: 14px;
            }

            .info-box p {
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

            .modal-dialog {
                max-width: 65%;
            }

            .modal-body {
                flex-direction: column;
                align-items: center;
                margin-top: 20px;
            }

            .modal-body form {
                width: 100% !important;
            }

            .image-upload-container {
                width: 100%;
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

            .info-box {
                padding: 10px;
            }

            .modal-dialog {
                max-width: 100%;
            }
            .w-50 {
                width: 100% !important;
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
                    <a href="" class="nav-link active">
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
                    <div class="add-site-box" onclick="showModal()" style="cursor: pointer;">
                        <div class="plus-sign">+</div>
                        <p class="add-text">Add Tourist Site</p>
                    </div>
                </div>
                    <?php
                        if (!empty($sites)) {
                            foreach ($sites as $site) {
                                echo '<div class="d-flex justify-content-center col-12 col-md-6 col-lg-3 mb-3">';
                                echo '<div class="info-box siteitem" style="cursor: pointer;"
                                data-siteid="' . htmlspecialchars($site['siteid']) . '" 
                                data-sitename="' . htmlspecialchars($site['sitename']) . '" 
                                data-siteimage="' . htmlspecialchars($site['siteimage']) . '" 
                                data-sitedesc="' . htmlspecialchars($site['description']) . '" 
                                data-siteopdays="' . htmlspecialchars($site['opdays']) . '" 
                                data-price="' . htmlspecialchars($site['price']) . '">';
                                echo '<img src="/T-VIBES/public/uploads/' . htmlspecialchars($site['siteimage']) . '" alt="' . htmlspecialchars($site['sitename']) . '" class="destination-image">';
                                echo '<p>'.htmlspecialchars($site['sitename']).'</p>';
                                echo '</div>';
                                echo '</div>';
                            }
                        } else {
                            echo '<p>No sites found.</p>';
                        }
                    ?>
            </div>
        </div>
    </div>

    <div class="modal fade" id="touristSitesModal" tabindex="-1" aria-labelledby="touristSitesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content p-4">
                <div class="modal-header border-0">
                    <h5 class="modal-title w-100">Add Tourist Site</h5>
                    <button type="button" class="btn-close position-absolute end-0 me-3" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body d-flex gap-4">
                    <div class="image-upload-container d-flex flex-column align-items-center">
                        <div class="image-preview">
                            <i class="bi bi-image" id="previewIcon"></i>
                            <img id="previewImage" style="display: none;" />
                        </div>
                        <form class="w-100" id="addSiteForm" method="POST" enctype="multipart/form-data">   
                        <input type="hidden" name="action" id="action" value="addSite">
                        <input type="file" name="imageUpload" id="imageUpload" class="form-control w-100 mt-2">
                    </div>
                    <div class="w-50">
                        <div class="mb-3">
                            <input type="text" name="siteName" class="form-control w-100" id="siteName" placeholder="Name">
                        </div>
                        <div class="mb-3">
                            <input type="number" name="sitePrice" min="0" class="form-control w-100" id="sitePrice" placeholder="Price">
                        </div>
                        <div class="input-group mb-3 d-flex flex-wrap justify-content-center gap-2">
                            <input type="text" id="asiteOpDays" name="asiteOpDays" hidden>
                            
                            <input type="checkbox" class="btn-check" id="asun" autocomplete="off" name="adays[]" value="0">
                            <label class="btn btn-outline-primary" for="asun">Sun</label>

                            <input type="checkbox" class="btn-check" id="amon" autocomplete="off" name="adays[]" value="1">
                            <label class="btn btn-outline-primary" for="amon">Mon</label>

                            <input type="checkbox" class="btn-check" id="atue" autocomplete="off" name="adays[]" value="2">
                            <label class="btn btn-outline-primary" for="atue">Tue</label>

                            <input type="checkbox" class="btn-check" id="awed" autocomplete="off" name="adays[]" value="3">
                            <label class="btn btn-outline-primary" for="awed">Wed</label>

                            <input type="checkbox" class="btn-check" id="athu" autocomplete="off" name="adays[]" value="4">
                            <label class="btn btn-outline-primary" for="athu">Thu</label>

                            <input type="checkbox" class="btn-check" id="afri" autocomplete="off" name="adays[]" value="5">
                            <label class="btn btn-outline-primary" for="afri">Fri</label>

                            <input type="checkbox" class="btn-check" id="asat" autocomplete="off" name="adays[]" value="6">
                            <label class="btn btn-outline-primary" for="asat">Sat</label>
                        </div>
                        <div class="mb-3">
                            <textarea name="siteDescription" id="siteDescription" class="form-control w-100" rows="5" placeholder="Description"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn-custom" onclick="aupdateBitmask()">Submit</button>
                </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editTouristSitesModal" tabindex="-1" aria-labelledby="editTouristSitesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content p-4">
                <div class="modal-header border-0">
                    <h5 class="modal-title w-100">Edit Tourist Site</h5>
                    <button type="button" class="btn-close position-absolute end-0 me-3" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body d-flex gap-4">
                    <div class="image-upload-container d-flex flex-column align-items-center">
                    <form class="w-100" id="editSiteForm" method="POST" enctype="multipart/form-data">
                    <div class="image-preview" id="editimagePreview">
                        <i class="bi bi-image" id="editpreviewIcon"></i>
                        <img id="editpreviewImage" style="display: none; max-width: 100%; height: auto;" />
                    </div>
                        <input type="hidden" name="action" id="action" value="editSite">
                        <input type="hidden" name="siteid" id="siteid">
                        <input type="file" id="editimageUpload" name="editimageUpload" class="form-control w-100 mt-2">
                    </div>

                    <div class="w-50">
                        <div class="mb-3">
                            <input type="text" class="form-control w-100" id="siteName" name="siteName" placeholder="Name">
                        </div>
                        <div class="mb-3">
                            <input type="number" class="form-control w-100" id="sitePrice" name="sitePrice" placeholder="Price" min="0">
                        </div>
                        <div class="input-group mb-3 d-flex flex-wrap justify-content-center gap-2">
                            <input type="text" id="asiteOpDays" name="asiteOpDays" hidden>
                            
                            <input type="checkbox" class="btn-check" id="asun" autocomplete="off" name="adays[]" value="0">
                            <label class="btn btn-outline-primary" for="asun">Sun</label>

                            <input type="checkbox" class="btn-check" id="amon" autocomplete="off" name="adays[]" value="1">
                            <label class="btn btn-outline-primary" for="amon">Mon</label>

                            <input type="checkbox" class="btn-check" id="atue" autocomplete="off" name="adays[]" value="2">
                            <label class="btn btn-outline-primary" for="atue">Tue</label>

                            <input type="checkbox" class="btn-check" id="awed" autocomplete="off" name="adays[]" value="3">
                            <label class="btn btn-outline-primary" for="awed">Wed</label>

                            <input type="checkbox" class="btn-check" id="athu" autocomplete="off" name="adays[]" value="4">
                            <label class="btn btn-outline-primary" for="athu">Thu</label>

                            <input type="checkbox" class="btn-check" id="afri" autocomplete="off" name="adays[]" value="5">
                            <label class="btn btn-outline-primary" for="afri">Fri</label>

                            <input type="checkbox" class="btn-check" id="asat" autocomplete="off" name="adays[]" value="6">
                            <label class="btn btn-outline-primary" for="asat">Sat</label>
                        </div>
                        <div class="mb-3">
                            <textarea id="siteDescription" name="siteDescription" class="form-control w-100" rows="5" placeholder="Description"></textarea>
                        </div>
                    </div>
                </div>
                <div class="edit-modal-footer">
                    <button class="btn-custom editsitebtn" onclick="updateBitmask()">Submit</button>
                </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="showDetailsModal" tabindex="-1" aria-labelledby="showDetailsModal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content p-4">
                <div class="modal-header border-0">
                    <h5 class="modal-title w-100">Edit Tourist Site</h5>
                    <button type="button" class="btn-close position-absolute end-0 me-3" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body d-flex gap-4">
                    <div class="image-upload-container d-flex flex-column align-items-center">
                        <div class="image-preview">
                            <img id="displayImage"></img>
                        </div>
                        <input type="text" id="displayFileName" class="form-control w-100 mt-2" readonly>
                    </div>

                    <div class="w-50">
                        <div class="mb-3">
                            <input type="text" class="form-control w-100" id="displaySiteName" readonly>
                        </div>
                        <div class="mb-3">
                            <input type="text" class="form-control w-100" id="displaySitePrice" readonly>
                        </div>
                        <div class="mb-3">
                            <input type="text" class="form-control w-100" id="displaySiteSchedule" readonly>
                        </div>
                        <div class="mb-3">
                            <textarea id="displaySiteDescription" class="form-control w-100" rows="5" readonly></textarea>
                        </div>
                    </div>
                </div>
                <div class="edit-modal-footer">
                    <button class="btn-custom" id="showeditmodal">Edit</button>
                </div>
            </div>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/js/all.min.js"></script>
<script src="../../../public/assets/scripts/main.js"></script>
<script src="../../../public/assets/scripts/emptouristsites.js"></script>

</body>
</html>