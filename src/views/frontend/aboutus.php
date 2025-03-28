<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us</title>
    <link rel="stylesheet" href="assets/styles/index.css">
    <link rel="stylesheet" href="assets/styles/main.css">
    <link rel="stylesheet" href="assets/styles/aboutus.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;700&family=Raleway:wght@300;400;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        * {
            font-family: 'Nunito', sans-serif !important;
        }

        h2 {
            color: #102E47;
            font-family: 'Raleway', sans-serif !important;
            font-weight: bold;
        }

        .trivia-carousel-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: transparent;
            border: none;
            z-index: 10;
        }

        .trivia-carousel-btn.left {
            left: 5%;  
        }

        .trivia-carousel-btn.right {
            right: 5%;
        }

        .trivia-carousel-btn i {
            font-size: 2rem;
            color: #333; 
        }

        h5 {
            text-align: justify;
        }

        .text-muted {
            text-align: justify;
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
<main>
<section class="container my-5">
    <h2 class="fw-bold">Get To Know Us</h2>
    <div class="row">
        <div class="col-md-6">
            <h5>Taal Tourism is your ultimate guide to exploring the rich history, vibrant culture, and breathtaking attractions of Taal. Our mission is to provide visitors with seamless access to information about must-visit heritage sites, local festivities, and exciting experiences that Taal has to offer.</h5>
        </div>
        <div class="col-md-6">
            <p class="text-muted">From its well-preserved Spanish-era houses to the world-renowned Taal Basilica, our platform brings you closer to Taal’s heritage and adventures. Plan your visit with ease, book guided tours, and immerse yourself in the timeless charm of Taal. Let us be your travel companion in discovering the beauty of this historic town!</p>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-3"><div class="bg-light p-5 rounded" style="height: 150px;"></div></div>
        <div class="col-3"><div class="bg-light p-5 rounded" style="height: 150px;"></div></div>
        <div class="col-3"><div class="bg-light p-5 rounded" style="height: 150px;"></div></div>
        <div class="col-3"><div class="bg-light p-5 rounded" style="height: 150px;"></div></div>
    </div>
</section>

<section class="container my-5 text-center position-relative trivia-section">
    <h2 class="fw-bold">Trivia</h2>
    <p class="text-center" style="color: #757575;">Get to know a little bit more</p>

    <div class="carousel-container position-relative">
        <div id="triviaCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                <!-- Slide 1 -->
                <div class="carousel-item active">
                    <div class="row align-items-center">
                        <div class="col-md-6 text-start">
                            <h4 class="fw-bold" style="text-align: justify; color: #102E47;">Did You Know? Taal is home to the largest Catholic church in Asia, the Basilica of St. Martin de Tours!</h4>
                            <p style="text-align: justify; color: #434343;">This stunning landmark stands as a testament to Spanish-era architecture and has been a beacon of faith for centuries. Take a tour and witness its grandeur.</p>
                        </div>
                        <div class="col-md-6 position-relative">
                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" 
                                 style="width: 250px; height: 250px; margin: auto;">
                                <i class="bi bi-image" style="font-size: 3rem; color: gray;"></i>
                            </div>
                            <div class="position-absolute" style="top: 20px; left: 10px; width: 50px; height: 50px; background-color: #FF6B6B; border-radius: 50%;"></div>
                            <div class="position-absolute" style="bottom: -10px; right: 50px; width: 20px; height: 20px; background-color: #FF6B6B; border-radius: 50%;"></div>
                        </div>
                    </div>
                </div>

                <!-- Slide 2 -->
                <div class="carousel-item">
                    <div class="row align-items-center">
                        <div class="col-md-6 text-start">
                            <h4 class="fw-bold">Another Trivia Fact</h4>
                            <p>More interesting information about the topic goes here.</p>
                        </div>
                        <div class="col-md-6 position-relative">
                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" 
                                 style="width: 250px; height: 250px; margin: auto;">
                                <i class="bi bi-image rounded-circle h1 bg-light p-2 m-2 shadow"></i>
                            </div>
                            <div class="position-absolute" style="top: 20px; left: 10px; width: 50px; height: 50px; background-color: #FF6B6B; border-radius: 50%;"></div>
                            <div class="position-absolute" style="bottom: -10px; right: 50px; width: 20px; height: 20px; background-color: #FF6B6B; border-radius: 50%;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Left Button (Outside) -->
        <button class="trivia-carousel-btn left" data-bs-target="#triviaCarousel" data-bs-slide="prev">
            <i class="fas fa-angle-left"></i>
        </button>

        <!-- Right Button (Outside) -->
        <button class="trivia-carousel-btn right" data-bs-target="#triviaCarousel" data-bs-slide="next">
            <i class="fas fa-angle-right"></i>
        </button>
    </div>
</section>

    </main>

    <?php include '../templates/footer.html'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
