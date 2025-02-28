<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Destination Page</title>
    <link rel="stylesheet" href="assets/styles/index.css">
    <link rel="stylesheet" href="assets/styles/main.css">
    <link rel="stylesheet" href="assets/styles/aboutus.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

    <?php include '../templates/header.php'; ?>

    <!-- Main Content -->
	<main class="container py-4">
    <div class="d-flex flex-column mb-3">
        <button class="btn align-self-start" style="background-color: #EC6350; border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
            <i class="fas fa-arrow-left text-white"></i>
        </button>
        <h1 class="fw-bold mt-2" style="color: #102E47;">Destination Name</h1>
    </div>
    
    <div class="bg-light rounded" style="height: 200px;"></div>
    
    <div class="d-flex mt-3">
        <div class="flex-grow-1">
            <ul class="nav nav-pills" id="destinationTabs" role="tablist" style="font-weight: bold; gap: 10px;">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active rounded-pill px-4" id="overview-tab" data-bs-toggle="pill" data-bs-target="#overview" type="button" role="tab" style="background-color: #102E47; color: white; font-weight: bold;">Overview</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link rounded-pill px-4" id="fees-tab" data-bs-toggle="pill" data-bs-target="#fees" type="button" role="tab" style="background-color: white; color: #102E47; font-weight: bold;">Estimated Fees</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link rounded-pill px-4" id="reviews-tab" data-bs-toggle="pill" data-bs-target="#reviews" type="button" role="tab" style="background-color: white; color: #102E47; font-weight: bold;">Ratings & Reviews</button>
                </li>
            </ul>
        </div>
        <div class="d-flex flex-column align-items-end w-25 position-relative">
            <button class="btn text-white position-relative w-100 py-5 fw-bold" style="background-color: #EC6350; font-size: 1.5rem; text-align: left; padding-top: 10px; padding-left: 15px;">
                <span class="position-absolute" style="top: 10px; left: 15px;">Add To Your Tour</span>
                <span class="position-absolute" style="bottom: 15px; right: 15px;">
                    <i class="bi bi-plus-circle" style="font-size: 2rem;"></i>
                </span>
            </button>
        </div>
    </div>
    
    <div class="tab-content mt-3" id="destinationTabsContent">
        <div class="tab-pane fade show active" id="overview" role="tabpanel">
            <ul>
                <li>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</li>
                <li>Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</li>
            </ul>
        </div>
        <div class="tab-pane fade" id="fees" role="tabpanel">
            <p>Estimated Fees information goes here.</p>
        </div>
        <div class="tab-pane fade" id="reviews" role="tabpanel">
            <div>
                <h2 class="fw-bold" style="color: #102E47;">5.0</h2>
                <div class="d-flex align-items-center">
                    <span class="text-warning">★★★★★</span>
                    <span class="ms-2">All Ratings (400+)</span>
                </div>
                <div class="mt-3">
                    <div class="d-flex align-items-center">
                        <span class="me-2">5.0</span>
                        <div class="progress w-50">
                            <div class="progress-bar bg-dark" role="progressbar" style="width: 80%"></div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="me-2">4.0</span>
                        <div class="progress w-50">
                            <div class="progress-bar bg-dark" role="progressbar" style="width: 60%"></div>
                        </div>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="card p-3 mb-2 review-card" role="button" data-bs-toggle="modal" data-bs-target="#reviewModal">
                        <p class="fst-italic">Lorem ipsum dolor sit amet, consectetur adipiscing elit...</p>
                        <strong>User</strong>
                        <span class="text-muted">3 weeks ago</span>
                    </div>
                    <div class="card p-3 review-card" role="button" data-bs-toggle="modal" data-bs-target="#reviewModal">
                        <p class="fst-italic">Lorem ipsum dolor sit amet, consectetur adipiscing elit...</p>
                        <strong>User</strong>
                        <span class="text-muted">5 months ago</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<div class="modal fade" id="reviewModal" tabindex="-1" aria-labelledby="reviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content text-center p-4 position-relative">
            <i class="fa-solid fa-quote-right position-absolute" style="top: 20px; right: 20px; font-size: 2rem; color: #729AB8;"></i>
            <div class="modal-body">
                <div id="reviewCarousel" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        <div class="carousel-item active">
                            <p class="fst-italic">Lorem ipsum dolor sit amet, consectetur adipiscing elit...</p>
                            <strong>User</strong>
                            <span class="text-muted">3 weeks ago</span>
                        </div>
                        <div class="carousel-item">
                            <p class="fst-italic">Lorem ipsum dolor sit amet, consectetur adipiscing elit...</p>
                            <strong>User</strong>
                            <span class="text-muted">5 months ago</span>
                        </div>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#reviewCarousel" data-bs-slide="prev" style="background-color: #EC6350; border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#reviewCarousel" data-bs-slide="next" style="background-color: #EC6350; border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        var tabButtons = document.querySelectorAll("#destinationTabs .nav-link");
        var tabContents = document.querySelectorAll(".tab-pane");

        tabButtons.forEach(function(button) {
            button.addEventListener("click", function() {
                tabButtons.forEach(btn => {
                    btn.style.backgroundColor = "white";
                    btn.style.color = "#102E47";
                    btn.style.fontWeight = "bold";
                });
                this.style.backgroundColor = "#102E47";
                this.style.color = "white";
                this.style.fontWeight = "bold";

                tabContents.forEach(tab => tab.classList.remove("show", "active"));
                document.querySelector(this.getAttribute("data-bs-target")).classList.add("show", "active");
            });
        });
    });
</script>




    <?php include '../templates/footer.html'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
