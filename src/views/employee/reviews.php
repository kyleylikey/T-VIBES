<?php
include '../../../includes/auth.php';
require_once '../../config/dbconnect.php';

$userid = $_SESSION['userid'];
require_once '../../controllers/reviewscontroller.php';

$query = "SELECT name FROM users WHERE userid = :userid LIMIT 1";
$stmt = $conn->prepare($query);
$stmt->bindParam(':userid', $userid);
$stmt->execute();
$employee = $stmt->fetch(PDO::FETCH_ASSOC);

$employeeName = $employee ? htmlspecialchars($employee['name']) : "Employee";
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
            box-sizing: border-box;
        }
        
        .sidebar {
            font-family: 'Raleway', sans-serif;
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
            color: #434343 !important;
            padding: 10px;
            border-radius: 5px;
            font-weight: bold; 
            transition: background 0.3s ease, color 0.3s ease;
        }

        .nav-link.active {
            background-color: #EC6350 !important;
            color: #FFFFFF !important;
            font-weight: bold;
        }

        .nav-link:hover {
            background-color: #EC6350 !important; 
            color: #FFFFFF !important;
        }

        .nav-link i {
            color: inherit; 
        }
        
        .employee-name.active {
            background-color: #102E47 !important;
            color: #FFFFFF !important;
            font-weight: bold;
        }

        .sign-out.active {
            background-color: #E7EBEE !important;
            color: #102E47 !important;
            font-weight: bold;
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

        .content-container h2, 
        .content-container .date h2 {
            color: #102E47;
            font-weight: bold;
        }

        .header {
            font-family: 'Raleway', sans-serif;
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
            background-color: #FFFFFF;
            color: #434343;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-right: 10px;
            margin-top: 10px;
            min-width: 80px;
            text-align: center;
            font-family: 'Nunito', sans-serif !important;
        }

        .btn-custom.active {
            background-color: #102E47 !important;
            color: #FFFFFF !important;
            font-weight: bold;
        }

        .btn-custom:hover {
            background-color: #102E47;
            color: #FFFFFF;
            font-weight: bold;
        }

        .info-box {
            position: relative;
            min-height: 150px;
            min-width: 200px; 
            background-color: rgba(114, 154, 184, 0.2); 
            border-radius: 10px;
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
            padding: 15px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: flex-start;
            text-align: left;
            font-family: 'Nunito', sans-serif;
        }

        .table-responsive {
            overflow-x: auto;
            width: 100%;
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 10px;
            margin: auto;
            text-align: center;
            overflow-x: auto;
            min-width: 500px;
            font-family: 'Nunito', sans-serif;
            table-layout: fixed;
        }

        thead th {
            color: #434343;
            font-weight: bold;
            padding-bottom: 10px;
            background: none !important;
            border: none !important;
            box-shadow: none !important;
            vertical-align: middle;
        }

        tbody tr {
            background: #FFFFFF;
            border-radius: 15px;
            box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1);
        }

        tbody tr td {
            padding: 10px;
            border: none;
            color: #434343;
            vertical-align: middle;
        }

        tbody tr td:first-child {
            border-top-left-radius: 15px;
            border-bottom-left-radius: 15px;
        }

        tbody tr td:last-child {
            border-top-right-radius: 15px;
            border-bottom-right-radius: 15px;
        }

        .table th,
        .table td {
            white-space: nowrap; 
            overflow: hidden;
            text-overflow: ellipsis; 
        }

        .table th:nth-child(1), 
        .table td:nth-child(1) {
            width: 5%; 
        }

        .table th:nth-child(2), 
        .table td:nth-child(2) {
            width: 30%; 
        }

        .table th:nth-child(3), 
        .table td:nth-child(3) {
            width: 20%; 
        }

        .table th:nth-child(4), 
        .table td:nth-child(4) {
            width: 15%; 
        }

        .table th:nth-child(5), 
        .table td:nth-child(5) {
            width: 35%; 
        }

        .table th:nth-child(6), 
        .table td:nth-child(6) {
            width: 10%; 
        }

        .table th:nth-child(7), 
        .table td:nth-child(7) {
            width: 15%; 
        }

        .btn-action {
            display: block;
            width: 100px; 
            margin: 5px auto;
            padding: 8px 0;
            text-align: center;
            font-size: 14px;
            border-radius: 20px;
            cursor: pointer;
            background-color: #FFFFFF;
            font-weight: bold;
            font-family: 'Nunito', sans-serif !important;
        }

        .btn-view {
            border: 2px solid #102E47;
            color: #102E47;
        }

        .btn-view:hover {
            background-color: #102E47;
            color: #FFFFFF;
        }

        .btn-display {
            border: 2px solid #28a745;
            color: #28a745;
        }

        .btn-display:hover {
            background-color: #28a745;
            color: #FFFFFF;
        }

        .btn-archive {
            border: 2px solid #EC6350;
            color: #EC6350;
        }

        .btn-archive:hover {
            background-color: #EC6350;
            color: #FFFFFF;
        }

        .btn-group {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }

        .filter-search-container {
            display: flex;
            align-items: center;
            margin-left: auto;
            gap: 10px;
        }

        .btn-filter {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: white;
            border: 2px solid #102E47;
            color: #434343;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-filter:hover {
            background-color: #102E47;
            color: white;
        }

        .search-container {
            position: relative;
            width: 250px;
        }

        .search-input {
            width: 100%;
            padding: 10px 40px 10px 15px;
            border: 2px solid #102E47;
            border-radius: 25px;
            font-family: 'Nunito', sans-serif;
            font-size: 14px;
            color: #434343;
        }

        .search-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #434343;
            pointer-events: none;
        }

        .modal-icon {
            z-index: 1;
        }

        .review-truncate {
            max-width: 200px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .modal-dialog {
            max-width: 50%;
        }

        .modal-content {
            border-radius: 25px;
            font-family: 'Nunito', sans-serif !important; 
            background-color: #E7EBEE;
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
            color: #102E47;
        }

        .modal-icon i {
            color: #729AB8;
        }

        .modal-body p {
            font-size: 18px;
            text-align: justify;
            color: #757575;
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
            color: #757575;
        }

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
            font-family: 'Nunito', sans-serif !important;
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

        .no-reviews {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            color: #434343;
            padding: 10px;
            border-radius: 8px;
            width: fit-content;
            margin: 20px auto;
            font-family: 'Nunito', sans-serif !important;
        }

        .no-filter-search {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            color: #434343;
            padding: 10px;
            border-radius: 8px;
            width: fit-content;
            margin: 20px auto;
            font-family: 'Nunito', sans-serif !important;
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

            .table {
                table-layout: auto;
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

            .nav-link.active {
                background-color: #EC6350 !important;
                color: #FFFFFF !important;
                font-weight: bold;
            }

            .nav-link:hover {
                background-color: #EC6350 !important; 
                color: #FFFFFF !important;
            }

            .employee-name.active {
                background-color: #102E47 !important;
                color: #FFFFFF !important;
                font-weight: bold;
            }

            .sign-out.active {
                background-color: #E7EBEE !important;
                color: #102E47 !important;
                font-weight: bold;
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

            .filter-search-container {
                width: 100%;
                margin-top: 10px;
                justify-content: center;
            }
            
            .search-container {
                width: 100%;
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
                background-color: #EC6350 !important;
                color: #FFFFFF !important;
                font-weight: bold;
            }

            .nav-link:hover {
                background-color: #EC6350 !important; 
                color: #FFFFFF !important;
            }

            .employee-name.active {
                background-color: #102E47 !important;
                color: #FFFFFF !important;
                font-weight: bold;
            }

            .sign-out.active {
                background-color: #E7EBEE !important;
                color: #102E47 !important;
                font-weight: bold;
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

            .row {
                justify-content: center !important;
            }

            .btn-custom {
                padding: 6px 12px;
                font-size: 14px;
            }

            .info-box {
                width: 100% !important; 
                text-align: center;
                align-items: center;
                margin: 0 auto; 
            }

            .info-box i {
                align-self: center;
            }

            .table-responsive {
                overflow-x: auto; 
            }

            table {
                font-size: 14px; 
            }

            thead th, tbody td {
                white-space: nowrap; 
                padding: 5px; 
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

            .nav-link.active {
                background-color: #EC6350 !important;
                color: #FFFFFF !important;
                font-weight: bold;
            }

            .nav-link:hover {
                background-color: #EC6350 !important; 
                color: #FFFFFF !important;
            }

            .employee-name.active {
                background-color: #102E47 !important;
                color: #FFFFFF !important;
                font-weight: bold;
            }

            .sign-out.active {
                background-color: #E7EBEE !important;
                color: #102E47 !important;
                font-weight: bold;
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

            .info-box {
                min-height: 120px;
                min-width: auto;
                padding: 10px;
            }

            .info-box i {
                font-size: 30px;
            }

            table {
                font-size: 12px;
                overflow-x: auto;
            }

            tbody tr td {
                padding: 5px; 
            }

            .modal-dialog {
                max-width: 100%;
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
                    <a href="home.php" class="nav-link">
                        <i class="bi bi-grid"></i>
                        <span class="d-none d-sm-inline">Dashboard</span>
                    </a>
                </li>
                <li class="nav-item mb-4">
                    <a href="tourrequests.php" class="nav-link">
                        <i class="bi bi-map"></i>
                        <span class="d-none d-sm-inline">Tour Requests</span>
                    </a>
                </li>
                <li class="nav-item mb-4">
                    <a href="upcomingtours.php" class="nav-link">
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
                    <a href="touristsites.php" class="nav-link">
                        <i class="bi bi-image"></i>
                        <span class="d-none d-sm-inline">Tourist Sites</span>
                    </a>
                </li>
            </ul>
        </div>
        
        <ul class="nav nav-pills flex-column mb-4">
            <li class="nav-item mb-3">
                <a href="javascript:void(0);" class="nav-link employee-name active">
                    <i class="bi bi-person-circle"></i>
                    <span class="d-none d-sm-inline"><?= $employeeName; ?></span>
                </a>
            </li>
            <li class="nav-item">
                <a href="javascript:void(0);" class="nav-link sign-out active" onclick="logoutConfirm()">
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
                <button type="button" class="btn-custom <?= $statusFilter == 'displayed' ? 'active' : '' ?>" onclick="filterReviews('displayed')">Displayed</button>
                <button type="button" class="btn-custom <?= $statusFilter == 'archived' ? 'active' : '' ?>" onclick="filterReviews('archived')">Archived</button>
                
                <div class="filter-search-container">
                    <button type="button" class="btn-filter" id="sortButton">
                        <i class="fas fa-filter"></i>
                    </button>
                    <div class="search-container">
                        <input type="text" class="search-input" id="searchInput" placeholder="Search">
                        <i class="fas fa-search search-icon"></i>
                    </div>
                </div>
            </div>

            <div class="row mt-3 d-flex justify-content-center">
                <div class="col-lg-12 col-md-12 col-12 mb-3">
                    <?php if (empty($reviews)): ?>
                        <div class="no-reviews">No reviews available.</div>
                    <?php else: ?>
                        <div class="info-box">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Tour Site/s</th>
                                            <th>Author</th>
                                            <th>Submitted On</th>
                                            <th>Review</th>
                                            <th>Rate</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $rowNumber = 1;
                                        foreach ($reviews as $review): 
                                        ?>
                                        <tr>
                                            <td><?= $rowNumber ?></td>
                                            <td><?= htmlspecialchars($review['sitename']) ?></td>
                                            <td><?= htmlspecialchars($review['username']) ?></td>
                                            <td><?= date('d M Y', strtotime($review['date'])) ?></td>
                                            <td class="review-truncate"><?= htmlspecialchars($review['review']) ?></td>
                                            <td>
                                                <?php if (isset($review['rating'])): ?>
                                                    <?= htmlspecialchars($review['rating']) ?>
                                                <?php else: ?>
                                                    N/A
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <button class="btn-action btn-view" onclick='showModal(<?php echo json_encode($review); ?>)'>
                                                    <i class="fas fa-eye"></i> View
                                                </button>
                                                <?php if ($review['status'] == 'submitted'): ?>
                                                <button class="btn-action btn-display" onclick="updateReviewStatus('displayed', <?= $review['revid'] ?>)">
                                                    <i class="fas fa-check"></i> Display
                                                </button>
                                                <button class="btn-action btn-archive" onclick="updateReviewStatus('archived', <?= $review['revid'] ?>)">
                                                    <i class="fas fa-archive"></i> Archive
                                                </button>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php 
                                            $rowNumber++;
                                            endforeach; 
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="reviewsModal" tabindex="-1" aria-labelledby="reviewsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content p-4">
                <div class="modal-header border-0 position-relative">
                    <h5 class="modal-title w-100">Site</h5>
                    <div class="modal-icon position-absolute end-0 d-flex align-items-center">
                        <i class="bi bi-quote fs-3 me-3"></i> 
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
            </div>
        </div>
    </div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/js/all.min.js"></script>
<script src="../../../public/assets/scripts/main.js"></script>
<script src="../../../public/assets/scripts/empreviews.js"></script>
<script>
    function showModal(review) {
        currentReviewId = review.revid;
        document.querySelector(".modal-title").innerText = review.sitename;
        document.querySelector(".modal-body p").innerText = review.review;
        document.querySelector(".user-text h5").innerText = review.username;
        
        let dateObj = new Date(review.date);
        let day = String(dateObj.getDate()).padStart(2, '0');
        let month = dateObj.toLocaleString('en-US', { month: 'short' });
        let year = dateObj.getFullYear();
        let hours = dateObj.getHours();
        let minutes = String(dateObj.getMinutes()).padStart(2, '0');
        let ampm = hours >= 12 ? 'PM' : 'AM';
        hours = hours % 12;
        hours = hours ? hours : 12;
        
        let formattedDate = `${day} ${month} ${year} | ${hours}:${minutes} ${ampm}`;
        document.querySelector(".user-text p").innerText = formattedDate;

        var modal = new bootstrap.Modal(document.getElementById('reviewsModal'));
        modal.show();
    }
</script>
</body>
</html>