<?php
session_start();
require_once '../../controllers/tourist/explorecontroller.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Explore Page</title>
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
    <h2 class="fw-bold">Popular Destinations</h2>

    <div id="destinationCarousel" class="carousel slide " data-bs-ride="carousel">
        <div class="carousel-inner" style="height: 800px;">
            <?php 
            $isFirst = true; 
            foreach ($topSites as $site): 
            ?>
                <div class="carousel-item <?php echo $isFirst ? 'active' : ''; ?> h-100">
                    <img src="../../../public/uploads/<?php echo $site['siteimage']; ?>" class="img-fluid mt-3 h-75 w-100 object-fit-cover rounded" style="object-fit: cover;" alt="<?php echo $site['sitename']; ?>">
                    <h2 class="my-3 fw-bold"><?php echo $site['sitename']; ?></h2>
                </div>
                <?php $isFirst = false; ?>
            <?php endforeach; ?>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#destinationCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#destinationCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
        </button>
    </div>

    <h2 class="fw-bold">All Destinations</h2>
    
    <div class="row g-4">
        <?php foreach ($displaySites as $site): ?>
        <div class="col-md-6 col-lg-3">
            <a href="destination.php?siteid=<?php echo $site['siteid'];?>" class="text-decoration-none">
                <div class="card border-0 shadow rounded-3 overflow-hidden position-relative">
                    <img src="../../../public/uploads/<?php echo $site['siteimage'];?>" class="img-fluid w-100 object-fit-cover" style="height: 200px;" alt="<?php echo $site['sitename'];?>">
                    <div class="position-absolute top-0 end-0 m-2" style="background-color: #EC6350; color: white; padding: 4px 8px; border-radius: 5px;">★ <?php echo $site['rating'];?></div>
                    <div class="card-body text-center">
                        <h5 class="fw-bold" style="color: #102E47;"><?php echo $site['sitename'];?></h5>
                        <p class="text-muted">
                            <?php 
                            $description = $site['description'];
                            $words = explode(' ', $description);
                            $limitedWords = array_slice($words, 0, 10); 
                            echo implode(' ', $limitedWords);
                            if (count($words) > 10) {
                                echo '...';
                            }
                            ?>
                        </p>                
                    </div>
                </div>
            </a>
        </div>
        <?php endforeach;?>
    </div>

<?php include '../templates/footer.html'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
