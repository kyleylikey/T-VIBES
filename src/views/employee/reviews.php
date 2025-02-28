<?php
session_start();
require_once '../../controllers/helpers.php';
require_once '../../config/dbconnect.php';

if (!isset($_SESSION['userid'])) {
    header('Location: ../frontend/login.php'); 
    exit();
}

if ($_SESSION['usertype'] !== 'emp') {
    echo "<!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Access Denied</title>
        <link rel='stylesheet' href='../../../public/assets/styles/main.css'>
        <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css'>
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        <script>
            setTimeout(function() {
                Swal.fire({
                    iconHtml: '<i class=\"fas fa-exclamation-circle\"></i>',
                    customClass: {
                        icon: 'swal2-icon swal2-error-icon',
                    },
                    html: '<p style=\"font-size: 24px; font-weight: bold;\">Access Denied! You do not have permission to access this page.</p>',
                    showConfirmButton: false,
                    timer: 3000
                }).then(() => {
                    window.location.href = '../frontend/login.php';
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
                color: #102E47;
            }
        </style>
    </head>
    <body></body>
    </html>";
    exit();
}

$conn = (new Database())->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['review_id'], $_POST['status'])) {
    $review_id = $_POST['review_id'];
    $status = $_POST['status'];
    $stmt = $conn->prepare("UPDATE rev SET status = ? WHERE revid = ?");
    $stmt->execute([$status, $review_id]);
    exit;
}

$statusFilter = $_GET['status'] ?? 'submitted';
$stmt = $conn->prepare("SELECT rev.revid, rev.review, rev.date, rev.status, users.username, sites.sitename FROM rev JOIN users ON rev.userid = users.userid JOIN sites ON rev.siteid = sites.siteid WHERE rev.status = ?");
$stmt->execute([$statusFilter]);
$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Dashboard - Reviews</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;700&family=Raleway:wght@300;400;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        * {
            font-family: 'Nunito', sans-serif;
            box-sizing: border-box;
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 250px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 20px;
            background-color: white;
            z-index: 1000;
            transition: all 0.3s ease-in-out;
        }

        .sidebar img {
            max-width: 100%; 
            height: auto;
            display: block; 
            margin: auto;
            transition: all 0.3s ease-in-out; 
        }

        .menu-section {
            margin-top: auto;
            margin-bottom: auto;
        }

        .nav-link {
            color: #102E47 !important;
            padding: 10px;
            border-radius: 5px;
            transition: background 0.3s ease;
        }

        .nav-link.active {
            background-color: #102E47 !important;
            color: white !important;
            font-weight: bold;
        }

        .nav-link i {
            color: inherit; 
        }

        .nav-link:hover {
            background-color: #102E47 !important; 
            color: white !important;
            transition: background 0.3s ease;
        }

        .content-container {
            background-color: #E7EBEE;
            padding: 20px;
            border-radius: 10px;
        }

        .main-content {
            margin-left: 260px;
            padding: 20px;
            transition: all 0.3s ease-in-out;
            width: calc(100% - 260px); 
        }

        .content-container h2 {
            font-weight: bold;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap; 
            gap: 10px; 
        }

        .date {
            text-align: right; 
            min-width: 150px; 
            flex-shrink: 0; 
        }

        .btn-custom {
            padding: 10px 20px;
            font-size: 16px;
            font-weight: bold;
            border: 2px solid #102E47;
            border-radius: 25px;
            background-color: white;
            color: #102E47;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-right: 10px;
            margin-top: 10px;
            min-width: 80px;
            text-align: center;
        }

        .btn-custom.active {
            background-color: #102E47 !important;
            color: white !important;
            font-weight: bold;
        }

        .btn-custom:hover {
            background-color: #102E47;
            color: white;
        }

        .info-box {
            height: 290px;
            width: 375px;
            background-color: #729AB8;
            border-radius: 10px;
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
            padding: 15px;
            margin-top: 15px;
        }

        .info-box span {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 4px;
        }

        .info-box p {
            text-align: justify;
        }

        .info-box:hover {
            transform: scale(1.03);
            transition: all 0.3s;
        }

        .modal-dialog {
            max-width: 50%;
        }

        .modal-content {
            border-radius: 25px; 
        }

        .modal-footer {
            display: flex;
            justify-content: center;
            gap: 15px;
            padding: 20px;
            border-top: none;
        }

        .modal-title {
            font-size: 24px;
            font-weight: bold;
            color: #434343;
        }

        .modal-body p {
            font-size: 18px;
            text-align: justify;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px; 
        }

        .user-icon {
            font-size: 50px; 
            margin-right: 5px;
            color: #434343;
        }

        .user-text h5 {
            margin: 0;
            margin-bottom: 5px;
            font-size: 16px;
            font-weight: bold;
            color: #434343;
        }

        .user-text p {
            margin: 0;
            font-size: 14px;
        }

        .swal2-icon {
            background: none !important;
            border: none !important;
            box-shadow: none !important;
        }

        .swal2-icon-custom {
            font-size: 10px; 
            color: #102E47; 
        }

        .swal2-title-custom {
            font-size: 24px !important;
            font-weight: bold;
            color: #434343 !important;
        }

        .swal-custom-popup {
            padding: 20px;
            border-radius: 25px;
        }

        .swal-custom-btn {
            padding: 10px 20px !important;
            font-size: 16px !important;
            font-weight: bold !important;
            border: 2px solid #102E47 !important;
            border-radius: 25px !important;
            background-color: white !important;
            color: #102E47 !important;
            cursor: pointer !important;
            transition: all 0.3s ease !important;
        }

        .swal-custom-btn:hover {
            background-color: #102E47 !important;
            color: white !important;
        }

        .no-reviews {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            color: #434343;
            padding: 10px;
            border-radius: 8px;
            width: fit-content;
            margin: 20px auto;
        }

        @media (max-width: 1280px) {
            .btn-custom {
                padding: 8px 16px;
                font-size: 15px;
            }

            .modal-dialog {
                max-width: 60%;
            }

            .info-box {
                height: 100%;
                width: 100%;
            }
        }

        @media (max-width: 912px) {
            .sidebar {
                width: 200px; 
                padding: 15px;
            }

            .nav-link {
                font-size: 14px; 
                padding: 8px; 
            }

            .menu-section {
                margin-top: auto;
                margin-bottom: auto;
                padding: 10px 0; 
            }

            .main-content {
                margin-left: 200px;
                width: calc(100% - 200px);
            }

            .btn-custom {
                padding: 8px 14px;
                font-size: 14px;
                min-width: 70px;
            }

            .modal-dialog {
                max-width: 65%;
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 80px;
                padding: 10px;
            }

            .sidebar img {
                max-width: 60px; 
            }

            .main-content {
                margin-left: 90px; 
                width: calc(100% - 90px); 
            }

            .nav-link {
                text-align: center;
                padding: 10px;
            }

            .nav-link span {
                display: none; 
            }

            .nav-link.active {
                background-color: #102E47 !important;
                color: white !important;
                border-radius: 5px;
            }

            .nav-link:hover {
                background-color: #102E47 !important; 
                color: white !important;
                transition: background 0.3s ease;
            }

            .header {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }

            .date {
                text-align: center; 
                width: 100%; 
            }

            .btn-custom {
                padding: 6px 12px;
                font-size: 14px;
            }

            .modal-dialog {
                max-width: 70%;
            }
        }

        @media (max-width: 600px) {
            .sidebar {
                width: 70px;
                padding: 5px;
            }

            .main-content {
                margin-left: 75px;
                width: calc(100% - 75px);
            }

            .nav-link i {
                font-size: 20px;
            }

            .nav-link span {
                display: none;
            }

            .nav-link:hover {
                background-color: #102E47 !important; 
                color: white !important;
                transition: background 0.3s ease;
            }

            .btn-group {
                flex-direction: column;
                align-items: center;
                display: flex;
                justify-content: center;
            }
            
            .btn-custom {
                width: 90%;
                padding: 8px;
                font-size: 14px;
            }

            .modal-dialog {
                max-width: 100%;
            }

            .info-box {
                padding: 10px;
            }
        }

        @media (max-width: 360px) {
            .btn-custom {
                font-size: 13px;
                padding: 6px;
            }
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="pt-4 pb-1 px-2 text-center">
            <a href="#" class="text-decoration-none">
                <img src="../../../public/assets/images/headerlogo.jpg" alt="Header Logo" class="img-fluid">
            </a>
        </div>
        
        <div class="menu-section">
            <ul class="nav nav-pills flex-column mb-4">
                <li class="nav-item mb-4">
                    <a href="home.php" class="nav-link text-dark">
                        <i class="bi bi-grid"></i>
                        <span class="d-none d-sm-inline">Overview</span>
                    </a>
                </li>
                <li class="nav-item mb-4">
                    <a href="tourrequests.php" class="nav-link text-dark">
                        <i class="bi bi-map"></i>
                        <span class="d-none d-sm-inline">Tour Requests</span>
                    </a>
                </li>
                <li class="nav-item mb-4">
                    <a href="upcomingtourstoday.php" class="nav-link text-dark">
                        <i class="bi bi-geo"></i>
                        <span class="d-none d-sm-inline">Upcoming Tours</span>
                    </a>
                </li>
                <li class="nav-item mb-4">
                    <a href="" class="nav-link active">
                        <i class="bi bi-pencil-square"></i>
                        <span class="d-none d-sm-inline">Reviews</span>
                    </a>
                </li>
                <li class="nav-item mb-4">
                    <a href="touristsites.php" class="nav-link text-dark">
                        <i class="bi bi-image"></i>
                        <span class="d-none d-sm-inline">Tourist Sites</span>
                    </a>
                </li>
            </ul>
        </div>
        
        <ul class="nav nav-pills flex-column mb-4">
            <li class="nav-item mb-3">
                <a href="" class="nav-link active">
                    <i class="bi bi-person-circle"></i>
                    <span class="d-none d-sm-inline">Employee Name</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="" class="nav-link text-dark">
                    <i class="bi bi-box-arrow-right"></i>
                    <span class="d-none d-sm-inline">Sign Out</span>
                </a>
            </li>
        </ul>
    </div>

    <div class="main-content"> 
        <div class="content-container">
            <div class="header">
                <h2>Reviews</h2>
                <span class="date">
                    <h2>
                        <?php 
                            date_default_timezone_set('Asia/Manila');
                            echo date('M d, Y | h:i A'); 
                        ?>
                    </h2>
                </span>
            </div>

            <div class="btn-group" role="group">
                <button type="button" class="btn-custom <?= $statusFilter == 'submitted' ? 'active' : '' ?>" onclick="filterReviews('submitted')">Pending</button>
                <button type="button" class="btn-custom <?= $statusFilter == 'displayed' ? 'active' : '' ?>" onclick="filterReviews('displayed')">Approved</button>
                <button type="button" class="btn-custom <?= $statusFilter == 'archived' ? 'active' : '' ?>" onclick="filterReviews('archived')">Archived</button>
            </div>

            <div class="row mt-3 d-flex justify-content-start">
                <?php if (empty($reviews)): ?>
                    <div class="no-reviews">No reviews available.</div>
                <?php else: ?>
                    <?php foreach ($reviews as $review): ?>
                        <div class="col-lg-4 col-md-6 col-12 mb-3">
                            <div class="info-box" onclick='showModal(<?php echo json_encode($review); ?>)' style="cursor: pointer;">
                                <span><?php echo htmlspecialchars($review['sitename']); ?></span>
                                <br><br>
                                <span><?php echo htmlspecialchars($review['username']); ?></span>
                                <br>
                                <i class="bi bi-star-fill star-icon"></i>
                                <i class="bi bi-star-fill star-icon"></i>
                                <i class="bi bi-star-fill star-icon"></i>
                                <i class="bi bi-star-fill star-icon"></i>
                                <i class="bi bi-star-fill star-icon"></i>
                                <br>
                                <p><?php echo htmlspecialchars($review['review']); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="modal fade" id="reviewsModal" tabindex="-1" aria-labelledby="reviewsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content p-4">
                <div class="modal-header border-0 position-relative">
                    <h5 class="modal-title w-100">Site</h5>
                    <div class="position-absolute end-0 d-flex align-items-center">
                        <i class="bi bi-quote fs-3 text-secondary me-3"></i> 
                        <button type="button" class="btn-close me-3" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                </div>

                <div class="modal-body">
                    <p></p>
                    <div class="user-info">
                        <i class="bi bi-person-circle user-icon"></i>
                        <div class="user-text">
                            <h5>User</h5>
                            <p></p>
                        </div>
                    </div>
                </div>

                <div class="modal-footer" id="modalFooter">
                    <button class="btn-custom" id="displayBtn" onclick="updateReviewStatus('displayed')">Display</button>
                    <button class="btn-custom" id="archiveBtn" onclick="updateReviewStatus('archived')">Archive</button>
                </div>
            </div>
        </div>
    </div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/js/all.min.js"></script>

<script>
    function filterReviews(status) {
        window.location.href = '?status=' + status;
    }

    let currentReviewId;

    function showModal(review) {
        currentReviewId = review.revid;
        document.querySelector(".modal-title").innerText = review.sitename;
        document.querySelector(".modal-body p").innerText = review.review;
        document.querySelector(".user-text h5").innerText = review.username;
        document.querySelector(".user-text p").innerText = review.date;

        const displayBtn = document.getElementById("displayBtn");
        const archiveBtn = document.getElementById("archiveBtn");

        if (review.status === "submitted") {
            displayBtn.style.display = "inline-block";
            archiveBtn.style.display = "inline-block";
        } else {
            displayBtn.style.display = "none";
            archiveBtn.style.display = "none";
        }

        var modal = new bootstrap.Modal(document.getElementById('reviewsModal'));
        modal.show();
    }

    function updateReviewStatus(status) {
        Swal.fire({
            iconHtml: status === 'displayed' ? '<i class="fas fa-thumbs-up"></i>' : '<i class="fas fa-thumbs-down"></i>',
            title: status === 'displayed' ? "Display User Review?" : "Archive User Review?",
            showCancelButton: true,
            confirmButtonText: "Yes",
            cancelButtonText: "No",
            customClass: {
                title: "swal2-title-custom",
                icon: "swal2-icon-custom",
                popup: "swal-custom-popup",
                confirmButton: "swal-custom-btn",
                cancelButton: "swal-custom-btn"
            }
        }).then((result) => {
            if (result.isConfirmed) {
                fetch("reviews.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: "review_id=" + currentReviewId + "&status=" + status
                }).then(() => {
                    Swal.fire({
                        iconHtml: '<i class="fas fa-circle-check"></i>',
                        title: status === 'displayed' ? "Successfully Displayed User Review!" : "Successfully Archived User Review!",
                        timer: 3000,
                        showConfirmButton: false,
                        customClass: {
                            title: "swal2-title-custom",
                            icon: "swal2-icon-custom",
                            popup: "swal-custom-popup"
                        }
                    }).then(() => {
                        window.location.reload();
                    });
                });
            }
        });
    }
</script>
</body>
</html>