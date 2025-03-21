<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Explore Page</title>
    <link rel="stylesheet" href="assets/styles/index.css">
    <link rel="stylesheet" href="assets/styles/main.css">
    <link rel="stylesheet" href="assets/styles/aboutus.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

    <?php 
    if (!isset($_SESSION['usertype']) || $_SESSION['usertype'] !== 'trst') {
        include '../templates/header.php';
    } else {
        include '../templates/headertours.php';
    }
    ?>

    <!-- Main Content -->
	<main class="container my-5">
    <h2 class="fw-bold">Popular Destinations</h2>
    
    <!-- Carousel -->
    <div id="destinationCarousel" class="carousel slide mb-4" data-bs-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="../../../public/uploads/taal_heritage_town.jpg" class="img-fluid mt-3 h-75 w-100 object-fit-cover rounded" alt="Taal Heritage Town">
            </div>
            <div class="carousel-item">
                <img src="../../../public/uploads/taal_basilica.jpg" class="img-fluid mt-3 h-75 w-100 object-fit-cover rounded" alt="Taal Basilica">
            </div>
            <div class="carousel-item">
                <img src="../../../public/uploads/san_lorenzo_steps.jpg" class="img-fluid mt-3 h-75 w-100 object-fit-cover rounded" alt="San Lorenzo Steps">
            </div>
            <div class="carousel-item">
                <img src="../../../public/uploads/paradores_castillo.jpg" class="img-fluid mt-3 h-75 w-100 object-fit-cover rounded" alt="Paradores Castillo">
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#destinationCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#destinationCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
        </button>
    </div>

<!-- Destination Cards -->
<div class="row g-4">
    <div class="col-md-6 col-lg-3">
        <a href="destination.php?name=Taal Heritage Town" class="text-decoration-none">
            <div class="card border-0 shadow rounded-3 overflow-hidden position-relative">
                <img src="../../../public/uploads/taal_heritage_town.jpg" class="img-fluid w-100 object-fit-cover" style="height: 200px;" alt="Taal Heritage Town">
                <div class="position-absolute top-0 end-0 m-2" style="background-color: #EC6350; color: white; padding: 4px 8px; border-radius: 5px;">★ 5.0</div>
                <div class="card-body text-center">
                    <h5 class="fw-bold" style="color: #102E47;">Taal Heritage Town</h5>
                    <p class="text-muted">A glimpse into the historic town of Taal.</p>
                </div>
            </div>
        </a>
    </div>

    <div class="col-md-6 col-lg-3">
        <a href="destination.php?name=Taal Basilica" class="text-decoration-none">
            <div class="card border-0 shadow rounded-3 overflow-hidden position-relative">
                <img src="../../../public/uploads/taal_basilica.jpg" class="img-fluid w-100 object-fit-cover" style="height: 200px;" alt="Taal Basilica">
                <div class="position-absolute top-0 end-0 m-2" style="background-color: #EC6350; color: white; padding: 4px 8px; border-radius: 5px;">★ 5.0</div>
                <div class="card-body text-center">
                    <h5 class="fw-bold" style="color: #102E47;">Taal Basilica</h5>
                    <p class="text-muted">Asia's largest Catholic church.</p>
                </div>
            </div>
        </a>
    </div>
</div>


    <?php include '../templates/footer.html'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
