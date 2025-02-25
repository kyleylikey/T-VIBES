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

        .stepper {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start; /* Aligns circles towards the top */
            padding-top: 10px; /* Moves the first circle up */
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
            background-color: white; 
            border: 4px solid #102E47; 
            color: #102E47; 
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: bold;
        }

        .dashed-line {
            width: 2px;
            height: 120px; /* Adjust height dynamically */
            border-left: 4px dashed #102E47;
        }

        .destination-card {
            display: flex;
            align-items: center;
            padding: 12px;
            width: 100%;
            max-width: 350px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            background-color: #fff;
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
        }

        .destination-info p {
            margin-bottom: 0;
            color: #6c757d;
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
            min-height: 50%; 
            border-radius: 25px;
        }

        .summary-container {
            flex-grow: 1; 
            min-height: 250px; 
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .summary-container p:nth-child(1) { margin-bottom: 20px; } /* Date Created */
        .summary-container p:nth-child(2) { margin-bottom: 20px; } /* Number of People */
        .summary-container p:nth-child(3) { margin-bottom: 25px; } /* Estimated Fees */

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

        .modal-footer {
            display: flex;
            justify-content: center;
            gap: 15px;
            padding: 20px;
            border-top: none;
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
        }

        .btn-custom:hover {
            background-color: #102E47;
            color: white;
        }

        .modal-sm-custom {
            max-width: 35%; 
        }

        .modal-content {
            border-radius: 25px; 
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
                text-align: left; /* Changed from center */
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
                    <a href="home.php" class="nav-link text-dark">
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
                                    <tr onclick="showModal()" style="cursor: pointer;">
                                        <td>User</td>
                                        <td>DD MMM YY</td>
                                        <td>2</td>
                                        <td>DD MMM YY</td>
                                        <td>2</td>
                                    </tr>
                                    <tr onclick="showModal()" style="cursor: pointer;">
                                        <td>User</td>
                                        <td>DD MMM YY</td>
                                        <td>2</td>
                                        <td>DD MMM YY</td>
                                        <td>2</td>
                                    </tr>
                                    <tr onclick="showModal()" style="cursor: pointer;">
                                        <td>User</td>
                                        <td>DD MMM YY</td>
                                        <td>2</td>
                                        <td>DD MMM YY</td>
                                        <td>2</td>
                                    </tr>
                                    <tr onclick="showModal()" style="cursor: pointer;">
                                        <td>User</td>
                                        <td>DD MMM YY</td>
                                        <td>2</td>
                                        <td>DD MMM YY</td>
                                        <td>2</td>
                                    </tr>
                                    <tr onclick="showModal()" style="cursor: pointer;">
                                        <td>User</td>
                                        <td>DD MMM YY</td>
                                        <td>2</td>
                                        <td>DD MMM YY</td>
                                        <td>2</td>
                                    </tr>
                                    <tr onclick="showModal()" style="cursor: pointer;">
                                        <td>User</td>
                                        <td>DD MMM YY</td>
                                        <td>2</td>
                                        <td>DD MMM YY</td>
                                        <td>2</td>
                                    </tr>
                                    <tr onclick="showModal()" style="cursor: pointer;">
                                        <td>User</td>
                                        <td>DD MMM YY</td>
                                        <td>2</td>
                                        <td>DD MMM YY</td>
                                        <td>2</td>
                                    </tr>
                                    <tr onclick="showModal()" style="cursor: pointer;">
                                        <td>User</td>
                                        <td>DD MMM YY</td>
                                        <td>2</td>
                                        <td>DD MMM YY</td>
                                        <td>2</td>
                                    </tr>
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
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body d-flex">
                    <div class="d-flex">
                        <div class="stepper">
                            <div class="step">
                                <div class="circle">1</div>
                                <div class="dashed-line"></div>
                            </div>
                            <div class="step">
                                <div class="circle">2</div>
                            </div>
                        </div>

                        <div class="destination-container">
                            <div class="destination-card first-card">
                                <div class="image-placeholder">
                                    <i class="bi bi-image"></i>
                                </div>
                                <div class="destination-info">
                                    <h6>Destination Name</h6>
                                    <p><i class="bi bi-calendar"></i> Date</p>
                                </div>
                            </div>

                            <div class="destination-card second-card">
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
                        <p><strong>Date Created</strong><br>DD M YYYY</p>
                        <p><strong>Number of People</strong><br>2</p>
                        <p><strong>Estimated Fees</strong></p>
                        <div class="estimated-fees">
                            <p>Destination Name <span>x2</span></p>
                            <p>Destination Name </p>
                        </div>
                        <p class="total-price"><strong>₱ 0.00</strong></p>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn-custom">Accept</button>
                    <button class="btn-custom">Decline</button>
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

<script>
    function showModal() {
        var modal = new bootstrap.Modal(document.getElementById('tourRequestModal'));
        modal.show();
    }
</script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Accept Button
        document.querySelector(".btn-custom:nth-child(1)").addEventListener("click", function () {
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
                        iconHtml: '<i class="fas fa-circle-check"></i>',
                        title: "Successfully Accepted Tour Request!",
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

        // Decline Button with Reason for Cancellation
        document.querySelector(".btn-custom:nth-child(2)").addEventListener("click", function () {
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
                    // Show Reason for Cancellation modal
                    var cancelModal = new bootstrap.Modal(document.getElementById("cancelReasonModal"));
                    cancelModal.show();
                }
            });
        });

        // Submit Reason for Cancellation
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

            // Close the modal
            var cancelModal = bootstrap.Modal.getInstance(document.getElementById("cancelReasonModal"));
            cancelModal.hide();

            // Show success message
            Swal.fire({
                iconHtml: '<i class="fas fa-circle-check"></i>',
                title: "Successfully Declined Tour Request!",
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