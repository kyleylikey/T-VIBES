<?php
session_start();

require_once '../../../controllers/tourist/tourapprovedcontroller.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Tour Approved</title>
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
            color:rgb(60, 231, 69);
            font-weight: bold;
        }

        .modal-body {
            position: relative;
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

    <?php foreach ($userApprovedTour as $pending) : ?>
        <div class="row align-items-center tour-row" data-bs-toggle="modal" data-bs-target="#tourModal<?= $pending['tourid'] ?>">
            <div class="col-3"><?= date('M d, Y', strtotime($pending['created_at'])) ?></div>
            <div class="col-3"><?= date('M d, Y', strtotime($pending['date'])) ?></div>
            <div class="col-2"><?= $pending['companions'] ?></div>
            <div class="col-4"><?= $pending['total_sites'] ?></div>
        </div>
        
        <!-- Modal -->
        <div class="modal fade" id="tourModal<?= $pending['tourid'] ?>" tabindex="-1" aria-labelledby="modalLabel<?= $pending['tourid'] ?>" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <!-- Modal header -->
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalLabel<?= $pending['tourid'] ?>">Tour Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    
                    <!-- Modal body -->
                    <div class="modal-body">
                        <div class="row">
                            <!-- Left Section: Destination Cards -->
                            <div class="col-md-6">
                                <?php 
                                // Get sites for this specific tour
                                $tourSites = $tourModel->getApprovedTourSitesByUser($pending['tourid'], $_SESSION['userid']);
                                foreach ($tourSites as $site) : 
                                ?>
                                    <div class="destination-card">
                                        <div class="destination-image">
                                            <?php if (!empty($site['siteimage'])): ?>
                                                <img src="../../../../public/uploads/<?= $site['siteimage'] ?>" alt="<?= $site['sitename'] ?>" class="img-fluid" style="width: 50px; height: 50px; object-fit: cover;">
                                            <?php else: ?>
                                                <i class="bi bi-image"></i>
                                            <?php endif; ?>
                                        </div>
                                        <div class="destination-name"><?= $site['sitename'] ?></div>
                                    </div>
                                <?php endforeach; ?>

                                
                            </div>

                            <!-- Right Section: Summary -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="summary-title">Date Created:</div>
                                    <div class="summary-value"><?= date('M d, Y', strtotime($pending['created_at'])) ?></div>
                                </div>
                                <div class="mb-3">
                                    <div class="summary-title">Planned Date:</div>
                                    <div class="summary-value"><?= date('M d, Y', strtotime($pending['date'])) ?></div>
                                </div>
                                <div class="mb-3">
                                    <div class="summary-title">Number of People:</div>
                                    <div class="summary-value"><?= $pending['companions'] ?></div>
                                </div>
                                <div class="mb-3">
                                    <div class="summary-title">Estimated Fees:</div>
                                    <?php 
                                    $tourSitesAndFees = $tourModel->getApprovedTourSitesByUser($pending['tourid'], $_SESSION['userid']);
                                    $totalFees = 0;
                                    foreach ($tourSitesAndFees as $site) : 
                                        ?>
                                    <div class="summary-value">
                                        <div class="row">
                                            <div class="col-8 summary-value"><?= $site['sitename'] ?></div>
                                            <div class="col-4 summary-value">P<?= $site['price'] ?></div>
                                        </div>
                                    </div>
                                    <?php $totalFees += $site['price']; endforeach; ?>
                                    <div class="summary-value">
                                        <div class="row">
                                            <div class="col-8"></div>
                                            <div class="col-4 fw-bold">P<?= $totalFees ?></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="summary-title">Total Fees:</div>
                                    <div class="row">
                                        <div class="col-8 summary-value"><?= $pending['companions'] ?> x P<?= $totalFees ?></div>
                                        <div class="col-4 fs-5 fw-bolder"> = P<?= $pending['companions'] * $totalFees ?>*</div>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                    <!-- Status at bottom-left -->
                    <div class="modal-footer status-pending">
                        Status: Approved
                        <div class="summary-value fw-light fst-italic">*Fee is only an estimate and subject to change if the destination can accommodate special discounts.</div>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    <div class="text-center mt-4">
        Need modifications with your tour? <a href="../contactus.php" class="link-primary link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover">Contact Us</a>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
