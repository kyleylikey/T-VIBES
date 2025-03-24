<?php
session_start();
require_once '../../controllers/tourist/destinationcontroller.php';
require_once '../../controllers/helpers.php';

if (isset($_GET['siteid'])) {
    $siteid = $_GET['siteid'];
} else {
    header('Location: explore.php');
    exit();
}
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
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;700&family=Raleway:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../../public/assets/styles/destination.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .swal2-icon {
            background: none !important;
            border: none !important;
            box-shadow: none !important;
        }

        .swal2-icon-custom {
            font-size: 10px; 
            color: #EC6350; 
        }

        .swal2-title-custom {
            font-size: 24px !important;
            font-weight: bold;
            color: #434343 !important;
        }

        .swal-custom-popup {
            padding: 20px;
            border-radius: 25px;
            font-family: 'Raleway', sans-serif !important;
        }

        .swal-custom-btn {
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

        .swal-custom-btn:hover {
            background-color: #102E47 !important;
            color: #FFFFFF !important;
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
<main class="container py-4">
    <div class="d-flex flex-column mb-3">
        <button class="btn align-self-start" style="background-color: #EC6350; border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;" onclick="window.location.href='explore.php'">
            <i class="fas fa-arrow-left text-white"></i>
        </button>
        <h1 class="fw-bold mt-2" style="color: #102E47;"><?php echo $siteDetails['sitename']; ?></h1>
    </div>
    <div style="height:600px;">
        <img class="img-fluid h-100 w-100 object-fit-cover rounded" src="../../../public/uploads/<?php echo $siteDetails['siteimage']; ?>" style="object-fit: cover;"></img>
    </div>

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
                    <h3>Operating Days</h3>
                    <h5><?php $opdays = Site::binaryToDays($siteDetails['opdays']); 
                    echo $opdays;?></h5>
                    <h3 class="mt-5">Description</h3>
                    <p><?php echo $siteDetails['description']; ?></p>
                </div>
                <div class="tab-pane fade" id="pills-fees" role="tabpanel" aria-labelledby="pills-fees-tab">
                    <h3>P<?php echo $siteDetails['price']; ?></h3>
                    <p>per person</p>
                </div>
                <div class="tab-pane fade" id="pills-reviews" role="tabpanel" aria-labelledby="pills-reviews-tab">
                    <div class="row align-items-center mb-5">
                        <div>
                            <div class="rating-display">
                                <h2 class="display-4 fw-bold"><?php echo $siteDetails['rating']; ?></h2>
                                <div class="stars text-warning">
                                    <?php echo generateStarRating($siteDetails['rating']); ?>
                                </div>                                    
                                <span class="rating-count">All Ratings (<?php echo $siteDetails['rating_cnt']; ?>)</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <?php foreach ($siteReviews as $review): ?>
                            <div class="review-list-card" role="button" data-bs-toggle="modal" data-bs-target="#reviewModal" style="cursor: pointer;">
                                <p class="fst-italic"><?php echo htmlspecialchars($review['review']); ?></p>
                                <strong><?php echo htmlspecialchars($review['author']); ?></strong>
                                <span class="text-muted"><?php echo htmlspecialchars(date('F j, Y', strtotime($review['date']))); ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 position-relative">
            <div class="sticky-top" style="top: 2rem;">
                <button class="btn text-white position-relative w-100 py-5 fw-bold"  
                    style="background-color: #EC6350; font-size: 1.5rem; text-align: left; padding-top: 10px; 
                    padding-left: 15px;" 
                    <?php if (isset($_SESSION['userid']) && $_SESSION['usertype'] == 'trst') { ?> 
                        <?php if (isset($_SESSION['tour_ui_state']) && $_SESSION['tour_ui_state'] !== 'edit') { ?>
                            onclick="editModeError()"
                        <?php } else { ?>
                            onclick="addToTour(<?php echo $siteid; ?>)"
                        <?php } ?>
                    <?php } else {  
                        echo " onclick='loginFirst()'"; 
                    }?> 
                >
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
                        <?php foreach ($siteReviews as $index => $review): ?>
                            <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                                <div class="review-card">
                                    <i class="fas fa-quote-right quote-icon"></i>
                                    <p class="review-text">
                                        <?php echo htmlspecialchars($review['review']); ?>
                                    </p>
                                    <div class="user-info">
                                        <div class="user-avatar">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <div class="user-details">
                                            <div class="user-name"><?php echo htmlspecialchars($review['author']); ?></div>
                                            <div class="timestamp"><?php echo htmlspecialchars(date('F j, Y', strtotime($review['date']))); ?></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/js/all.min.js"></script>
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

function loginFirst() {
    Swal.fire({
        iconHtml: '<i class="fas fa-exclamation-circle"></i>',
        title: "Please log in to continue.",
        text: "Log in to add destinations to your tour!",
        showCancelButton: true,
        confirmButtonText: "Log In",
        cancelButtonText: "Cancel",
        customClass: {
            title: "swal2-title-custom",
            icon: "swal2-icon-custom",
            popup: "swal-custom-popup",
            confirmButton: "swal-custom-btn",
            cancelButton: "swal-custom-btn"
        }
    }).then((result) => {
        if (result.isConfirmed) {
        window.location.href = 'login.php';
        }
    });
}

function addToTour(siteid) {
    fetch('tours/tourrequest.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: 'siteid=' + siteid
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                iconHtml: '<i class="fas fa-circle-check"></i>',
                title: "Added to your tour!",
                text: "What would you like to do next?",
                showCancelButton: true,
                confirmButtonText: "View My Tour",
                cancelButtonText: "Continue Browsing",
                customClass: {
                    title: "swal2-title-custom",
                    icon: "swal2-icon-custom",
                    popup: "swal-custom-popup",
                    confirmButton: "swal-custom-btn",
                    cancelButton: "swal-custom-btn"
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'tours/tourrequest.php';
                } else {
                    window.location.href = 'explore.php';
                }
            });
        } else {
            Swal.fire({
                iconHtml: '<i class="fas fa-exclamation-circle"></i>',
                title: "Already Added!",
                text: data.message || "This destination is already in your tour list.",
                showCancelButton: true,
                confirmButtonText: "View My Tour",
                cancelButtonText: "Continue Browsing",
                customClass: {
                    title: "swal2-title-custom",
                    icon: "swal2-icon-custom",
                    popup: "swal-custom-popup",
                    confirmButton: "swal-custom-btn",
                    cancelButton: "swal-custom-btn"
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'tours/tourrequest.php';
                } else {
                    window.location.href = 'explore.php';
                }
            });
        }
    })
    .catch(error => console.error('Error:', error));
}

function editModeError() {
    Swal.fire({
        iconHtml: '<i class="fas fa-exclamation-circle"></i>',
        title: "Editing Disabled!",
        text: "You cannot add a destination while your tour request is finalized.",
        showConfirmButton: true,
        confirmButtonText: "OK",
        customClass: {
            title: "swal2-title-custom",
            icon: "swal2-icon-custom",
            popup: "swal-custom-popup",
            confirmButton: "swal-custom-btn"
        }
    });
}
</script>
</body>
</html>