<?php
session_start();
require_once '../../controllers/tourist/destinationcontroller.php';
require_once '../../controllers/helpers.php';

if (isset($_GET['siteid'])) {
    $siteid = $_GET['siteid'];
} else {
    header('Location: explore.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Destination - Taal Heritage Town</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;700&family=Raleway:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../../public/assets/styles/destination.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        * {
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
            font-family: 'Raleway', sans-serif !important;
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

        h1 {
            color: #102E47;
            font-family: 'Raleway', sans-serif !important;
            font-weight: bold;
        }

        h3 {
            color: #102E47;
            font-family: 'Raleway', sans-serif !important;
            font-weight: bold !important;
        }

        .add-to-tour-btn:hover {
            transition: all 0.3s;
            transform: scale(1.03);
        }

        .back-btn {
            border: 2px solid #EC6350 !important;
            background-color: transparent !important;
            color: #EC6350 !important;
            border-radius: 50px !important;
            width: 40px !important;
            height: 40px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            transition: all 0.3s ease !important;
        }

        .back-btn:hover {
            background-color: #EC6350 !important;
            color: #FFFFFF !important;
        }

        @media only screen and (min-width: 993px) and (max-width: 1280px) {
            .container {
                padding: 15px;
                max-width: 100%;
            }
            
            h1 {
                font-size: 2.8rem;
                margin-bottom: 1.8rem;
            }
            
            .row {
                display: flex;
                flex-flow: row wrap;
                margin: 0 -10px;
            }
            
            .col-md-6 {
                width: 50%;
                padding: 0 10px;
                margin-bottom: 15px;
            }
            
            .col-md-6:first-child div {
                height: 500px !important;
                width: 100% !important;
            }
            
            .col-md-6 img {
                height: 100% !important;
                width: 100% !important;
                object-fit: cover !important;
                border-radius: 25px !important;
            }
            
            .col-md-6:nth-child(2) > div {
                width: 100% !important;
            }
            
            .add-to-tour-btn {
                height: 250px !important;
                font-size: 1.7rem !important;
                padding: 20px !important;
                border-radius: 25px !important;
            }
            
            .add-to-tour-btn i {
                font-size: 1.8rem !important;
            }
            
            .p-3.mb-3 {
                height: auto !important;
                min-height: 100px !important;
                margin-bottom: 15px !important;
                border-radius: 25px !important;
            }
            
            .p-3.mb-3 .fw-bold {
                font-size: 1.6rem !important;
            }
            
            .col-6 {
                width: 50%;
                padding: 0 5px;
            }
            
            .col-6 .p-3 {
                height: auto !important;
                min-height: 120px !important;
                padding: 15px !important;
                border-radius: 25px !important;
            }
            
            .col-6 .p-3 .fw-bold {
                font-size: 1.6rem !important;
            }
            
            .col-6 .p-3 div:last-child {
                font-size: 1.1rem;
                line-height: 1.5;
            }
            
            .nav-pills {
                width: 97% !important;
                border-radius: 15px;
            }
            
            .nav-pills .nav-link {
                padding: 10px 20px;
                font-size: 1.1rem;
            }
            
            #pills-overview p {
                width: 97% !important;
                font-size: 1.1rem;
                line-height: 1.6;
            }
            
            .col-md-4, .col-md-7 {
                padding: 0 10px;
            }
            
            .back-btn {
                width: 40px !important;
                height: 40px !important;
                margin-bottom: 15px;
            }
            
            .mt-4 {
                margin-top: 1.5rem !important;
            }
            
            .review-list-card {
                width: 97% !important;
                padding: 20px;
                margin-bottom: 15px;
            }
            
            .row.gx-3 {
                width: 100%;
                margin-left: 0;
                margin-right: 0;
            }
            
            .tab-content {
                width: 100%;
            }
        }

        @media only screen and (min-width: 769px) and (max-width: 992px) {
            .container {
                padding: 15px;
                max-width: 100%;
            }
            
            h1 {
                font-size: 2.5rem;
                margin-bottom: 1.5rem;
            }
            
            .row {
                display: flex;
                flex-flow: row wrap;
                margin: 0 -10px;
            }
            
            .col-md-6 {
                width: 100%;
                padding: 0 10px;
                margin-bottom: 20px;
            }
            
            .col-md-6:first-child div {
                height: auto !important;
                width: 100% !important;
            }
            
            .col-md-6 img {
                height: 100% !important;
                width: 100% !important;
                object-fit: cover !important;
                border-radius: 20px !important;
            }
            
            .col-md-6:nth-child(2) > div {
                width: 100% !important;
            }
            
            .add-to-tour-btn {
                height: 180px !important;
                font-size: 1.5rem !important;
                padding: 20px !important;
                border-radius: 20px !important;
            }
            
            .add-to-tour-btn i {
                font-size: 1.8rem !important;
            }
            
            .p-3.mb-3 {
                height: auto !important;
                min-height: 100px !important;
                margin-bottom: 15px !important;
                border-radius: 20px !important;
            }
            
            .p-3.mb-3 .fw-bold {
                font-size: 1.5rem !important;
            }
            
            .col-6 {
                width: 50%;
                padding: 0 5px;
            }
            
            .col-6 .p-3 {
                height: auto !important;
                min-height: 140px !important;
                padding: 15px !important;
                border-radius: 20px !important;
            }
            
            .col-6 .p-3 .fw-bold {
                font-size: 1.5rem !important;
            }
            
            .col-6 .p-3 div:last-child {
                font-size: 1rem;
                line-height: 1.5;
            }
            
            .nav-pills {
                width: 100% !important;
                border-radius: 15px;
                display: flex;
                justify-content: space-between;
            }
            
            .nav-pills .nav-link {
                padding: 10px 20px;
                font-size: 1.1rem;
                text-align: center;
            }
            
            #pills-overview p {
                width: 100% !important;
                font-size: 1.1rem;
                line-height: 1.6;
            }
            
            .col-md-4, .col-md-7 {
                width: 50%;
                padding: 0 10px;
            }
            
            .back-btn {
                width: 45px !important;
                height: 45px !important;
                margin-bottom: 15px;
            }
            
            .mt-4 {
                margin-top: 1.5rem !important;
            }
            
            .review-list-card {
                width: 100% !important;
                padding: 20px;
                margin-bottom: 15px;
            }
            
            .row.gx-3 {
                width: 100%;
            }
            
            .tab-content {
                width: 100%;
            }
            
            .rating-display {
                text-align: center;
            }
            
            .rating-bars {
                margin-left: 15px;
            }
            
            .row.gx-3 .col-6 .p-3 {
                display: flex;
                flex-direction: column;
                justify-content: flex-start;
            }
        }

        @media only screen and (max-width: 768px) and (min-width: 601px) {
            .container {
                padding: 15px;
                max-width: 100%;
            }
            
            h1 {
                font-size: 2.2rem;
                margin-bottom: 1rem;
            }
            
            .row {
                display: flex;
                flex-flow: row wrap;
                margin: 0 -10px;
            }
            
            .col-md-6 {
                width: 50%;
                padding: 0 10px;
                margin-bottom: 15px;
            }
            
            .col-md-6:first-child div {
                height: 350px !important;
                width: 100% !important;
            }
            
            .col-md-6 img {
                height: 100% !important;
                width: 100% !important;
                object-fit: cover !important;
                border-radius: 20px !important;
            }
            
            .col-md-6:nth-child(2) > div {
                width: 100% !important;
            }
            
            .add-to-tour-btn {
                height: 120px !important;
                font-size: 1.3rem !important;
                padding: 15px !important;
                border-radius: 20px !important;
            }
            
            .add-to-tour-btn i {
                font-size: 1.5rem !important;
            }
            
            .p-3.mb-3 {
                height: auto !important;
                min-height: 80px !important;
                margin-bottom: 15px !important;
                border-radius: 20px !important;
            }
            
            .p-3.mb-3 .fw-bold {
                font-size: 1.3rem !important;
            }
            
            .col-6 {
                padding: 0 5px;
            }
            
            .col-6 .p-3 {
                height: auto !important;
                min-height: 100px !important;
                padding: 12px !important;
                border-radius: 20px !important;
            }
            
            .col-6 .p-3 .fw-bold {
                font-size: 1.3rem !important;
            }
            
            .col-6 .p-3 div:last-child {
                font-size: 0.9rem;
                line-height: 1.4;
            }
            
            .nav-pills {
                width: 100% !important;
                border-radius: 15px;
                display: flex;
                justify-content: space-between;
            }
            
            .nav-pills .nav-link {
                padding: 8px 15px;
                font-size: 1rem;
                text-align: center;
            }
            
            #pills-overview p {
                width: 100% !important;
                font-size: 1rem;
                line-height: 1.5;
            }
            
            .col-md-4, .col-md-7 {
                width: 50%;
                padding: 0 10px;
            }
            
            .back-btn {
                width: 40px !important;
                height: 40px !important;
                margin-bottom: 10px;
            }
            
            .mt-4 {
                margin-top: 1.25rem !important;
            }
            
            .review-list-card {
                width: 100% !important;
                padding: 15px;
                margin-bottom: 10px;
            }
        }

        @media only screen and (max-width: 600px) {
            h1 {
                font-size: 1.5rem;
            }
            
            h3 {
                font-size: 1.2rem;
            }
            
            .container {
                padding-left: 10px;
                padding-right: 10px;
            }
            
            .col-md-6 div {
                height: auto !important;
                width: 100% !important;
            }
            
            .col-md-6 img {
                height: auto !important;
                max-height: 300px;
                border-radius: 15px !important;
            }
            
            .add-to-tour-btn {
                height: auto !important;
                padding: 15px !important;
                font-size: 1.3rem !important;
            }
            
            .add-to-tour-btn i {
                font-size: 1.5rem !important;
            }
            
            .p-3.mb-3, .col-6 .p-3 {
                height: auto !important;
                padding: 10px !important;
            }
            
            .p-3.mb-3 .fw-bold, .col-6 .p-3 .fw-bold {
                font-size: 1.2rem !important;
            }
            
            .col-md-6, .col-6 {
                width: 100%;
                padding-left: 5px;
                padding-right: 5px;
                margin-bottom: 10px;
            }
            
            .row {
                margin-left: -5px;
                margin-right: -5px;
            }
            
            .nav-pills {
                width: 100% !important;
                flex-wrap: wrap;
                gap: 5px;
            }
            
            .nav-pills .nav-item {
                width: 100%;
                text-align: center;
            }
            
            .nav-pills .nav-link {
                padding: 5px;
                font-size: 0.9rem;
            }
            
            .rating-display {
                text-align: center;
                margin-bottom: 20px;
            }
            
            .col-md-4, .col-md-7 {
                width: 100%;
            }
            
            .rating-bar-row .rating-value {
                width: 25px !important;
                font-size: 0.9rem;
            }
            
            .modal-dialog.modal-xl {
                margin: 10px;
            }
            
            .review-text {
                font-size: 0.9rem;
            }
            
            .tab-content {
                width: 100%;
            }
            
            .tab-pane p {
                width: 100% !important;
                font-size: 0.9rem;
            }
            
            .back-btn {
                width: 32px !important;
                height: 32px !important;
                font-size: 0.8rem;
            }
            
            #reviewCarousel .carousel-item {
                padding: 10px;
            }
            
            .review-list-card {
                width: 100% !important;
                padding: 10px;
            }

            .col-6 {
                width: 100%;
                padding-left: 0;
                padding-right: 0;
                margin-bottom: 10px;
            }
            
            .row.gx-3 {
                margin-left: 0;
                margin-right: 0;
                width: 100%;
            }
            
            .col-6 .p-3 {
                width: 100%;
                height: auto !important;
                padding: 10px !important;
                margin-left: 0;
                margin-right: 0;
                box-sizing: border-box;
            }
            
            .col-md-6 > div {
                width: 100% !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            
            .col-md-6:nth-child(2) > div {
                width: 100% !important;
            }
            
            .p-3.mb-3, .col-6 .p-3 {
                width: 100%;
                margin-left: 0;
                margin-right: 0;
                box-sizing: border-box;
            }
        }
    </style>
</head>
<body>
<?php 
if (!isset($_SESSION['usertype']) || $_SESSION['usertype'] !== 'trst') {
    include '../templates/header.php';
} else {
    include '../templates/headertours.php';
}
?>
<main class="container py-4">
    <div class="d-flex flex-column mb-3">
        <button class="btn align-self-start back-btn" onclick="window.location.href='explore.php'">
            <i class="fas fa-arrow-left"></i>
        </button>
        <h1 class="fw-bold mt-2" style="color: #102E47;"><?php echo $siteDetails['sitename']; ?></h1>
    </div>
    
    <div class="row">
        <div class="col-md-6">
            <div style="height: 600px; width: 600px;">
                <img class="img-fluid h-100 w-100 object-fit-cover" src="../../../public/uploads/<?php echo $siteDetails['siteimage']; ?>" style="object-fit: cover; border-radius: 25px;">
            </div>
        </div>
        
        <div class="col-md-6">
            <div style="width: 600px;">
                <div style="height: 300px;">
                    <button class="btn text-white position-relative w-100 h-100 fw-bold add-to-tour-btn"  
                            style="background-color: #EC6350; font-size: 1.8rem; text-align: left; padding: 20px; 
                            border-radius: 25px; font-family: 'Raleway', sans-serif !important;" 
                            <?php if (isset($_SESSION['userid']) && $_SESSION['usertype'] == 'trst') { ?> 
                                <?php if (isset($_SESSION['tour_ui_state']) && $_SESSION['tour_ui_state'] !== 'edit') { ?>
                                    onclick="editModeError()"
                                <?php } else { ?>
                                    onclick="addToTour(<?php echo $siteid; ?>)"
                                <?php } ?>
                            <?php } else {  
                                echo " onclick='loginFirst()'"; 
                            }?> 
                        >
                        <span class="fw-bold" style="font-family: 'Raleway', sans-serif !important;">Add To Your <br>Tour</span>
                            <span class="position-absolute" style="bottom: 15px; right: 15px;">
                                <i class="bi bi-plus-circle" style="font-size: 2rem;"></i>
                            </span>
                    </button>
                </div>

                <div class="mt-4">
                    <div class="p-3 mb-3" style="background-color: #E7EBEE; border-radius: 25px; height: 120px;">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <?php 
                                $avgRating = ($siteDetails['rating_cnt'] == 0) ? 0 : round(($siteDetails['rating']/$siteDetails['rating_cnt']), 1);
                                $ratingDescription = getRatingDescription($avgRating);
                                ?>
                                <div class="fw-bold" style="font-family: 'Raleway', sans-serif !important; color: #102E47; font-size: 1.8rem;"><?php echo $ratingDescription; ?></div>
                                <small class="fs-5" style="color: #757575"><?php echo $reviewCount; ?> review/s</small>
                            </div>
                            <div class="bg-white p-2 rounded-pill">
                                <span class="text-danger me-1">★</span>
                                <span class="fw-bold"><?php echo ($siteDetails['rating_cnt'] == 0) ? "0.0" : number_format($avgRating, 1); ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="row gx-3">
                        <div class="col-6">
                            <div class="p-3" style="background-color: #E7EBEE; border-radius: 25px; height: 140px;">
                                <div class="fw-bold" style="font-family: 'Raleway', sans-serif !important; color: #102E47; font-size: 1.8rem;">Price</div>
                                <div class="fs-5" style="color: #757575">P<?php echo $siteDetails['price']; ?></div>
                            </div>
                        </div>
                        
                        <div class="col-6">
                            <div class="p-3" style="background-color: #E7EBEE; border-radius: 25px; height: 140px;">
                                <div class="fw-bold" style="font-family: 'Raleway', sans-serif !important; color: #102E47; font-size: 1.8rem;">Available on</div>
                                <div style="text-align: justify; color: #757575;"><?php $opdays = Site::binaryToDays($siteDetails['opdays']); echo $opdays;?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> 
        </div>
    </div>

    <div class="row mt-4 gx-4">
        <div class="col-lg-12">
            <ul class="nav nav-pills mb-4" id="pills-tab" role="tablist" style="background-color: #E7EBEE; width: 97%;">
                <li class="nav-item">
                    <a class="nav-link active" id="pills-overview-tab" data-bs-toggle="pill" href="#pills-overview" role="tab" aria-controls="pills-overview" aria-selected="true" style="font-family: 'Raleway', sans-serif !important;">Overview</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="pills-fees-tab" data-bs-toggle="pill" href="#pills-fees" role="tab" aria-controls="pills-fees" aria-selected="false" style="font-family: 'Raleway', sans-serif !important;">Estimated Fees</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="pills-reviews-tab" data-bs-toggle="pill" href="#pills-reviews" role="tab" aria-controls="pills-reviews" aria-selected="false" style="font-family: 'Raleway', sans-serif !important;">Ratings & Reviews</a>
                </li>
            </ul>

            <div class="tab-content" id="pills-tabContent">
            <div class="tab-pane fade show active" id="pills-overview" role="tabpanel" aria-labelledby="pills-overview-tab">
                <h3>Description</h3>
                <p style="text-align: justify; width: 97%;"><?php echo nl2br(htmlspecialchars($siteDetails['description'])); ?></p>
            </div>
                <div class="tab-pane fade" id="pills-fees" role="tabpanel" aria-labelledby="pills-fees-tab">
                    <h3>P<?php echo $siteDetails['price']; ?></h3>
                    <p>per person</p>
                </div>
                <div class="tab-pane fade" id="pills-reviews" role="tabpanel" aria-labelledby="pills-reviews-tab">
                    <div class="row align-items-center mb-5">
                        <div class="col-md-4">
                            <div class="rating-display">
                                <h2 class="display-4 fw-bold"><?php echo ($siteDetails['rating_cnt'] == 0) ? "0.0" : number_format($avgRating, 1); ?></h2>
                                <div class="stars" style="color: #EC6350;">
                                    <?php echo generateStarRating(($siteDetails['rating_cnt'] == 0) ? 0 : $siteDetails['rating']/$siteDetails['rating_cnt']); ?>
                                </div>                                    
                                <span class="rating-count">All Ratings (<?php echo $siteDetails['rating_cnt']; ?>)</span>
                            </div>
                        </div>
                        
                        <div class="col-md-7">
                            <div class="rating-bars">
                                <?php
                                for ($i = 5; $i >= 1; $i--) {
                                    $percentage = isset($ratingDistribution[$i]) ? $ratingDistribution[$i] : 0;
                                ?>
                                <div class="rating-bar-row d-flex align-items-center mb-2">
                                    <div class="rating-value me-2" style="width: 30px;"><?php echo $i; ?>.0</div>
                                    <div class="progress flex-grow-1" style="height: 15px;">
                                        <div class="progress-bar" role="progressbar" 
                                            style="width: <?php echo $percentage; ?>%; background-color: #102E47;" 
                                            aria-valuenow="<?php echo $percentage; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <?php foreach ($siteReviews as $review): ?>
                            <div class="review-list-card" role="button" data-bs-toggle="modal" data-bs-target="#reviewModal" style="cursor: pointer; border-radius: 25px; width: 97%;">
                                <p class="fst-italic"><?php echo htmlspecialchars($review['review']); ?></p>
                                <strong><?php echo htmlspecialchars($review['author']); ?></strong>
                                <span class="text-muted"><?php echo htmlspecialchars(date('F j, Y', strtotime($review['date']))); ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<div class="modal fade review-modal" id="reviewModal" tabindex="-1" aria-labelledby="reviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body p-0">
                <div id="reviewCarousel" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        <?php foreach ($siteReviews as $index => $review): ?>
                            <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                                <div class="review-card">
                                    <i class="fas fa-quote-right quote-icon"></i>
                                    <p class="review-text">
                                        <?php echo htmlspecialchars($review['review']); ?>
                                    </p>
                                    <div class="user-info">
                                        <div class="user-avatar">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <div class="user-details">
                                            <div class="user-name"><?php echo htmlspecialchars($review['author']); ?></div>
                                            <div class="timestamp"><?php echo htmlspecialchars(date('F j, Y', strtotime($review['date']))); ?></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#reviewCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#reviewCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../templates/footer.html'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/js/all.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
    var triggerTabList = [].slice.call(document.querySelectorAll('#pills-tab a'));
    var tabList = triggerTabList.map(function(triggerEl) {
        var tabTrigger = new bootstrap.Tab(triggerEl);

        triggerEl.addEventListener('click', function(event) {
            event.preventDefault();
            tabTrigger.show();
        });

        return tabTrigger;
    });

    triggerTabList.forEach(function(triggerEl) {
        triggerEl.addEventListener('shown.bs.tab', function(event) {
            // This event fires after tab content is shown
            // Scroll to the tab content with smooth animation
            const tabContentId = triggerEl.getAttribute('href');
            const tabContent = document.querySelector(tabContentId);
            
            // Add a small delay to ensure content is rendered before scrolling
            setTimeout(() => {
                tabContent.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }, 100);
        });
    });
});
document.addEventListener("DOMContentLoaded", function() {
    var triggerTabList = [].slice.call(document.querySelectorAll('#pills-tab a'));
    var tabList = triggerTabList.map(function(triggerEl) {
        var tabTrigger = new bootstrap.Tab(triggerEl);

        triggerEl.addEventListener('click', function(event) {
            event.preventDefault();
            tabTrigger.show();
        });

        return tabTrigger;
    });

    triggerTabList.forEach(function(triggerEl) {
        triggerEl.addEventListener('shown.bs.tab', function(event) {
        });
    });
});

function loginFirst() {
    Swal.fire({
        iconHtml: '<i class="fas fa-exclamation-circle"></i>',
        title: "Please log in to continue.",
        text: "Log in to add destinations to your tour!",
        showCancelButton: true,
        confirmButtonText: "Log In",
        cancelButtonText: "Cancel",
        customClass: {
            title: "swal2-title-custom",
            icon: "swal2-icon-custom",
            popup: "swal-custom-popup",
            confirmButton: "swal-custom-btn",
            cancelButton: "swal-custom-btn"
        }
    }).then((result) => {
        if (result.isConfirmed) {
        window.location.href = 'login.php';
        }
    });
}

function addToTour(siteid) {
    Swal.fire({
        iconHtml: '<i class="fas fa-map-marker-alt"></i>',
        title: "Add to Tour?",
        text: "Do you want to add this destination to your tour?",
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
            fetch('tours/tourrequest.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'siteid=' + siteid
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        iconHtml: '<i class="fas fa-circle-check"></i>',
                        title: "Added to your tour!",
                        text: "What would you like to do next?",
                        showCancelButton: true,
                        confirmButtonText: "View My Tour",
                        cancelButtonText: "Continue Browsing",
                        customClass: {
                            title: "swal2-title-custom",
                            icon: "swal2-icon-custom",
                            popup: "swal-custom-popup",
                            confirmButton: "swal-custom-btn",
                            cancelButton: "swal-custom-btn"
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = 'tours/tourrequest.php';
                        } else {
                            window.location.href = 'explore.php';
                        }
                    });
                } else {
                    Swal.fire({
                        iconHtml: '<i class="fas fa-exclamation-circle"></i>',
                        title: "Already Added!",
                        text: data.message || "This destination is already in your tour list.",
                        showCancelButton: true,
                        confirmButtonText: "View My Tour",
                        cancelButtonText: "Continue Browsing",
                        customClass: {
                            title: "swal2-title-custom",
                            icon: "swal2-icon-custom",
                            popup: "swal-custom-popup",
                            confirmButton: "swal-custom-btn",
                            cancelButton: "swal-custom-btn"
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = 'tours/tourrequest.php';
                        } else {
                            window.location.href = 'explore.php';
                        }
                    });
                }
            })
            .catch(error => console.error('Error:', error));
        }
    });
}

function editModeError() {
    Swal.fire({
        iconHtml: '<i class="fas fa-exclamation-circle"></i>',
        title: "Editing Disabled!",
        text: "You cannot add a destination while your tour request is finalized.",
        showConfirmButton: true,
        confirmButtonText: "OK",
        customClass: {
            title: "swal2-title-custom",
            icon: "swal2-icon-custom",
            popup: "swal-custom-popup",
            confirmButton: "swal-custom-btn"
        }
    });
}
</script>
</body>
</html>