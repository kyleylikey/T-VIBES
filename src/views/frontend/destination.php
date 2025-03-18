<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Destination Page</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .review-modal .modal-content {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            border: none;
            border-radius: 1rem;
            padding: 3rem;
            min-height: 400px;
            position: relative;
        }

        .review-modal .review-card {
            background: white;
            border-radius: 1rem;
            padding: 2.5rem;
            margin: 0 auto;
            max-width: 85%;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        .review-modal .quote-icon {
            position: absolute;
            top: 1.5rem;
            right: 1.5rem;
            font-size: 3rem;
            color: #729AB8;
            margin-bottom: 3rem; /* Added space below quote */
        }

        .review-modal .carousel-control-prev,
        .review-modal .carousel-control-next {
            background-color: #EC6350;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            top: 50%;
            transform: translateY(-50%);
            opacity: 1;
            position: absolute;
            z-index: 3;
        }

        .review-modal .carousel-control-prev {
            left: -20px;
        }

        .review-modal .carousel-control-next {
            right: -20px;
        }

        .review-modal .user-info {
            margin-top: 2rem;
            text-align: left;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .review-modal .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #E2E8F0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .review-modal .user-details {
            display: flex;
            flex-direction: column;
        }

        .review-modal .user-name {
            font-weight: bold;
            color: #1A202C;
            margin-bottom: 0.25rem;
            font-size: 1.1rem;
        }

        .review-modal .timestamp {
            color: #718096;
            font-size: 0.875rem;
        }

        .review-modal .review-text {
            font-style: italic;
            font-size: 1.1rem;
            line-height: 1.6;
            color: #4A5568;
            margin-top: 4rem; /* Increased space above text after quote */
            margin-bottom: 2rem;
            text-align: left;
            font-weight: normal;
        }
        /* Review list card styles */
        .review-list-card {
            border: none;
            background: #F8F9FA;
            transition: background-color 0.2s;
            margin-bottom: 1rem;
            cursor: pointer;
            padding: 1.5rem;
            border-radius: 0.5rem;
        }

        .review-list-card:hover {
            background: #F1F3F5;
        }

        /* Modal styles */
        .review-modal .modal-content {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            border: none;
            border-radius: 1rem;
            padding: 3rem;
            min-height: 400px;
            position: relative;
        }

        .review-modal .review-card {
            background: white;
            border-radius: 1rem;
            padding: 2.5rem;
            margin: 0 auto;
            max-width: 85%;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        .review-modal .quote-icon {
            position: absolute;
            top: 1.5rem;
            right: 1.5rem;
            font-size: 3rem;
            color: #729AB8;
            margin-bottom: 3rem;
        }

        .review-modal .carousel-control-prev,
        .review-modal .carousel-control-next {
            background-color: #EC6350;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            top: 50%;
            transform: translateY(-50%);
            opacity: 1;
            position: absolute;
            z-index: 3;
        }

        .review-modal .carousel-control-prev {
            left: -20px;
        }

        .review-modal .carousel-control-next {
            right: -20px;
        }

        .review-modal .user-info {
            margin-top: 2rem;
            text-align: left;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .review-modal .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #E2E8F0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .review-modal .user-details {
            display: flex;
            flex-direction: column;
        }

        .review-modal .user-name {
            font-weight: bold;
            color: #1A202C;
            margin-bottom: 0.25rem;
            font-size: 1.1rem;
        }

        .review-modal .timestamp {
            color: #718096;
            font-size: 0.875rem;
        }

        .review-modal .review-text {
            font-style: italic;
            font-size: 1.1rem;
            line-height: 1.6;
            color: #4A5568;
            margin-top: 4rem;
            margin-bottom: 2rem;
            text-align: left;
            font-weight: normal;
        }
        /* Update pills navigation styling */
        #pills-tab {
            gap: 0.5rem;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            background-color: #F8F9FA;
            padding: 0.5rem;
            border-radius: 2rem;
            width: fit-content;
        }

        #pills-tab .nav-item {
            margin: 0;
        }

        #pills-tab .nav-link {
            border-radius: 50rem;
            padding: 0.5rem 1.25rem;
            font-weight: 600;
            transition: all 0.2s ease-in-out;
            font-size: 0.95rem;
            margin: 0;
            border: none;
            white-space: nowrap;
        }

        #pills-tab .nav-link:not(.active) {
            background-color: transparent;
            color: #102E47;
        }

        #pills-tab .nav-link.active,
        #pills-tab .nav-link:hover {
            background-color: #102E47;
            color: white;
        }

        /* Improve tab content transitions */
        .tab-content > .tab-pane {
            padding: 1.5rem 0;
            opacity: 0;
            transition: opacity 0.15s linear;
        }

        .tab-content > .active {
            opacity: 1;
        }

        /* Add spacing between tab content sections */
        .tab-content > .tab-pane:not(:last-child) {
            margin-bottom: 2rem;
        }

        /* Update review section styles */
        #pills-reviews .row {
            margin-bottom: 3rem;
        }

        /* Rating display styles */
        .rating-display {
            text-align: center;
            padding-right: 2rem;
        }

        .rating-display .display-4 {
            font-size: 3.5rem;
            line-height: 1;
            margin-bottom: 0.25rem;
            color: #102E47;
        }

        .rating-display .stars {
            font-size: 1.5rem;
            line-height: 1;
            margin-bottom: 0.25rem;
        }

        .rating-display .rating-count {
            color: #6B7280;
            font-size: 0.875rem;
        }

        /* Progress bars styles */
        .progress-section {
            padding-top: 0.5rem;
        }

        .progress {
            background-color: #E5E7EB;
            border-radius: 9999px;
            height: 6px;
        }

        .progress-bar {
            transition: width 0.3s ease;
            background-color: #102E47;
        }

        .progress-label {
            min-width: 2rem;
            text-align: right;
            font-size: 0.8rem;
            color: #4B5563;
            margin-left: 0.75rem;
        }

        .progress-row {
            display: flex;
            align-items: center;
            margin-bottom: 0.5rem;
        }

        .progress-row:last-child {
            margin-bottom: 0;
        }

        /* Toast notification styles */
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1050;
        }

        .success-toast {
            background-color: #fff1f0;
            color: #102E47;
            border: none;
            border-radius: 8px;
            min-width: 300px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .success-toast .toast-body {
            padding: 1.25rem;
        }

        .toast-link {
            color: #dc3545;
            text-decoration: none;
            font-weight: 500;
        }

        .toast-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <?php include '../templates/header.php'; ?>

    <!-- Toast Container -->
    <div class="toast-container">
        <div class="toast success-toast" role="alert" aria-live="assertive" aria-atomic="true" id="successToast">
            <div class="toast-body">
                <div class="fw-bold mb-2">Destination Added to Tour!</div>
                <div><a href="#" class="toast-link">Click here to view your Tour details</a></div>
            </div>
        </div>
    </div>

    <main class="container py-4">
        <div class="d-flex flex-column mb-3">
		<button class="btn align-self-start" style="background-color: #EC6350; border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;" 
    onclick="window.location.href='explore.php'">
    <i class="fas fa-arrow-left text-white"></i>
</button>

            <h1 class="fw-bold mt-2" style="color: #102E47;">Destination Name</h1>
        </div>

        <div class="bg-light rounded" style="height: 200px;"></div>

        <div class="row mt-4 gx-4">
            <div class="col-lg-9">
                <ul class="nav nav-pills mb-4" id="pills-tab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="pills-overview-tab" data-bs-toggle="pill" href="#pills-overview" role="tab" aria-controls="pills-overview" aria-selected="true">Overview</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="pills-fees-tab" data-bs-toggle="pill" href="#pills-fees" role="tab" aria-controls="pills-fees" aria-selected="false">Estimated Fees</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="pills-reviews-tab" data-bs-toggle="pill" href="#pills-reviews" role="tab" aria-controls="pills-reviews" aria-selected="false">Ratings & Reviews</a>
                    </li>
                </ul>

                <div class="tab-content" id="pills-tabContent">
                    <div class="tab-pane fade show active" id="pills-overview" role="tabpanel" aria-labelledby="pills-overview-tab">
                        <ul>
                            <li>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</li>
                            <li>Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</li>
                        </ul>
                    </div>
                    <div class="tab-pane fade" id="pills-fees" role="tabpanel" aria-labelledby="pills-fees-tab">
                        <p>Estimated Fees information goes here.</p>
                    </div>
                    <div class="tab-pane fade" id="pills-reviews" role="tabpanel" aria-labelledby="pills-reviews-tab">
                        <div class="row align-items-center mb-5">
                            <!-- Left Column: Rating Display -->
                            <div class="col-md-4">
                                <div class="rating-display">
                                    <h2 class="display-4 fw-bold">5.0</h2>
                                    <div class="stars text-warning">★★★★★</div>
                                    <span class="rating-count">All Ratings (400+)</span>
                                </div>
                            </div>

                            <!-- Right Column: Progress Bars -->
                            <div class="col-md-8">
                                <div class="progress-section">
                                    <div class="progress-row">
                                        <div class="progress flex-grow-1">
                                            <div class="progress-bar" role="progressbar" style="width: 100%"></div>
                                        </div>
                                        <span class="progress-label">5.0</span>
                                    </div>
                                    <div class="progress-row">
                                        <div class="progress flex-grow-1">
                                            <div class="progress-bar" role="progressbar" style="width: 80%"></div>
                                        </div>
                                        <span class="progress-label">4.0</span>
                                    </div>
                                    <div class="progress-row">
                                        <div class="progress flex-grow-1">
                                            <div class="progress-bar" role="progressbar" style="width: 60%"></div>
                                        </div>
                                        <span class="progress-label">3.0</span>
                                    </div>
                                    <div class="progress-row">
                                        <div class="progress flex-grow-1">
                                            <div class="progress-bar" role="progressbar" style="width: 40%"></div>
                                        </div>
                                        <span class="progress-label">2.0</span>
                                    </div>
                                    <div class="progress-row">
                                        <div class="progress flex-grow-1">
                                            <div class="progress-bar" role="progressbar" style="width: 20%"></div>
                                        </div>
                                        <span class="progress-label">1.0</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Review Cards -->
                        <div class="mt-4">
                            <div class="review-list-card" role="button" data-bs-toggle="modal" data-bs-target="#reviewModal" style="cursor: pointer;">
                                <p class="fst-italic">Lorem ipsum dolor sit amet, consectetur adipiscing elit...</p>
                                <strong>User</strong>
                                <span class="text-muted">3 weeks ago</span>
                            </div>
                            <div class="review-list-card" role="button" data-bs-toggle="modal" data-bs-target="#reviewModal" style="cursor: pointer;">
                                <p class="fst-italic">Lorem ipsum dolor sit amet, consectetur adipiscing elit...</p>
                                <strong>User</strong>
                                <span class="text-muted">5 months ago</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 position-relative">
                <div class="sticky-top" style="top: 2rem;">
                    <button class="btn text-white position-relative w-100 py-5 fw-bold" 
                            style="background-color: #EC6350; font-size: 1.5rem; text-align: left; padding-top: 10px; padding-left: 15px;"
                            onclick="showAddedToTourNotification()">
                        <span class="position-absolute" style="top: 10px; left: 15px;">Add To Your <br>Tour</span>
						<br>
						<br>
                        <span class="position-absolute" style="bottom: 15px; right: 15px;">
                            <i class="bi bi-plus-circle" style="font-size: 2rem;"></i>
                        </span>
                    </button>
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
                            <div class="carousel-item active">
                                <div class="review-card">
                                    <i class="fas fa-quote-right quote-icon"></i>
                                    <p class="review-text">
                                        Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
                                    </p>
                                    <div class="user-info">
                                        <div class="user-avatar">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <div class="user-details">
                                            <div class="user-name">User</div>
                                            <div class="timestamp">3 weeks ago</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="carousel-item">
                                <div class="review-card">
                                    <i class="fas fa-quote-right quote-icon"></i>
                                    <p class="review-text">
                                        Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
                                    </p>
                                    <div class="user-info">
                                        <div class="user-avatar">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <div class="user-details">
                                            <div class="user-name">User</div>
                                            <div class="timestamp">5 months ago</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
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
                });
            });
        });

        // Add notification functionality
        function showAddedToTourNotification() {
            const toast = new bootstrap.Toast(document.getElementById('successToast'), {
                animation: true,
                autohide: true,
                delay: 5000
            });
            toast.show();
        }
    </script>
</body>
</html>