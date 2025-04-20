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

// Modified query to include siteids
$query = "SELECT t.tourid, t.date, t.status, t.created_at, t.companions, 
                 GROUP_CONCAT(s.siteid ORDER BY s.sitename SEPARATOR ',') AS siteids,
                 GROUP_CONCAT(s.sitename ORDER BY s.sitename SEPARATOR ',') AS destinations, 
                 GROUP_CONCAT(s.price ORDER BY s.sitename SEPARATOR ',') AS prices,
                 GROUP_CONCAT(s.siteimage ORDER BY s.sitename SEPARATOR ',') AS images
          FROM tour t
          JOIN sites s ON t.siteid = s.siteid
          WHERE t.userid = ? AND t.status = 'accepted' AND t.date < CURDATE()
          GROUP BY t.tourid
          ORDER BY t.created_at DESC";

$stmt = $conn->prepare($query);
$stmt->bindParam(1, $userid);
$stmt->execute();
$tours = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all sites the user has already rated
$ratedSitesQuery = "SELECT site_id FROM user_ratings WHERE user_id = :user_id";
$ratedSitesStmt = $conn->prepare($ratedSitesQuery);
$ratedSitesStmt->bindParam(':user_id', $userid, PDO::PARAM_INT);
$ratedSitesStmt->execute();
$ratedSites = $ratedSitesStmt->fetchAll(PDO::FETCH_COLUMN);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Tour History</title>
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

        .table-container {
            margin-top: 20px;
        }

        .table-header {
            font-weight: bold;
            color: #102E47;
        }

        .tour-row {
            background-color: #EDF1F5;
            border-radius: 12px;
            padding: 12px;
            margin-bottom: 12px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .tour-row:hover {
            background-color: #D9E2EC;
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
    </style>
</head>
<body>
<?php include '../../templates/headertours.php'; ?>
<?php include '../../templates/toursnav.php'; ?>

<div class="container table-container">
    <div class="row table-header py-2">
        <div class="col-3">Date Created</div>
        <div class="col-3">Planned Date/s</div>
        <div class="col-2">Number of People</div>
        <div class="col-4">Destinations</div>
    </div>

    <?php foreach ($tours as $index => $tour) : 
        $destinations = explode(',', $tour['destinations']); 
        $prices = explode(',', $tour['prices']); 
        $images = explode(',', $tour['images']); 
        $siteIds = explode(',', $tour['siteids']);
        $num_people = $tour['companions']; 
    ?>
    <div class="row align-items-center tour-row" data-bs-toggle="modal" data-bs-target="#tourModal<?= $index ?>">
        <div class="col-3"><?= date('d M Y', strtotime($tour['created_at'])) ?></div>
        <div class="col-3"><?= date('d M Y', strtotime($tour['date'])) ?></div>
        <div class="col-2"><?= $num_people ?></div>
        <div class="col-4">
            <?php foreach ($destinations as $destination) : ?>
                <div><?= trim($destination) ?></div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="modal fade" id="tourModal<?= $index ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tour Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <?php foreach ($destinations as $key => $destination) : 
                                $siteId = trim($siteIds[$key]);
                                $isRated = in_array($siteId, $ratedSites);
                            ?>
                                <div class="destination-card d-flex align-items-center p-2 mb-2" style="background: #f8f9fa; border-radius: 8px;" data-site-id="<?= $siteId ?>">
                                    <div class="destination-image me-2">
                                        <img src="../../../../public/uploads/<?= trim($images[$key]) ?>" alt="<?= trim($destination) ?>" class="img-fluid" style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px;">
                                    </div>
                                    <div class="destination-details flex-grow-1">
                                        <div class="destination-name fw-bold"><?= trim($destination) ?></div>
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
                                <span class="value-grey"><?= $num_people ?></span>
                            </div>
                            <div class="mb-3">
                                <span class="label-bold">Estimated Fees:</span><br>
                                <div class="value-grey">
                                    <?php 
                                    $total_per_person = 0;
                                    foreach ($destinations as $key => $destination) : 
                                        $fee_per_destination = (int) $prices[$key];
                                        $total_per_person += $fee_per_destination;
                                    ?>
                                        <div class="d-flex justify-content-between">
                                            <span><?= trim($destination) ?></span>
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

                            <div class="mb-3">
                                <span class="label-bold">Total Fees:</span><br>
                                <div class="value-grey d-flex justify-content-between fw-bold">
                                    <span>₱<?= number_format($total_per_person, 2) ?> x <?= $num_people ?></span>
                                    <span style="color: #EC6350;">₱<?= number_format($total_per_person * $num_people, 2) ?></span>
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
    </div>
    <?php endforeach; ?>
</div>

<div class="modal fade" id="rateExperienceModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content p-4 text-center">
            <h5 class="mb-3">Rate Your Experience</h5>
            <div class="stars mb-3">
                <?php for ($i = 1; $i <= 5; $i++) : ?>
                    <i class="bi bi-star" data-value="<?= $i ?>"></i>
                <?php endfor; ?>
            </div>
            <button id="submitRating">Submit</button>
        </div>
    </div>
</div>

<div class="modal fade" id="reviewModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content p-4 text-center">
            <h5 class="modal-title mb-3">Leave a Review</h5>
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
            title: "Thank you!",
            text: "Your review has been submitted successfully.",
            icon: "success",
            confirmButtonColor: "#EC6350"
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
                    title: "Error",
                    text: "Please write a review before submitting.",
                    icon: "error",
                    confirmButtonColor: "#EC6350"
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
                title: "Error",
                text: "Please select a rating before submitting.",
                icon: "error",
                confirmButtonColor: "#EC6350"
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
                    title: "Already Rated",
                    text: "You have already rated this site.",
                    icon: "info",
                    confirmButtonColor: "#EC6350"
                });
                
                // Disable the rate button for this site
                disableRateButtonBySiteId(siteId);
            } else if (data.status === 'success') {
                // Show success message
                Swal.fire({
                    title: "Thank you for your rating!",
                    text: `You rated ${rating} star(s).`,
                    icon: "success",
                    confirmButtonColor: "#EC6350"
                });
                
                // Disable the rate button for this site
                disableRateButtonBySiteId(siteId);
            } else {
                // Show error
                Swal.fire({
                    title: "Error",
                    text: data.message || "There was a problem submitting your rating.",
                    icon: "error",
                    confirmButtonColor: "#EC6350"
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
                title: "Error",
                text: "There was a problem submitting your rating. Please try again.",
                icon: "error",
                confirmButtonColor: "#EC6350"
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

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>