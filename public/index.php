<?php
session_start();

if (!isset($_SESSION['userid'])) {
    header('Location: ../src/views/frontend/login.php'); 
    exit();
}

if ($_SESSION['usertype'] !== 'trst') {
    echo "<!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Access Denied</title>
        <link rel='stylesheet' href='assets/styles/main.css'>
        <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css'>
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        <script>
            setTimeout(function() {
                Swal.fire({
                    iconHtml: '<i class=\"fas fa-exclamation-circle\"></i>',
                    customClass: {
                        icon: 'swal2-icon swal2-error-icon',
                    },
                    html: '<p style=\"font-size: 24px; font-weight: bold;\">Access Denied! This page is for tourists only.</p>',
                    showConfirmButton: false,
                    timer: 3000
                }).then(() => {
                    window.location.href = '../src/views/frontend/login.php';
                });
            }, 100);
        </script>
        <style>
            .swal2-popup {
                border-radius: 12px;
                padding: 20px;
            }
            .swal2-icon.swal2-error-icon {
                border: none;
                font-size: 10px;
                display: flex;
                align-items: center;
                justify-content: center;
                width: 60px;
                height: 60px;
                color: #333;
            }
        </style>
    </head>
    <body></body>
    </html>";
    exit();
}
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
    <?php include '../src/views/templates/header.php'; ?>

    <!-- Main Content -->
    <main>
        <!-- Hero Section -->
        <section class="hero">
            <div class="hero-content">
                <h1>Step Into Our<br><br>World of Wonders</h1>
                <br>
                <p>Take a visit and embrace the charm of Taal.</p>
                <a href="#" class="cta-button">Plan Your Next Trip</a>
            </div>
        </section>

        <section class="features">
            <div class="container">
                <div class="row">
                    <div class="col-lg-6">
                        <h2 class="text-start">Upidatat dolor veniam ipsum culpa in nulla adipisicing ad magna minim ipsum reprehenderit mollit sit.</h2>
                        <img src="assets\images\thumb-ferris-wheel.jpg" alt="" class="img-fluid mt-3">
                    </div>
                    <div class="col-lg-6">
                        <div class="row">
                            <div class="col-lg-6">
                                <img src="assets\images\thumb-ferris-wheel.jpg" alt="" class="img-fluid">
                            </div>
                            <div class="col-lg-6">
                                <img src="assets\images\thumb-ferris-wheel.jpg" alt="" class="img-fluid">
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
        <section class="about">
            <h2>About Us</h2>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Discover more about what we offer and how we help you explore.</p>
            <a href="#" class="about-button">Learn More</a>
        </section>
    </main>

    <!-- Footer Section -->
    <footer>
        <p>&copy; 2025 Your Company. All rights reserved.</p>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>
</html>
