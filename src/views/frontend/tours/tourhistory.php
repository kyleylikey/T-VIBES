<?php
include '../../../../includes/auth.php';
require_once '../../../config/dbconnect.php';

$database = new Database();
$conn = $database->getConnection();

$userid = $_SESSION['userid']; 

$query = "SELECT t.tourid, t.date, t.status, t.created_at, t.companions, 
                 GROUP_CONCAT(s.sitename ORDER BY s.sitename SEPARATOR ', ') AS destinations, 
                 GROUP_CONCAT(s.price ORDER BY s.sitename SEPARATOR ', ') AS prices,
                 GROUP_CONCAT(s.siteimage ORDER BY s.sitename SEPARATOR ', ') AS images
          FROM tour t
          JOIN sites s ON t.siteid = s.siteid
          WHERE t.userid = ? AND t.status = 'accepted' AND t.date < CURDATE()
          GROUP BY t.tourid
          ORDER BY t.created_at DESC";

$stmt = $conn->prepare($query);
$stmt->bindParam(1, $userid);
$stmt->execute();
$tours = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
                            <?php foreach ($destinations as $key => $destination) : ?>
                                <div class="destination-card d-flex align-items-center p-2 mb-2" style="background: #f8f9fa; border-radius: 8px;">
                                    <div class="destination-image me-2">
                                        <img src="../../../../public/uploads/<?= trim($images[$key]) ?>" alt="<?= trim($destination) ?>" class="img-fluid" style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px;">
                                    </div>
                                    <div class="destination-details flex-grow-1">
                                        <div class="destination-name fw-bold"><?= trim($destination) ?></div>
                                        <div class="rate-review-container mt-1">
                                            <button class="btn btn-custom btn-sm">Rate</button>
                                            <button class="btn btn-custom btn-sm">Review</button>
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
                        <div class="summary-value fw-light fst-italic text-center">*Fee is only an estimate and subject to change if the destination can accommodate special discounts.</div>
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

<script>
    function openRateModal() {
        const modal = new bootstrap.Modal(document.getElementById('rateExperienceModal'));
        modal.show();

        document.querySelectorAll('.stars .bi').forEach(star => {
            star.addEventListener('click', function () {
                const value = this.getAttribute('data-value');
                document.querySelectorAll('.stars .bi').forEach((s, i) => {
                    s.classList.toggle('active', i < value);
                });
            });
        });
    }
</script>

<script>
    let selectedRating = 0;

    function openRateModal() {
        const modal = new bootstrap.Modal(document.getElementById('rateExperienceModal'));
        modal.show();

        // Handle star click events
        document.querySelectorAll('.stars .bi').forEach(star => {
            star.addEventListener('click', function () {
                selectedRating = parseInt(this.getAttribute('data-value'));
                document.querySelectorAll('.stars .bi').forEach((s, i) => {
                    s.classList.toggle('active', i < selectedRating);
                });
            });
        });

        // Handle submit button
        document.getElementById('submitRating').addEventListener('click', function () {
            if (selectedRating > 0) {
                // Close modal
                modal.hide();

                // Display SweetAlert confirmation
                Swal.fire({
                    title: "Thank you for leaving a rating!",
                    text: `You rated ${selectedRating} star(s).`,
                    icon: "success",
                    confirmButtonColor: "#EC6350"
                });

                // Reset stars after submission
                selectedRating = 0;
                document.querySelectorAll('.stars .bi').forEach(star => {
                    star.classList.remove('active');
                });
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
    }
	function openReviewModal() {
    Swal.fire({
        title: "Leave a Review",
        input: "textarea",
        inputAttributes: {
            autocapitalize: "off",
            placeholder: "Type here..."
        },
        showCancelButton: true,
        confirmButtonText: "Submit",
        confirmButtonColor: "#EC6350",
        showLoaderOnConfirm: true,
        preConfirm: async (review) => {
            if (!review) {
                Swal.showValidationMessage("Please enter a review");
                return false;
            } 
            // Simulate async operation (like saving to a database)
            await new Promise((resolve) => setTimeout(resolve, 500));
            return review;
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: "Thank you!",
                text: "Your review has been submitted.",
                icon: "success"
            });
        }
    });
}

function showTourDetails(tour) {
    document.getElementById('destination-name').innerText = tour.destinations;
    document.getElementById('tour-status').innerText = 'Status: ' + tour.status;
    document.getElementById('tour-created').innerText = tour.created_at;
    document.getElementById('tour-people').innerText = tour.companions;
    document.getElementById('tour-fees').innerText = tour.destinations + ' x2';
    document.getElementById('tour-destination').innerText = tour.destinations;
    document.getElementById('tour-total').innerText = '₱ 0.00';

    var modal = new bootstrap.Modal(document.getElementById('tourModal'));
    modal.show();
}

</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
