<?php
include '../../../../includes/auth.php';
require_once '../../../config/dbconnect.php';

// Check if user is logged in
if(!isset($_SESSION['userid'])) {
    header("Location: ../../../auth/login.php");
    exit;
}

$userid = $_SESSION['userid']; 

// Handle POST request for ratings separately
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the site ID and rating from the form data
    $siteId = $_POST['siteId'] ?? null;
    $rating = $_POST['rating'] ?? null;
    
    // Validate inputs
    if(!$siteId || !$rating || !is_numeric($siteId) || !is_numeric($rating) || $rating < 1 || $rating > 5) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
        exit;
    }
    
    try {
        // Initialize database connection
        $database = new Database();
        $conn = $database->getConnection();
        
        // Check if user has already rated this site
        $checkQuery = "SELECT id FROM user_ratings WHERE user_id = :user_id AND site_id = :site_id";
        $checkStmt = $conn->prepare($checkQuery);
        $checkStmt->bindParam(':user_id', $userid, PDO::PARAM_INT);
        $checkStmt->bindParam(':site_id', $siteId, PDO::PARAM_INT);
        $checkStmt->execute();
        
        if($checkStmt->rowCount() > 0) {
            // User has already rated this site
            echo json_encode(['status' => 'error', 'message' => 'You have already rated this site', 'alreadyRated' => true]);
            exit;
        }
        
        // Begin transaction
        $conn->beginTransaction();
        
        // Insert new rating into user_ratings table
        $insertQuery = "INSERT INTO user_ratings (user_id, site_id, rating) VALUES (:user_id, :site_id, :rating)";
        $insertStmt = $conn->prepare($insertQuery);
        $insertStmt->bindParam(':user_id', $userid, PDO::PARAM_INT);
        $insertStmt->bindParam(':site_id', $siteId, PDO::PARAM_INT);
        $insertStmt->bindParam(':rating', $rating, PDO::PARAM_INT);
        $insertResult = $insertStmt->execute();
        
        // Update site rating
        $updateQuery = "UPDATE sites SET rating = rating + :rating, rating_cnt = rating_cnt + 1 WHERE siteid = :siteid";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bindParam(':rating', $rating, PDO::PARAM_INT);
        $updateStmt->bindParam(':siteid', $siteId, PDO::PARAM_INT);
        $updateResult = $updateStmt->execute();
        
        if($insertResult && $updateResult) {
            $conn->commit();
            echo json_encode(['status' => 'success', 'message' => 'Rating submitted successfully']);
        } else {
            $conn->rollBack();
            echo json_encode(['status' => 'error', 'message' => 'Failed to submit rating']);
        }
    } catch(PDOException $e) {
        if(isset($conn) && $conn->inTransaction()) {
            $conn->rollBack();
        }
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    }
    exit; // Important: stop execution after responding to POST
}

// Continue with page rendering for GET requests
$database = new Database();
$conn = $database->getConnection();

// Fetch all sites the user has already rated
$ratedSitesQuery = "SELECT site_id FROM user_ratings WHERE user_id = :user_id";
$ratedSitesStmt = $conn->prepare($ratedSitesQuery);
$ratedSitesStmt->bindParam(':user_id', $userid, PDO::PARAM_INT);
$ratedSitesStmt->execute();
$ratedSites = $ratedSitesStmt->fetchAll(PDO::FETCH_COLUMN);

// Query to get completed tours (accepted status with past dates)
// MODIFIED: Group by tourid to get unique tours
$query = "SELECT t.tourid, t.date, t.status, t.created_at, t.companions 
          FROM tour t
          WHERE t.userid = ? AND t.status = 'accepted' AND t.date < CURDATE()
          GROUP BY t.tourid
          ORDER BY t.created_at DESC";

$stmt = $conn->prepare($query);
$stmt->bindParam(1, $userid);
$stmt->execute();
$completedTours = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Tour History - Taal Heritage Town</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;700&family=Raleway:wght@300;400;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9fafb;
            color: #333;
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

        /* Table Container Styling */
        .table-container {
            margin-top: 30px;
            max-width: 1140px;
            margin-left: auto;
            margin-right: auto;
            padding: 0 15px;
        }

        /* Table Header Styling */
        .table-header {
            background-color: #102E47;
            color: #FFFFFF;
            font-family: 'Raleway', sans-serif;
            font-weight: 700;
            font-size: 16px;
            border-radius: 10px 10px 0 0;
            padding: 15px 20px;
            margin-bottom: 0;
            letter-spacing: 0.5px;
        }

        /* Table Content Container */
        .table-content {
            border-radius: 0 0 10px 10px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            margin-bottom: 30px;
        }

        /* Tour Row Styling - History Style */
        .tour-row {
            background-color: #E7EBEE;
            padding: 18px 20px;
            margin-bottom: 0;
            border-left: 4px solid #F1C40F; /* Yellow border for history tours */
            transition: all 0.3s ease;
            border-bottom: 1px solid rgba(241, 196, 15, 0.15);
        }

        .tour-row:hover {
            background-color: #f2f0e8; /* Slight yellow tint on hover */
            transform: translateX(3px);
            cursor: pointer;
        }

        .tour-row:last-child {
            border-bottom: none;
            border-radius: 0 0 10px 10px;
        }

        /* Tour Row Column Styling */
        .tour-row .col-3, 
        .tour-row .col-2, 
        .tour-row .col-4 {
            padding: 8px 15px;
            font-family: 'Nunito', sans-serif;
            font-size: 15px;
            color: #434343;
        }

        /* Date Styling */
        .tour-date {
            font-weight: 600;
            color: #102E47;
        }

        /* Number of People Styling */
        .people-count {
            display: inline-block;
            background-color: #F1C40F; /* Yellow for completed */
            color: #102E47;
            border-radius: 50px;
            padding: 3px 15px;
            font-weight: 600;
            text-align: center;
            min-width: 45px;
        }

        /* Destinations List Styling */
        .destinations-list {
            padding-left: 0;
            list-style-type: none;
            margin-bottom: 0;
        }

        .destinations-list li {
            position: relative;
            padding-left: 18px;
            margin-bottom: 5px;
            font-size: 14px;
            color: #757575;
            text-decoration: line-through; /* Strikethrough to show completed */
            opacity: 0.9;
        }

        .destinations-list li:before {
            content: "★"; /* Star for history tours */
            position: absolute;
            left: 0;
            top: 0;
            color: #F1C40F;
            font-weight: bold;
            font-size: 14px;
            text-decoration: none;
        }

        .destinations-list li:last-child {
            margin-bottom: 0;
        }

        .modal-title {
            font-family: 'Raleway', sans-serif !important;
            color: #102E47;
            font-weight: bold;
            font-size: 24px;
        }

        .modal-content {
            padding: 20px;
            border-radius: 25px !important;
        }

        .destination-card {
            background-color: #EDF1F5;
            padding: 12px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            margin-bottom: 12px;
            gap: 12px;
        }

        .destination-image {
            width: 50px;
            height: 50px;
            border-radius: 8px;
            background-color: #D9E2EC;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .destination-image i {
            font-size: 30px;
            color: #7d7d7d;
        }

        .destination-image img {
            width: 50px !important;
            height: 50px !important;
            object-fit: cover;
            border-radius: 8px;
        }

        .destination-details {
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 4px;
            flex-grow: 1;
        }

        .destination-name {
            color: #102E47;
            font-weight: bold;
            font-size: 18px;
            margin-bottom: 4px;
        }
		
		.label-bold {
			font-weight: bold;
			color: #102E47;
		}

		.value-grey {
			color: #7d7d7d;
		}

		.rate-review-container {
            display: flex;
            gap: 10px;
        }

        .rate-btn, .review-btn {
            background-color: #EC6350;
            color: #fff;
            border: none;
            padding: 8px 18px;
            border-radius: 30px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s;
            flex: 1; 
            text-align: center;
        }

        .rate-btn:hover, .review-btn:hover {
            background-color: #e03e26;
        }

		.review-btn {
			background-color: #EC6350;
			color: #fff;
			border: none;
			padding: 6px 18px;
			border-radius: 30px;
			font-size: 14px;
			font-weight: bold;
			cursor: pointer;
			transition: background-color 0.3s;
			margin-left: 5px;
		}

		.review-btn:hover {
			background-color: #e03e26;
		}

        .stars .bi {
            font-size: 36px;
            cursor: pointer;
            color: #ccc;
            transition: color 0.2s ease;
        }

        .stars .bi.active {
            color: #EC6350;
        }

        #submitRating {
            background-color: #EC6350;
            color: #fff;
            border: none;
            padding: 10px 24px;
            border-radius: 30px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 10px;
            transition: background-color 0.3s;
        }

        #submitRating:hover {
            background-color: #e03e26;
        }

        .status-pending {
            color: #E74C3C;
            font-weight: bold;
            margin-top: 12px;
        }
		.rate-btn:hover, .review-btn:hover {
            background-color: #e03e26;
        }

        .btn-custom {
            border: 2px solid #EC6350 !important;
            color: #EC6350 !important;
            border-radius: 25px !important;
            padding: 5px 10px !important;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease-in-out;
            font-weight: bold !important;
            font-family: 'Nunito', sans-serif !important;
        }

        .btn-custom:hover {
            background-color: #EC6350;
            color: #FFFFFF !important;
        }
        
        /* Style for disabled buttons */
        .rate-btn.disabled {
            background-color: #cccccc !important;
            color: #666666 !important;
            cursor: not-allowed !important;
            border-color: #cccccc !important;
        }
        
        .rate-btn.disabled:hover {
            background-color: #cccccc !important;
            color: #666666 !important;
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
    </style>
</head>
<body>
<?php include '../../templates/headertours.php'; ?>
<?php include '../../templates/toursnav.php'; ?>

<div class="container table-container">
    <div class="table-content">
        <div class="row table-header">
            <div class="col-3" style="font-family: 'Raleway', sans-serif !important;">Date Created</div>
            <div class="col-3" style="font-family: 'Raleway', sans-serif !important;">Planned Date/s</div>
            <div class="col-3" style="font-family: 'Raleway', sans-serif !important;">Number of People</div>
            <div class="col-3" style="font-family: 'Raleway', sans-serif !important;">Destinations</div>
        </div>

        <?php if (!empty($completedTours)) : ?>
            <?php foreach ($completedTours as $tour) : ?>
                <div class="row align-items-center tour-row" data-bs-toggle="modal" data-bs-target="#tourModal<?= $tour['tourid'] ?>">
                    <div class="col-3">
                        <span class="tour-date"><?= date('d M Y', strtotime($tour['created_at'])) ?></span>
                    </div>
                    <div class="col-3">
                        <span class="tour-date"><?= date('d M Y', strtotime($tour['date'])) ?></span>
                    </div>
                    <div class="col-3">
                        <span class="people-count"><?= $tour['companions'] ?></span>
                    </div>
                    <div class="col-3">
                        <ul class="destinations-list">
                            <?php  
                            // Fetch sites for this tour
                            $siteQuery = "SELECT s.siteid, s.sitename FROM sites s 
                                         JOIN tour t ON s.siteid = t.siteid 
                                         WHERE t.tourid = ? AND t.userid = ?";
                            $siteStmt = $conn->prepare($siteQuery);
                            $siteStmt->bindParam(1, $tour['tourid']);
                            $siteStmt->bindParam(2, $userid);
                            $siteStmt->execute();
                            $tourSites = $siteStmt->fetchAll(PDO::FETCH_ASSOC);
                            
                            foreach ($tourSites as $site) :  
                            ?>
                                <li><?= $site['sitename'] ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else : ?>
            <div class="empty-state">
                <i class="bi bi-journal-check"></i>
                <h4>No Tour History Yet</h4>
                <p>You haven't completed any tours yet. Once your tours are completed, they will appear here.</p>
            </div>
        <?php endif; ?>
    </div>

    <?php foreach ($completedTours as $tour) : ?>
        <div class="modal fade" id="tourModal<?= $tour['tourid'] ?>" tabindex="-1" aria-labelledby="modalLabel<?= $tour['tourid'] ?>" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalLabel<?= $tour['tourid'] ?>">Tour Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <?php 
                                // Fetch sites for this tour with more details
                                $siteDetailsQuery = "SELECT s.siteid, s.sitename, s.siteimage, s.price 
                                                   FROM sites s 
                                                   JOIN tour t ON s.siteid = t.siteid 
                                                   WHERE t.tourid = ? AND t.userid = ?";
                                $siteDetailsStmt = $conn->prepare($siteDetailsQuery);
                                $siteDetailsStmt->bindParam(1, $tour['tourid']);
                                $siteDetailsStmt->bindParam(2, $userid);
                                $siteDetailsStmt->execute();
                                $tourSiteDetails = $siteDetailsStmt->fetchAll(PDO::FETCH_ASSOC);
                                
                                foreach ($tourSiteDetails as $site) : 
                                    $isRated = in_array($site['siteid'], $ratedSites);
                                ?>
                                    <div class="destination-card d-flex align-items-center p-2 mb-2" style="background: #f8f9fa; border-radius: 8px;" data-site-id="<?= $site['siteid'] ?>">
                                        <div class="destination-image me-2">
                                            <?php if (!empty($site['siteimage'])): ?>
                                                <img src="../../../../public/uploads/<?= $site['siteimage'] ?>" alt="<?= $site['sitename'] ?>" class="img-fluid" style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px;">
                                            <?php else: ?>
                                                <i class="bi bi-image"></i>
                                            <?php endif; ?>
                                        </div>
                                        <div class="destination-details flex-grow-1">
                                            <div class="destination-name fw-bold"><?= $site['sitename'] ?></div>
                                            <div class="rate-review-container mt-1">
                                                <button class="btn btn-custom btn-sm rate-btn <?= $isRated ? 'disabled' : '' ?>" 
                                                        <?= $isRated ? 'disabled' : '' ?> 
                                                        style="<?= $isRated ? 'background-color: #cccccc !important; color: #666666 !important; cursor: not-allowed;' : '' ?>">
                                                    <?= $isRated ? 'Rated' : 'Rate' ?>
                                                </button>
                                                <button class="btn btn-custom btn-sm review-btn">Review</button>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <span class="label-bold">Date Created:</span><br>
                                    <span class="value-grey"><?= date('M j, Y', strtotime($tour['created_at'])) ?></span>
                                </div>
                                <div class="mb-3">
                                    <span class="label-bold">Planned Date:</span><br>
                                    <span class="value-grey"><?= date('M j, Y', strtotime($tour['date'])) ?></span>
                                </div>
                                <div class="mb-3">
                                    <span class="label-bold">Number of People:</span><br>
                                    <span class="value-grey"><?= $tour['companions'] ?></span>
                                </div>
                                <div class="mb-3">
                                    <span class="label-bold">Estimated Fees:</span><br>
                                    <div class="value-grey">
                                        <?php 
                                        $total_per_person = 0;
                                        foreach ($tourSiteDetails as $site) : 
                                            $fee_per_destination = (int) $site['price'];
                                            $total_per_person += $fee_per_destination;
                                        ?>
                                            <div class="d-flex justify-content-between">
                                                <span><?= $site['sitename'] ?></span>
                                                <span>₱<?= number_format($fee_per_destination, 2) ?></span>                                       
                                            </div>
                                        <?php endforeach; ?>
                                        <div>
                                            <div class="row">
                                                <div class="col-8"></div>
                                                <div class="fw-bold" style="text-align: right;">₱<?= number_format($total_per_person, 2) ?></div>                 
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <span class="label-bold">Total Fees:</span><br>
                                    <div class="value-grey d-flex justify-content-between fw-bold">
                                        <span>₱<?= number_format($total_per_person, 2) ?> x <?= $tour['companions'] ?></span>
                                        <span style="color: #EC6350;">₱<?= number_format($total_per_person * $tour['companions'], 2) ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="status-completed fw-bold mt-2 text-warning">Status: <span class="text-warning">Completed</span></div>
                        <div class="summary-value fw-light text-center">*Fee is only an estimate and subject to change if the destination can accommodate special discounts.</div>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Rating Modal -->
<div class="modal fade" id="rateExperienceModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content p-4 text-center">
            <h5 class="mb-3 fw-bold" style="font-family: 'Raleway', sans-serif !important; color: #102E47;">Rate Your Experience</h5>
            <div class="stars mb-3">
                <?php for ($i = 1; $i <= 5; $i++) : ?>
                    <i class="bi bi-star" data-value="<?= $i ?>"></i>
                <?php endfor; ?>
            </div>
            <button id="submitRating">Submit</button>
        </div>
    </div>
</div>

<!-- Review Modal -->
<div class="modal fade" id="reviewModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content p-4 text-center">
            <h5 class="modal-title mb-3 fw-bold" style="font-family: 'Raleway', sans-serif !important; color: #102E47;">Leave a Review</h5>
            <div class="form-group mb-3">
                <textarea id="reviewText" class="form-control" rows="4" placeholder="Share your experience..."></textarea>
            </div>
            
            <button id="submitReview" class="btn" style="background-color: #EC6350; color: #fff; border-radius: 30px; padding: 10px 24px; font-weight: bold;">Submit Review</button>
        </div>
    </div>
</div>

<script>
    let selectedRating = 0;
    
    function openReviewModal(siteId) {
        const modal = new bootstrap.Modal(document.getElementById('reviewModal'));
        
        // Store the siteId in the modal for later use
        document.getElementById('reviewModal').dataset.siteId = siteId;
        
        modal.show();

        // Clear previous selection and text
        document.getElementById('reviewText').value = '';
    }

    function submitReview(siteId, reviewText) {
        // Create form data
        const formData = new FormData();
        formData.append('siteId', siteId);
        formData.append('review', reviewText);
        
        // Close the modal first
        const modal = bootstrap.Modal.getInstance(document.getElementById('reviewModal'));
        modal.hide();
        
        // Show success message (replace this with actual API call)
        Swal.fire({
            iconHtml: '<i class="fas fa-circle-check"></i>',
            title: "Thank you!",
            text: "Your review has been submitted successfully.",
            timer: 3000,
            showConfirmButton: false,
            customClass: {
                title: "swal2-title-custom",
                icon: "swal2-icon-custom",
                popup: "swal-custom-popup"
            }
        });
        
        // In a real implementation, you would add an AJAX call here
        // fetch('submit_review.php', {
        //     method: 'POST',
        //     body: formData
        // })
        // .then(response => response.json())
        // .then(data => {
        //     // Show success message
        //     Swal.fire({
        //         title: "Thank you!",
        //         text: "Your review has been submitted successfully.",
        //         icon: "success",
        //         confirmButtonColor: "#EC6350"
        //     });
        // })
        // .catch(error => {
        //     console.error('Error:', error);
        //     Swal.fire({
        //         title: "Error",
        //         text: "There was a problem submitting your review. Please try again.",
        //         icon: "error",
        //         confirmButtonColor: "#EC6350"
        //     });
        // });
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Add event listeners to all Rate buttons
        document.querySelectorAll('.rate-btn').forEach(button => {
            if (!button.classList.contains('disabled')) {
                button.addEventListener('click', function(event) {
                    event.stopPropagation(); // Prevent modal from closing
                    
                    // Get the parent destination-card element that has the data-site-id
                    const destinationCard = this.closest('.destination-card');
                    const siteId = destinationCard.dataset.siteId;
                    
                    openRateModal(siteId);
                });
            }
        });
        
        // Add event listeners to all Review buttons
        document.querySelectorAll('.review-btn').forEach(button => {
            button.addEventListener('click', function(event) {
                event.stopPropagation(); // Prevent modal from closing
                
                // Get the parent destination-card element that has the data-site-id
                const destinationCard = this.closest('.destination-card');
                const siteId = destinationCard.dataset.siteId;
                
                openReviewModal(siteId);
            });
        });

        // Set up the star click events once (not every time the modal opens)
        document.querySelectorAll('.stars .bi').forEach(star => {
            star.addEventListener('click', function() {
                selectedRating = parseInt(this.getAttribute('data-value'));
                document.querySelectorAll('.stars .bi').forEach((s, i) => {
                    if (i < selectedRating) {
                        s.classList.add('active');
                    } else {
                        s.classList.remove('active');
                    }
                });
            });
        });
        
        // Set up the submit review button click event
        document.getElementById('submitReview').addEventListener('click', function() {
            const reviewText = document.getElementById('reviewText').value.trim();
            const siteId = document.getElementById('reviewModal').dataset.siteId;
            
            if (reviewText === '') {
                Swal.fire({
                    iconHtml: '<i class="fas fa-exclamation-circle"></i>',
                    title: "Please write a review before submitting.",
                    timer: 3000,
                    showConfirmButton: false,
                    customClass: {
                        title: "swal2-title-custom",
                        icon: "swal2-icon-custom",
                        popup: "swal-custom-popup"
                    }
                });
                return;
            }
            
            submitReview(siteId, reviewText);
        });
    });

    // Modified openRateModal function to just open the modal and store the siteId
    function openRateModal(siteId) {
        const modal = new bootstrap.Modal(document.getElementById('rateExperienceModal'));
        
        // Store the siteId in the modal for later use
        document.getElementById('rateExperienceModal').dataset.siteId = siteId;
        
        // Reset the stars
        selectedRating = 0;
        document.querySelectorAll('.stars .bi').forEach(star => {
            star.classList.remove('active');
        });
        
        modal.show();
    }
    
    // Update the submit rating function to send rating to the server
    document.getElementById('submitRating').addEventListener('click', function() {
        if (selectedRating > 0) {
            const siteId = document.getElementById('rateExperienceModal').dataset.siteId;
            
            // Send the rating to the server
            submitRating(siteId, selectedRating);
        } else {
            // Display error if no rating is selected
            Swal.fire({
                iconHtml: '<i class="fas fa-exclamation-circle"></i>',
                title: "Please select a rating before submitting.",
                timer: 3000,
                showConfirmButton: false,
                customClass: {
                    title: "swal2-title-custom",
                    icon: "swal2-icon-custom",
                    popup: "swal-custom-popup"
                }
            });
        }
    });

    function submitRating(siteId, rating) {
        // Create form data
        const formData = new FormData();
        formData.append('siteId', siteId);
        formData.append('rating', rating);
        
        // Send AJAX request to the current page
        fetch(window.location.href, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('rateExperienceModal'));
            modal.hide();
            
            if (data.status === 'error' && data.alreadyRated) {
                // User has already rated this site
                Swal.fire({
                    iconHtml: '<i class="fas fa-exclamation-circle"></i>',
                    title: "You have already rated this site.",
                    timer: 3000,
                    showConfirmButton: false,
                    customClass: {
                        title: "swal2-title-custom",
                        icon: "swal2-icon-custom",
                        popup: "swal-custom-popup"
                    }
                });
                
                // Disable the rate button for this site
                disableRateButtonBySiteId(siteId);
            } else if (data.status === 'success') {
                // Show success message
                Swal.fire({
                    iconHtml: '<i class="fas fa-circle-check"></i>',
                    title: "Thank you for your rating!",
                    text: `You rated ${rating} star(s).`,
                    timer: 3000,
                    showConfirmButton: false,
                    customClass: {
                        title: "swal2-title-custom",
                        icon: "swal2-icon-custom",
                        popup: "swal-custom-popup"
                    }
                });
                
                // Disable the rate button for this site
                disableRateButtonBySiteId(siteId);
            } else {
                // Show error
                Swal.fire({
                    iconHtml: '<i class="fas fa-exclamation-circle"></i>',
                    title: "Error",
                    text: data.message || "There was a problem submitting your rating.",
                    timer: 3000,
                    showConfirmButton: false,
                    customClass: {
                        title: "swal2-title-custom",
                        icon: "swal2-icon-custom",
                        popup: "swal-custom-popup"
                    }
                });
            }
            
            // Reset the stars after submission attempt
            selectedRating = 0;
            document.querySelectorAll('.stars .bi').forEach(star => {
                star.classList.remove('active');
            });
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                iconHtml: '<i class="fas fa-exclamation-circle"></i>',
                title: "Error",
                text: "There was a problem submitting your rating. Please try again.",
                timer: 3000,
                showConfirmButton: false,
                customClass: {
                    title: "swal2-title-custom",
                    icon: "swal2-icon-custom",
                    popup: "swal-custom-popup"
                }
            });
        });
    }

    // Find and disable a rate button by site ID
    function disableRateButtonBySiteId(siteId) {
        document.querySelectorAll('.destination-card').forEach(card => {
            if (card.dataset.siteId === siteId) {
                const rateBtn = card.querySelector('.rate-btn');
                if (rateBtn) {
                    disableRateButton(rateBtn);
                }
            }
        });
    }

    // Add the disableRateButton helper function
    function disableRateButton(button) {
        button.disabled = true;
        button.classList.add('disabled');
        button.style.backgroundColor = '#cccccc';
        button.style.color = '#666666';
        button.style.cursor = 'not-allowed';
        button.innerText = 'Rated';
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/js/all.min.js"></script>
</body>
</html>