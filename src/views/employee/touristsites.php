<?php
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

        .add-site-box {
            height: 290px;
            width: 375px;
            background-color: #E7EBEE;
            border-radius: 10px;
            border: 2px dashed #939393;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            cursor: pointer;
            padding: 15px;
            margin-top: 15px;
            transition: background-color 0.3s ease, border-color 0.3s ease;
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
            font-size: 20px;
            color: #939393;
            position: absolute;
            bottom: 10px;
            left: 15px;
            margin: 0;
            font-weight: bold;
            transition: color 0.3s ease;
        }

        .add-site-box:hover {
            background-color: #D0D5DA; 
            border-color: #757575;
        }

        .add-site-box:hover .plus-sign,
        .add-site-box:hover .add-text {
            color: #757575; 
        }

        .info-box {
            height: 290px;
            width: 375px;
            background-color: #729AB8;
            border-radius: 10px;
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
            padding: 15px;
            margin-top: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            cursor: pointer;
        }

        .info-box span {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 4px;
        }

        .info-box p {
            font-size: 20px;
            color: #434343;
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
            height: 82%;
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
                height: 75%;
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

            .info-box {
                padding: 10px;
            }

            .modal-dialog {
                max-width: 100%;
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
                    <a href="" class="nav-link active">
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
                <a href="" class="nav-link text-dark">
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

            <div class="row mt-3 d-flex justify-content-start">
                <div class="col-lg-4 col-md-6 col-12 mb-3">
                    <div class="add-site-box" onclick="showModal()" style="cursor: pointer;">
                        <div class="plus-sign">+</div>
                        <p class="add-text">Add Tourist Site</p>
                    </div>
                </div>
                    <?php
                        if (!empty($sites)) {
                            foreach ($sites as $site) {
                                echo '<div class="col-lg-4 col-md-6 col-12 mb-3">';
                                echo '<div class="info-box" onclick="editModal()" style="cursor: pointer;">';
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
                        </div>
                        <input type="file" id="imageUpload" class="form-control w-100 mt-2">
                    </div>

                    <form class="w-50">
                        <div class="mb-3">
                            <input type="text" class="form-control w-100" id="tourLocation" placeholder="Location">
                        </div>
                        <div class="mb-3">
                            <input type="text" class="form-control w-100" id="tourPrice" placeholder="Price">
                        </div>
                        <div class="mb-3">
                            <input type="text" class="form-control w-100" id="tourSchedule" placeholder="Schedule">
                        </div>
                        <div class="mb-3">
                            <textarea id="descriptionInput" class="form-control w-100" rows="5" placeholder="Description"></textarea>
                        </div>
                    </form>
                </div>

                <div class="modal-footer">
                    <button class="btn-custom">Submit</button>
                </div>
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
                        <div class="image-preview">
                            <i class="bi bi-image" id="previewIcon"></i>
                        </div>
                        <input type="file" id="imageUpload" class="form-control w-100 mt-2">
                    </div>

                    <form class="w-50">
                        <div class="mb-3">
                            <input type="text" class="form-control w-100" id="tourLocation" placeholder="Location">
                        </div>
                        <div class="mb-3">
                            <input type="text" class="form-control w-100" id="tourPrice" placeholder="Price">
                        </div>
                        <div class="mb-3">
                            <input type="text" class="form-control w-100" id="tourSchedule" placeholder="Schedule">
                        </div>
                        <div class="mb-3">
                            <textarea id="descriptionInput" class="form-control w-100" rows="5" placeholder="Description"></textarea>
                        </div>
                    </form>
                </div>

                <div class="edit-modal-footer">
                    <button class="btn-custom">Submit</button>
                </div>
            </div>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/js/all.min.js"></script>

<script>
    function showModal() {
        var modal = new bootstrap.Modal(document.getElementById('touristSitesModal'));
        modal.show();
    }

    function editModal() {
        var modal = new bootstrap.Modal(document.getElementById('editTouristSitesModal'));
        modal.show();
    }

    document.addEventListener("DOMContentLoaded", function () {
        document.querySelector(".modal-footer .btn-custom").addEventListener("click", function () {
            Swal.fire({
                iconHtml: '<i class="fas fa-thumbs-up"></i>',
                title: "Confirm Details?",
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
                        title: "Tourist Site Successfully Added!",
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
                        title: "Tourist Site Successfully Edited!",
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