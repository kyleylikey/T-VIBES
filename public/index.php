<?php
session_start();

function recordVisit() {
    // Directory for storing counter files
    $counterDir = __DIR__ . '/../src/data/';
    
    // Create directory if it doesn't exist
    if (!is_dir($counterDir)) {
        mkdir($counterDir, 0755, true);
    }
    
    // Files for tracking visits
    $totalCountFile = $counterDir . 'total_visits.txt';
    $monthlyCountFile = $counterDir . date('Y_m') . '_visits.txt';
    
    // Only count once per session
    if (!isset($_SESSION['visit_counted'])) {
        // Total visits counter
        $totalCount = (file_exists($totalCountFile)) ? (int)file_get_contents($totalCountFile) : 0;
        $totalCount++;
        file_put_contents($totalCountFile, $totalCount);
        
        // Monthly visits counter
        $monthlyCount = (file_exists($monthlyCountFile)) ? (int)file_get_contents($monthlyCountFile) : 0;
        $monthlyCount++;
        file_put_contents($monthlyCountFile, $monthlyCount);
        
        // Mark this visit as counted in the session
        $_SESSION['visit_counted'] = true;
    }
}

// Record the visit
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    
</head>
<body>

	<header>
			<nav class="navbar navbar-expand-lg navbar-light bg-white">
				<div class="container-fluid">
				<img src="assets/images/headerlogo.jpg" alt="" class="img-fluid" width="250" height="82"> 
				<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
					<span class="navbar-toggler-icon"></span>
				</button>
				<div class="collapse navbar-collapse" id="navbarText">
					<ul class="navbar-nav me-auto mb-2 mb-lg-0">
					<li class="nav-item">
						<a class="nav-link active" aria-current="page" href="#">Home</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="#">Explore</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="#">About Us</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="#">Contact</a>
					</li>
					</ul>
					<ul class="navbar-nav d-flex -flex-row gap-3 align-items-center">
					<li class="nav-item">
							<a href="../src/views/frontend/signup.php" class="nav-link">Sign Up</a>
						</li>
						<li class="nav-item">
						<a href="T-VIBES/src/views/frontend/login.php" 
						class="btn btn-danger rounded-pill px-3 py-1" 
						style="font-size: 1.3rem;">
							Login
						</a>
					</li>
					</ul>`

				</div>
				</div>
			</nav>
		</header>

    </header>

    <!-- Main Content -->
    <main>
        <!-- Hero Section -->
        <section class="hero">
            <div class="hero-content">
                <h1>Step Into Our<br><br>World of Wonders</h1>
                <p>Take a visit and embrace the charm of Taal.</p>
                <a href="#" class="cta-button">Plan Your Next Trip</a>
            </div>
        </section>

		<!-- Features Section -->
        <section class="features">
            <div id="explore" class="container">
                <div class="row">
                    <div class="col-lg-6">
                        <h2 class="text-start">Upidatat dolor veniam ipsum culpa in nulla adipisicing ad magna minim ipsum reprehenderit mollit sit.</h2>
                        <img src="assets/images/thumb-ferris-wheel.jpg" alt="" class="img-fluid mt-3 h-75 w-100 object-fit-cover rounded">
                    </div>
                    <div class="col-lg-6">
                        <div class="row">
                            <div class="col-lg-6">
                                <img src="assets/images/thumb-ferris-wheel.jpg" alt="" class="img-fluid rounded">
                            </div>
                            <div class="col-lg-6">
                                <img src="assets/images/thumb-ferris-wheel.jpg" alt="" class="img-fluid rounded">
                            </div>
                        </div>
                        <div class="row mt-5">
                            <p class="text-start">Eu tempor pariatur dolor labore mollit exercitation velit sit nulla consectetur aliqua id. Quis exercitation consectetur aute duis. Consectetur eiusmod reprehenderit pariatur quis cupidatat laboris ut aute aliquip eu enim.</p>

                            <p class="text-start">Eu nulla Lorem nisi enim aliqua quis anim commodo do consectetur. Incididunt ut id aute fugiat ullamco occaecat fugiat culpa enim quis eu aliquip. Ex reprehenderit ipsum dolor proident commodo non esse consectetur. Labore est Lorem ullamco consectetur nulla duis cillum reprehenderit consectetur esse proident ipsum nostrud. Aute mollit commodo adipisicing aute nisi officia. Laboris consequat labore veniam amet mollit esse Lorem deserunt aliqua mollit duis culpa.</p>

                            <p class="text-start">Adipisicing quis enim non occaecat amet esse. Sunt id qui adipisicing velit eiusmod irure occaecat anim nisi laborum. Ipsum ullamco qui mollit non. Magna sit nisi dolor aute aliquip.</p>
                        </div>
                    </div>
                </div>
            </div>

        </section>

        <!-- About Section -->
		<section class="bg-gray-100 py-12">
        <div id="about" class="max-w-6xl mx-auto px-6 text-center">
		<a href="#" class="btn btn-dark rounded-pill mt-4 mb-4 px-4 py-2 btn-lg">About Us</a>

        <div class="mt-10 grid grid-cols-1 md:grid-cols-5 gap-8 bg-gray-200 p-6 rounded-lg">
			<div class="row bg-light">
            <!-- Column 1 --> 
            <div class="col text-center">
                <div class="p-6 top-60 inline-block">
				    <i class="bi bi-geo-alt rounded-circle h1 bg-light p-2 m-2 shadow"></i>
                </div>
                <h5 class="text-xl font-bold text-gray-800 mt-4">Discover Stuning Attractions</h4>
                <p class="text-gray-600 mt-3 p-3">Adipisicing quis enim non occaecat amet esse. Sunt id qui adipisicing velit eiusmod irure occaecat anim nisi laborum. Ipsum ullamco qui mollit non. Magna sit nisi dolor aute aliquip.</p>
            </div>

            <!-- Column 2 -->
            <div class="col text-center">
                <div class="p-6 top-50 inline-block">
				<i class="bi bi-bell rounded-circle h1 bg-light p-2 m-2 shadow"></i>
                </div>
                <h5 class="text-xl font-bold text-gray-800 mt-4">Stay Updated on Exciting Events</h5>
                <p class="text-gray-600 mt-3 p-3">Adipisicing quis enim non occaecat amet esse. Sunt id qui adipisicing velit eiusmod irure occaecat anim nisi laborum. Ipsum ullamco qui mollit non. Magna sit nisi dolor aute aliquip..</p>
            </div>

            <!-- Column 3 -->
            <div class="col text-center">
                <div class="p-6 top-50 inline-block">
                <i class="bi bi-calendar4 rounded-circle h1 bg-light p-2 m-2 shadow"></i>
                </div>
                <h5 class="text-xl font-bold text-gray-800 mt-4">Plan Your Trip with Ease</h5>
                <p class="text-gray-600 mt-3 p-3">Adipisicing quis enim non occaecat amet esse. Sunt id qui adipisicing velit eiusmod irure occaecat anim nisi laborum. Ipsum ullamco qui mollit non. Magna sit nisi dolor aute aliquip.</p>
            </div>

            <!-- Column 4 -->
            <div class="col text-center">
                <div class="p-6 top-50 inline-block">
                <i class="bi bi-display rounded-circle h1 bg-light p-2 m-2 shadow"></i>
                </div>
                <h5 class="text-xl font-bold text-gray-800 mt-4">Book Reservations Hassle-Free</h5>
                <p class="text-gray-600 mt-3 p-3">Adipisicing quis enim non occaecat amet esse. Sunt id qui adipisicing velit eiusmod irure occaecat anim nisi laborum. Ipsum ullamco qui mollit non. Magna sit nisi dolor aute aliquip..</p>
            </div>

            <!-- Column 5 -->
            <div class="col text-center">
                <div class="p-6 top-50 inline-block">
				<i class="bi bi-info-circle rounded-circle h1 bg-light p-2 m-2 shadow"></i>
                </div>
                <h5 class="text-xl font-bold text-gray-800 mt-4">Get Support from Your LGU</h5>
                <p class="text-gray-600 mt-3 p-3">Adipisicing quis enim non occaecat amet esse. Sunt id qui adipisicing velit eiusmod irure occaecat anim nisi laborum. Ipsum ullamco qui mollit non. Magna sit nisi dolor aute aliquip.</p>
            </div>
        </div>
		</div>
    </div>
</section>



    <!-- Top Destinations Section -->
    <section class="container my-5 text-center">
        <h2 class="mb-4 fw-bold">Top Destinations</h2>
        <div class="row justify-content-center g-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm p-3 text-center">
                    <div class="bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                        <i class="bi bi-image" style="font-size: 3rem; color: gray;"></i>
                    </div>
                    <h5 class="mt-3">Destination Name</h5>
                    <p>★ 5.0</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm p-3 text-center">
                    <div class="bg-light d-flex align-items-center justify-content-center" style="height: 250px;">
                        <i class="bi bi-image" style="font-size: 3rem; color: gray;"></i>
                    </div>
                    <h5 class="mt-3">Destination Name</h5>
                    <p>★ 5.0</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm p-3 text-center">
                    <div class="bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                        <i class="bi bi-image" style="font-size: 3rem; color: gray;"></i>
                    </div>
                    <h5 class="mt-3">Destination Name</h5>
                    <p>★ 5.0</p>
                </div>
            </div>
        </div>
        <a href="#" class="btn btn-dark rounded-pill mt-4 px-4 py-2">Explore Destinations</a>
    </section>



    <!-- Stories Worth Telling Section -->
    <section class="container my-5">
        <div class="row align-items-center">
            <div class="col-md-2 text-start">
                <button class="btn btn-dark rounded-circle me-2" data-bs-target="#storiesCarousel" data-bs-slide="prev">
                    <i class="bi bi-chevron-left"></i>
                </button>
                <button class="btn btn-dark rounded-circle" data-bs-target="#storiesCarousel" data-bs-slide="next">
                    <i class="bi bi-chevron-right"></i>
                </button>
            </div>
            <div class="col-md-10 text-end">
                <h2 class="fw-bold">Stories Worth Telling</h2>
				<p>Straight From Our Guests</p>
            </div>
        </div>
        <div id="storiesCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner text-start">
                <div class="carousel-item active">
                    <blockquote class="blockquote p-4 bg-light text-white rounded text-center">
                        <i class="bi bi-person-circle" style="font-size: 3rem;"></i><br>
                        <p class="fw-bold fs-4 fst-italic">"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat."</p>
                        <footer class="blockquote-footer bg-light text-dark">Username | Destination</footer>
                    </blockquote>
                </div>
                <div class="carousel-item">
                    <blockquote class="blockquote p-4 bg-light text-white rounded text-center">
                        <i class="bi bi-person-circle" style="font-size: 3rem;"></i><br>
                        <p class="fw-bold fs-4 fst-italic">"Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur."</p>
                        <footer class="blockquote-footer bg-light text-dark">Username | Destination</footer>
                    </blockquote>
                </div>
                <div class="carousel-item">
                    <blockquote class="blockquote p-4 bg-light text-white rounded text-center">
                        <i class="bi bi-person-circle" style="font-size: 3rem;"></i><br>
                        <p class="fw-bold fs-4 fst-italic">"Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur."</p>
                        <footer class="blockquote-footer bg-light text-dark">Username | Destination</footer>
                    </blockquote>
                </div>
            </div>
        </div>
    </section>

<!-- Trivia Section -->
<section class="container my-5 text-center">
    <h2 class="fw-bold">Trivia</h2>
    <p class="text-muted">Get to know a little bit more</p>
    <div class="row align-items-center">
        <div class="col-md-6 text-start">
            <h4 class="fw-bold">Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Duis aute irure dolor in reprehenderit in voluptate</h4>
            <p>Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur..</p>
            <a href="#" class="btn btn-dark rounded-pill px-4 py-2">Learn More</a>
        </div>
        <div class="col-md-6">
            <div class="position-relative">
                <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 250px; height: 250px; margin: auto;">
                    <i class="bi bi-image" style="font-size: 3rem; color: gray;"></i>
                </div>
                <div class="position-absolute top-0 start 5 bg-secondary rounded-circle" style="width: 70px; height: 70px;"></div>
                <div class="position-absolute bottom-0 end-0 bg-secondary rounded-circle" style="width: 50px; height: 50px;"></div>
            </div>
        </div>
    </div>
</section>


	</main>

    <?php include '../src/views/templates/footer.html'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>
</html>
