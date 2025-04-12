<?php
session_start();
require_once __DIR__ . '/../src/controllers/indexcontroller.php';


function recordVisit() {
    $counterDir = __DIR__ . '/../src/data/';
    
    if (!is_dir($counterDir)) {
        mkdir($counterDir, 0755, true);
    }
    
    $totalCountFile = $counterDir . 'total_visits.txt';
    $monthlyCountFile = $counterDir . date('Y_m') . '_visits.txt';
    
    if (!isset($_SESSION['visit_counted'])) {
        $totalCount = (file_exists($totalCountFile)) ? (int)file_get_contents($totalCountFile) : 0;
        $totalCount++;
        file_put_contents($totalCountFile, $totalCount);
        
        $monthlyCount = (file_exists($monthlyCountFile)) ? (int)file_get_contents($monthlyCountFile) : 0;
        $monthlyCount++;
        file_put_contents($monthlyCountFile, $monthlyCount);
        
        $_SESSION['visit_counted'] = true;
    }
}

recordVisit();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Explore Taal</title>
    <link rel="stylesheet" href="assets/styles/index.css">
    <link rel="stylesheet" href="assets/styles/main.css">
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

        .hero-content h1 {
            font-family: 'Raleway', sans-serif !important;
            font-weight: bold;
            color: #102E47;
            text-shadow: 0 0 3px #FFFFFF, 0 0 5px #FFFFFF, 0 0 8px #FFFFFF;
        }

        .hero-content p {
            text-shadow: 0 0 3px  #102E47, 0 0 5px  #102E47, 0 0 8px  #102E47;
        }
        
        h5 {
            font-family: 'Raleway', sans-serif !important;
            font-weight: bold !important;
            color: #102E47;
        }

        .cta-button {
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

        .cta-button:hover {
            background-color: #102E47 !important;
            color: #FFFFFF !important;
        }

        .btn-custom {
            font-weight: bold !important;
            border: 2px solid #102E47 !important;
            background-color: #FFFFFF !important;
            color: #434343 !important;
            cursor: pointer !important;
            transition: all 0.3s ease !important;
            font-family: 'Nunito', sans-serif !important;
            border-radius: 25px !important;
        }

        .btn-custom:hover {
            background-color: #102E47 !important;
            color: #FFFFFF !important;
            font-weight: bold !important;
        }

        .container h2 {
            font-family: 'Raleway', sans-serif !important;
            font-weight: bold;
            color: #102E47;
        }

        i {
            color: #757575;
        }

        .carousel-item p {
            color: #434343 !important;
        }
    </style>
</head>
<body>
<?php
if (!isset($_SESSION['usertype']) || $_SESSION['usertype'] !== 'trst') {
    echo "<header>
        <nav class='navbar navbar-expand-lg navbar-light bg-white'>
            <div class='container-fluid'>
            <img src='assets/images/headerlogo.jpg' alt='' class='img-fluid' width='250' height='82'> 
            <button class='navbar-toggler' type='button' data-bs-toggle='collapse' data-bs-target='#navbarText' aria-controls='navbarText' aria-expanded='false' aria-label='Toggle navigation'>
                <span class='navbar-toggler-icon'></span>
            </button>
            <div class='collapse navbar-collapse' id='navbarText'>
                <ul class='navbar-nav me-auto mb-2 mb-lg-0'>
                <li class='nav-item'>
                    <a class='nav-link active' aria-current='page' href='#'>Home</a>
                </li>
                <li class='nav-item'>
                    <a class='nav-link' href='../src/views/frontend/explore.php'>Explore</a>
                </li>
                <li class='nav-item'>
                    <a class='nav-link' href='../src/views/frontend/aboutus.php'>About Us</a>
                </li>
                <li class='nav-item'>
                    <a class='nav-link' href='../src/views/frontend/contactus.php'>Contact</a>
                </li>
                </ul>
                <ul class='navbar-nav d-flex -flex-row gap-3 align-items-center'>
                <li class='nav-item'>
                        <a href='../src/views/frontend/signup.php' class='nav-link'>Sign Up</a>
                    </li>
                    <li class='nav-item'>
                    <a href='../src/views/frontend/login.php' 
                    class='btn btn-danger rounded-pill px-3 py-1' 
                    style='font-size: 1.3rem;'>
                        Login
                    </a>
                </li>
                </ul>

            </div>
            </div>
        </nav>
    </header>";
}
else {
    include __DIR__ . '/../src/views/templates/headertours.php';
}
?>
<main>
    <section class="hero">
        <div class="hero-content">
            <h1>Step Into Our<br><br>World of Wonders</h1>
            <p>Take a visit and embrace the charm of Taal.</p>
            <a href="../src/views/frontend/explore.php" class="btn cta-button">Plan Your Next Trip</a>
        </div>
    </section>

    <section class="features">
        <div id="explore" class="container">
            <div class="row">
                <div class="col-lg-6">
                    <h2 style="text-align: justify;">Experience the Rich Heritage and Stunning Views of Taal.</h2>
                    <img src="assets/images/whitehouse.jpg" alt="" class="img-fluid mt-3 h-75 w-100 object-fit-cover" style="border-radius: 25px;">
                </div>
                <div class="col-lg-6">
                    <div class="row">
                        <div class="col-lg-6">
                            <img src="assets/images/marcela.jpg" alt="" class="img-fluid" style="border-radius: 25px;">
                        </div>
                        <div class="col-lg-6">
                            <img src="assets/images/tampuhan.jpg" alt="" class="img-fluid" style="border-radius: 25px;">
                        </div>
                    </div>
                    <div class="row mt-5">
                        <p style="text-align: justify;" style="color: #434343;">Discover Taal’s Spanish-era houses, century-old churches, and breathtaking landscapes. Each street holds a story waiting to be explored.</p>

                        <p style="text-align: justify;" style="color: #434343;">From the iconic Basilica of St. Martin de Tours to the famous Taal Lake, experience history, nature, and adventure all in one place.</p>

                        <p style="text-align: justify;" style="color: #434343;">Plan your trip today and indulge in the culture, cuisine, and charm of this heritage town.</p>
                    </div>
                </div>
            </div>
        </div>

    </section>

    <section class="bg-gray-100 py-12">
    <div id="about" class="max-w-6xl mx-auto px-6 text-center">
    <a href="../src/views/frontend/aboutus.php" class="btn btn-custom mt-4 mb-4 px-4 py-2 btn-lg">About Us</a>

    <div class="mt-10 grid grid-cols-1 md:grid-cols-5 gap-8 bg-gray-200 p-6 rounded-lg">
        <div class="row bg-light">
        <div class="col text-center">
                <div class="p-6 top-60 inline-block">
                    <i class="bi bi-geo-alt rounded-circle h1 bg-light p-2 m-2 shadow"></i>
                </div>
                <h5 class="text-xl font-bold text-gray-800 mt-4">Discover Stunning Attractions</h5>
                <p class="text-gray-600 mt-3 p-3" style="color: #434343;">Explore Taal's breathtaking landmarks, historical sites, and natural wonders. From colonial-era churches to scenic landscapes, there's something for every traveler.</p>
            </div>

            <div class="col text-center">
                <div class="p-6 top-50 inline-block">
                <i class="bi bi-bell rounded-circle h1 bg-light p-2 m-2 shadow"></i>
                </div>
                <h5 class="text-xl font-bold text-gray-800 mt-4">Stay Updated on Exciting Events</h5>
                <p class="text-gray-600 mt-3 p-3" style="color: #434343;">Never miss a festival, cultural event, or local celebration. Stay informed and be part of Taal's vibrant community gatherings.</p>
            </div>

            <div class="col text-center">
                <div class="p-6 top-50 inline-block">
                <i class="bi bi-calendar4 rounded-circle h1 bg-light p-2 m-2 shadow"></i>
                </div>
                <h5 class="text-xl font-bold text-gray-800 mt-4">Plan Your Trip with Ease</h5>
                <p class="text-gray-600 mt-3 p-3" style="color: #434343;">Get all the information you need to create the perfect itinerary. From travel tips to must-visit spots, we've got you covered.</p>
            </div>

            <div class="col text-center">
                <div class="p-6 top-50 inline-block">
                <i class="bi bi-display rounded-circle h1 bg-light p-2 m-2 shadow"></i>
                </div>
                <h5 class="text-xl font-bold text-gray-800 mt-4">Book Reservations Hassle-Free</h5>
                <p class="text-gray-600 mt-3 p-3" style="color: #434343;">Easily reserve accommodations, tours, and activities with just a few clicks. Experience a seamless travel experience in Taal.</p>
            </div>

            <div class="col text-center">
                <div class="p-6 top-50 inline-block">
                <i class="bi bi-info-circle rounded-circle h1 bg-light p-2 m-2 shadow"></i>
                </div>
                <h5 class="text-xl font-bold text-gray-800 mt-4">Get Support from Your LGU</h5>
                <p class="text-gray-600 mt-3 p-3" style="color: #434343;">Need assistance? Connect with the local government for travel guidelines, safety information, and other essential resources.</p>
            </div>
        </div>
    </div>
</div>
</section>

<section class="container my-5 text-center">
        <h2 class="mb-4 fw-bold">Top Destinations</h2>
        <div class="row justify-content-center g-4">
            <?php foreach ($topSites as $site): ?>
                <div class="col-md-4">
                    <div class="card border-0 shadow-lg p-3 text-center" style="border-radius: 25px;">
                        <div class="bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                            <?php if (!empty($site['siteimage'])): ?>
                                <img class="card border-0" src="/T-VIBES/public/uploads/<?php echo $site['siteimage']; ?>" alt="<?php echo $site['sitename']; ?>" style="height: 100%; width: 100%; object-fit: cover; border-radius: 25px;">
                            <?php else: ?>
                                <i class="bi bi-image" style="font-size: 3rem; color: gray;"></i>
                            <?php endif; ?>
                        </div>
                        <h5 class="mt-3"><?php echo $site['sitename']; ?></h5>
                        <p>★ <?php echo $site['ratings']; ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <a href="../src/views/frontend/explore.php" class="btn btn-custom mt-4 px-4 py-2">Explore Destinations</a>
    </section>

     <section class="container my-5">
        <div class="row align-items-center">
            <div class="col-md-2 text-start">
                <button class="btn btn-dark rounded-circle me-2" data-bs-target="#storiesCarousel" data-bs-slide="prev">
                    <i class="bi bi-chevron-left text-white"></i>
                </button>
                <button class="btn btn-dark rounded-circle" data-bs-target="#storiesCarousel" data-bs-slide="next">
                    <i class="bi bi-chevron-right text-white"></i>
                </button>
            </div>
            <div class="col-md-10 text-end">
                <h2 class="fw-bold">Stories Worth Telling</h2>
                <p>Straight From Our Guests</p>
            </div>
        </div>
        <div id="storiesCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner text-start">
                <?php foreach ($recentReviews as $index => $review): ?>
                    <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                        <blockquote class="blockquote p-4 bg-light text-white rounded text-center">
                            <i class="bi bi-person-circle" style="font-size: 3rem;"></i><br>
                            <h5 class="fw-bold"><?php echo $review['author']; ?></h5>
                            <p class="fw-bold fs-4 fst-italic">"<?php echo $review['review']; ?>"</p>
                            <h4 class="" style="color: #434343;"><?php echo $review['sitename']; ?> | <?php echo $review['date']; ?></h4>
                        </blockquote>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="container my-5 text-center">
        <h2 class="fw-bold">Trivia</h2>
        <p class="text-muted">Get to know a little bit more</p>
        <div class="row align-items-center">
            <div class="col-md-6 text-start">
                <h4 class="fw-bold" style="text-align: justify; color: #102E47;">Did You Know? Taal is home to the largest Catholic church in Asia, the Basilica of St. Martin de Tours!</h4>
                <p style="text-align: justify; color: #434343;">This stunning landmark stands as a testament to Spanish-era architecture and has been a beacon of faith for centuries. Take a tour and witness its grandeur.</p>
                <a href="../src/views/frontend/aboutus.php" class="btn btn-custom px-4 py-2">Learn More</a>
            </div>
            <div class="col-md-6">
                <div class="position-relative">
                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center overflow-hidden" style="width: 250px; height: 250px; margin: auto;">
                        <img src="assets/images/taalbasilica.jpg" alt="" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    <div class="position-absolute top-0 start 5 rounded-circle" style="width: 70px; height: 70px;  background-color: #FF6B6B;"></div>
                    <div class="position-absolute bottom-0 end-0 rounded-circle" style="width: 50px; height: 50px;  background-color: #FF6B6B;"></div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include '../src/views/templates/footer.html'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>
</html>
