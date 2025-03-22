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
    <title>Tour Pending</title>
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
        }

        .destination-image {
            width: 50px;
            height: 50px;
            border-radius: 8px;
            background-color: #D9E2EC;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
        }

        .destination-image i {
            font-size: 30px;
            color: #7d7d7d;
        }

        .destination-name {
            color: #102E47;
            font-weight: bold;
            font-size: 18px;
        }

        .summary-title {
            font-weight: bold;
            color: #102E47;
        }

        .summary-value {
            color: #7d7d7d;
        }

        /* Status styling */
        .status-pending {
            color: #E74C3C;
            font-weight: bold;
            position: absolute;
            bottom: 20px;
            left: 20px;
        }

        .modal-body {
            position: relative;
        }

		.load-more-btn {
            background-color: white;
            color: #102E47;
            border: 2px solid #A9BCC9;
            border-radius: 30px;
            padding: 8px 24px;
            font-weight: bold;
            transition: 0.3s;
        }

        .load-more-btn:hover {
            background-color: #D9E2EC;
            color: #102E47;
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
        <div class="modal fade" id="tourModal<?= $index ?>" tabindex="-1" aria-labelledby="modalLabel<?= $index ?>" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalLabel<?= $index ?>">Tour Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <!-- Left Section: Destination Cards -->
                            <div class="col-md-6">
                                <?php foreach ($tour['destinations'] as $i => $destination) : ?>
                                    <div class="destination-card">
                                        <div class="destination-image">
                                            <i class="bi bi-image"></i>
                                        </div>
                                        <div class="destination-name"><?= $destination ?></div>
                                    </div>
                                <?php endforeach; ?>

                                <!-- Status at bottom-left -->
                                <div class="status-pending">
                                    Status: Pending
                                </div>
                            </div>

                            <!-- Right Section: Summary -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="summary-title">Date Created:</div>
                                    <div class="summary-value"><?= $tour['created'] ?></div>
                                </div>
                                <div class="mb-3">
                                    <div class="summary-title">Number of People:</div>
                                    <div class="summary-value"><?= $tour['people'] ?></div>
                                </div>
                                <div class="mb-3">
                                    <div class="summary-title">Estimated Fees:</div>
                                    <?php foreach ($tour['destinations'] as $destination) : ?>
                                        <div class="summary-value"><?= $destination ?> x2</div>
                                    <?php endforeach; ?>
                                </div>
                                <div class="mt-3">
                                    <div class="summary-title">Total:</div>
                                    <div class="summary-value">₱ 0.00</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <div class="text-center mt-4">
        <button class="load-more-btn">Load More</button>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
