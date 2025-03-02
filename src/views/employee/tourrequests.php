<?php
include '../../../includes/auth.php';
require_once '../../config/dbconnect.php';
require_once '../../controllers/employee/tourrequestscontroller.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Dashboard - Tour Requests</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;700&family=Raleway:wght@300;400;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="../../../public/assets/styles/employee/tourrequests.css">
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
                    <a href="" class="nav-link active">
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
                    <a href="reviews.php" class="nav-link text-dark">
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
                <a href="javascript:void(0);" class="nav-link text-dark" onclick="logoutConfirm()">
                    <i class="bi bi-box-arrow-right"></i>
                    <span class="d-none d-sm-inline">Sign Out</span>
                </a>
            </li>
        </ul>
    </div>

    <div class="main-content">
        <div class="content-container">
            <div class="header">
                <h2>Tour Requests</h2>
                <span class="date">
                    <h2>
                        <?php
                        date_default_timezone_set('Asia/Manila');
                        echo date('M d, Y | h:i A');
                        ?>
                    </h2>
                </span>
            </div>

            <div class="row mt-3 d-flex justify-content-center">
                <div class="col-lg-12 col-md-12 col-12 mb-3">
                    <div class="info-box">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Submitted On</th>
                                        <th>Destinations</th>
                                        <th>Travel Date</th>
                                        <th>Pax</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(!empty($requests)) {
                                        foreach ($requests as $request) {
                                            echo "<tr onclick='showModal(this)' style='cursor: pointer;' 
                                            data-tourid='" .htmlspecialchars($request['tourid'])."' 
                                            data-userid='" .htmlspecialchars($request['userid'])."' 
                                            >";
                                            echo "<td>".$request['name']."</td>";
                                            echo "<td>".$request['created_at']."</td>";
                                            echo "<td>".$request['total_sites']."</td>";
                                            echo "<td>".$request['date']."</td>";
                                            echo "<td>".$request['companions']."</td>";
                                            echo "</tr>";
                                        }
                                    } 
                                    else {
                                        echo "<p>No requests found.</p>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <div class="modal fade" id="tourRequestModal" tabindex="-1" aria-labelledby="tourRequestModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content p-4">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold" id="tourRequestModalLabel">Tour Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body d-flex">
                    <div class="d-flex">
                        <div class="stepper">
                            <div class='step'>
                                <div class='circle'>1</div>
                                <div class='dashed-line'></div>
                            </div>
                        </div>
                        <div class="destination-container d-flex">
                            <div class="destination-card">
                                <div class="image-placeholder">
                                    <i class="bi bi-image"></i>
                                </div>
                                <div class="destination-info">
                                    <h6>Destination Name</h6>
                                    <p><i class="bi bi-calendar"></i> Date</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="summary-container">
                        <p><strong>Date Created</strong><br><span id="dateCreated">DD M YYYY</span></p>
                        <p><strong>Number of People</strong><br><span id="numberOfPeople">2</span></p>
                        <p><strong>Estimated Fees</strong></p>
                        <div class="estimated-fees">
                            <p>Destination Name: Price </p>
                            <p>Destination Name: Price </p>
                        </div>
                        <p class="total-price">₱ 0.00 x Pax = <strong id="estimatedFees">₱ 0.00*</strong></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn-custom accept" data-tourid="" data-userid="">Accept</button>
                    <button class="btn-custom decline" data-tourid="" data-userid="">Decline</button>
                    * Fee is only an estimate and subject to change if the destination can accommodate special discounts
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="cancelReasonModal" tabindex="-1" aria-labelledby="cancelReasonLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm-custom">
            <div class="modal-content swal-custom-popup text-center">
                <div class="modal-header border-0">
                    <h5 class="modal-title swal2-title-custom w-100" id="cancelReasonLabel">Reason for Cancellation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <textarea id="cancelReasonInput" class="form-control" rows="4" placeholder="Type here..."></textarea>
                </div>
                <div class="modal-footer justify-content-center">
                    <button id="submitCancelReason" class="swal-custom-btn">Submit</button>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/js/all.min.js"></script>
    <script src="../../../public/assets/scripts/main.js"></script>
    <script src="../../../public/assets/scripts/employee/tourrequests.js"></script>
</body>

</html>