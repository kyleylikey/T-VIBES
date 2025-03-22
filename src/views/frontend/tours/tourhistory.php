<?php
// Sample data for demonstration
$tours = [
    ['created' => 'DD M YYYY', 'planned' => 'DD M YYYY', 'people' => 2, 'destinations' => ['Destination Name']],
    ['created' => 'DD M YYYY', 'planned' => 'DD M YYYY', 'people' => 2, 'destinations' => ['Destination Name', 'Destination Name']],
    ['created' => 'DD M YYYY', 'planned' => 'DD M YYYY', 'people' => 2, 'destinations' => ['Destination Name']],
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Tour Requests</title>
    <link rel="stylesheet" href="../../../../public/assets/styles/index.css">
	<link rel="stylesheet" href="../../../../public/assets/styles/main.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>

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

        /* Modal styles */
        .modal-content {
            padding: 20px;
            border-radius: 12px;
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



        /* Rating Button */
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
    flex: 1; /* Make both buttons equal width */
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

        /* Rating Stars */
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

    <?php foreach ($tours as $index => $tour) : ?>
        <div class="row align-items-center tour-row" data-bs-toggle="modal" data-bs-target="#tourModal<?= $index ?>">
            <div class="col-3"><?= $tour['created'] ?></div>
            <div class="col-3"><?= $tour['planned'] ?></div>
            <div class="col-2"><?= $tour['people'] ?></div>
            <div class="col-4">
                <?php foreach ($tour['destinations'] as $destination) : ?>
                    <div><?= $destination ?></div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="tourModal<?= $index ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Tour Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <?php foreach ($tour['destinations'] as $destination) : ?>
                                    <div class="destination-card">
                                        <div class="destination-image">
                                            <i class="bi bi-image"></i>
                                        </div>
                                        <div class="destination-details">
                                            <div class="destination-name"><?= $destination ?></div>
                                            <div class="rate-review-container">
											<button class="rate-btn" onclick="openRateModal()">Rate</button>
											<button class="review-btn" onclick="openReviewModal()">Review</button>
										</div>

										</div>
										
                                    </div>
                                <?php endforeach; ?>
                                <div class="status-pending">Status: Complete</div>
                            </div>
							
							<div class="col-md-6">
                            <div class="mb-3">
								<span class="label-bold">Date Created:</span>
								<span class="value-grey"><?= $tour['created'] ?></span>
							</div>
							<div class="mb-3">
								<span class="label-bold">Number of People:</span>
								<span class="value-grey"><?= $tour['people'] ?></span>
							</div>
							<div class="mb-3">
								<span class="label-bold">Estimated Fees:</span>
								<?php foreach ($tour['destinations'] as $destination) : ?>
									<div class="value-grey"><?= $destination ?> x2</div>
								<?php endforeach; ?>
							</div>
							<div class="mb-3">
								<span class="label-bold">Destination Name:</span>
								<?php foreach ($tour['destinations'] as $destination) : ?>
									<div class="value-grey"><?= $destination ?></div>
								<?php endforeach; ?>
							</div>
							<div>
								<span class="label-bold">Total:</span>
								<span class="value-grey">₱ 0.00</span>
							</div>
							</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Rate Experience Modal -->
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


</script>

<!-- SweetAlert CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
