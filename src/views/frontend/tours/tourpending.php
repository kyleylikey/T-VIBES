<?php
include '../../../../includes/auth.php';
require_once '../../../controllers/tourist/tourpendingcontroller.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Tour Pending - Taal Heritage Town</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;700&family=Raleway:wght@300;400;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        * {
            font-family: 'Nunito', sans-serif;
        }

        body {
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
            font-family: 'Raleway', sans-serif !important;
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

        /* Tour Row Styling */
        .tour-row {
            background-color: #E7EBEE;
            padding: 18px 20px;
            margin-bottom: 0;
            border-left: 4px solid #729AB8;
            transition: all 0.3s ease;
            border-bottom: 1px solid rgba(115, 155, 184, 0.15);
        }

        .tour-row:hover {
            background-color: #d9e5ee;
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
            background-color: #729AB8;
            color: white;
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
        }

        .destinations-list li:before {
            content: "";
            position: absolute;
            left: 0;
            top: 8px;
            width: 8px;
            height: 8px;
            background-color: #EC6350;
            border-radius: 50%;
        }

        .destinations-list li:last-child {
            margin-bottom: 0;
        }

        /* Status Badge */
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 13px;
            margin-left: 10px;
        }

        .status-pending {
            background-color: rgba(236, 99, 80, 0.15);
            color: #A9221C;
        }

        /* Contact Us Link */
        .contact-us-section {
            text-align: center;
            margin: 30px 0 50px;
            font-size: 16px;
            color: #757575;
        }

        .contact-us-link {
            color: #729AB8;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            border-bottom: 1px dashed #729AB8;
            padding-bottom: 2px;
        }

        .contact-us-link:hover {
            color: #102E47;
            border-bottom: 1px solid #102E47;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 50px 20px;
            background-color: #E7EBEE;
            border-radius: 0 0 10px 10px;
        }

        .empty-state i {
            font-size: 50px;
            color: #729AB8;
            margin-bottom: 15px;
        }

        .empty-state h4 {
            font-family: 'Raleway', sans-serif;
            color: #102E47;
            margin-bottom: 10px;
        }

        .empty-state p {
            color: #757575;
            max-width: 350px;
            margin: 0 auto;
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

        .destination-image img {
            width: 50px !important;
            height: 50px !important;
            object-fit: cover;
            border-radius: 8px;
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
    <div class="table-content">
        <div class="row table-header">
            <div class="col-3" style="font-family: 'Raleway', sans-serif !important;">Date Created</div>
            <div class="col-3" style="font-family: 'Raleway', sans-serif !important;">Planned Date/s</div>
            <div class="col-3" style="font-family: 'Raleway', sans-serif !important;">Number of People</div>
            <div class="col-3" style="font-family: 'Raleway', sans-serif !important;">Destinations</div>
        </div>

        <?php foreach ($userPendingTour as $pending) : ?>
            <div class="row align-items-center tour-row" data-bs-toggle="modal" data-bs-target="#tourModal<?= $pending['tourid'] ?>">
                <div class="col-3">
                    <span class="tour-date"><?= date('d M Y', strtotime($pending['created_at'])) ?></span>
                </div>
                <div class="col-3">
                    <span class="tour-date"><?= date('d M Y', strtotime($pending['date'])) ?></span>
                </div>
                <div class="col-3">
                    <span class="people-count"><?= $pending['companions'] ?></span>
                </div>
                <div class="col-3">
                    <ul class="destinations-list">
                        <?php  
                        $tourSites = $tourModel->getPendingTourSitesByUser($pending['tourid'], $_SESSION['userid']);
                        foreach ($tourSites as $site) :  
                        ?>
                            <li><?= $site['sitename'] ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
            
            <!-- Create a separate modal for each tour -->
            <div class="modal fade" id="tourModal<?= $pending['tourid'] ?>" tabindex="-1" aria-labelledby="modalLabel<?= $pending['tourid'] ?>" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalLabel<?= $pending['tourid'] ?>">Tour Details</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <?php 
                                    $tourSites = $tourModel->getPendingTourSitesByUser($pending['tourid'], $_SESSION['userid']);
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
                                        $tourSitesAndFees = $tourModel->getPendingTourSitesByUser($pending['tourid'], $_SESSION['userid']);
                                        $totalFees = 0;
                                        foreach ($tourSitesAndFees as $site) : 
                                            ?>
                                        <div class="summary-value">
                                            <div class="row">
                                                <div class="col-8 summary-value"><?= $site['sitename'] ?></div>
                                                <div class="col-4 summary-value">₱<?= $site['price'] ?></div>
                                            </div>
                                        </div>
                                        <?php $totalFees += $site['price']; endforeach; ?>
                                        <div class="summary-value">
                                            <div class="row">
                                                <div class="col-8"></div>
                                                <div class="col-4 fw-bold">₱<?= $totalFees ?></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="summary-title">Total Fees:</div>
                                        <div class="row">
                                            <div class="col-8 summary-value">₱<?= $totalFees ?> x <?= $pending['companions'] ?></div>
                                            <div class="col-4 fw-bolder" style="color: #EC6350;">₱<?= $pending['companions'] * $totalFees ?></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <div class="status-completed fw-bold mt-2 text-info">Status: <span class="text-info">Pending</span></div>
                            <div class="summary-value fw-light text-center">*Fee is only an estimate and subject to change if the destination can accommodate special discounts.</div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        
        <?php if (empty($userPendingTour)) : ?>
            <div class="empty-state">
                <i class="bi bi-calendar-check"></i>
                <h4>No Pending Tours</h4>
                <p>You don't have any pending tours at the moment. Create a new tour plan to get started!</p>
            </div>
        <?php endif; ?>
    </div>
    
    <div class="contact-us-section">
        Need modifications with your tour? <a href="../contactus.php" class="contact-us-link">Contact Us</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>