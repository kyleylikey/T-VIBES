<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact - Taal Heritage Town</title>
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
        
        .nav-link {
            font-size: 20px; 
            font-family: 'Raleway', sans-serif !important;
        }

        header a.nav-link {
            color: #434343 !important;
            font-weight: normal !important;
            transition: color 0.3s ease, font-weight 0.3s ease;
        }

        header a.nav-link:hover {
            color: #729AB8 !important;
        }

        header a.nav-link.active {
            color: #729AB8 !important;
            font-weight: bold !important;
        }

        .navbar-nav .btn-danger {
            background-color: transparent !important;
            border: 2px solid #EC6350 !important;
            color: #EC6350 !important;
            transition: all 0.3s ease;
            font-weight: bold !important;
        }

        .navbar-nav .btn-danger:hover {
            background-color: #EC6350 !important;
            color: #FFFFFF !important;
            font-weight: bold !important;
        }

        .icon-circle {
            width: 40px;
            height: 40px;
            background-color: #729AB8;
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 50%;
            margin-right: 10px;
        }

        .icon-circle i {
            color: white;
        }

        h2 {
            color: #102E47;
            font-family: 'Raleway', sans-serif !important;
            font-weight: bold;
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

<main class="container my-5">
    <h2 class="fw-bold mb-4">Contact Us</h2>
        <div class="row g-4">
            <div class="col-md-6">
                <div class="p-4" style="background-color: #EC6350; color: white; border-radius: 0.5rem;">
                    <h4>We're here to help!</h4>
                    <div class="bg-white text-dark p-4 rounded-3 shadow-sm mb-3 d-flex align-items-center">
                        <div class="icon-circle flex-shrink-0">
                            <i class="bi bi-envelope"></i>
                        </div>
                        <div class="ms-3">
                            <p class="mb-1 fw-bold">Email</p>
                            <p>sample@email.com</p>
                        </div>
                    </div>

                    <div class="bg-white text-dark p-4 rounded-3 shadow-sm d-flex align-items-center">
                        <div class="icon-circle flex-shrink-0">
                            <i class="bi bi-telephone-fill"></i>
                        </div>
                        <div class="ms-3">
                            <p class="mb-1 fw-bold">Phone</p>
                            <p>+63 123 456 7890</p>
                        </div>
                    </div>

                </div>
            </div>
            <div class="col-md-6">
                <div class="rounded overflow-hidden">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3930.8042315632833!2d120.92426707405195!3d13.87813958653986!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397d9000f3c83e3%3A0xaccfa8e3e1d5d687!2sTaal%2C%20Batangas!5e0!3m2!1sen!2sph!4v1700000000000"
                            width="100%" height="200" style="border:0;" allowfullscreen="" loading="lazy">
                    </iframe>
                </div>
                <div class="mt-3 p-3 d-flex align-items-center" style="background-color: #EC6350; color: white; border-radius: 0.5rem;">
                    <div class="icon">
                        <p><i class="fas fa-map-marker-alt"></i> Address </p>
                    </div>
                </div>
            </div>
        </div>
</main>

<?php include '../templates/footer.html'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
