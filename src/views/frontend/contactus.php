<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us</title>
    <link rel="stylesheet" href="assets/styles/index.css">
    <link rel="stylesheet" href="assets/styles/main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
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

    <!-- Main Content -->
    <main class="container my-5">
        <h2 class="fw-bold mb-4">Contact Us</h2>
        <div class="row g-4">
            <div class="col-md-6">
                <div class="p-4" style="background-color: #EC6350; color: white; border-radius: 0.5rem;">
                    <h4>We're here to help!</h4>
					<div class="bg-white text-dark p-4 rounded-3 shadow-sm mb-3 d-flex align-items-center">
    <div class="icon-circle flex-shrink-0">
        <i class="fas fa-envelope"></i>
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
