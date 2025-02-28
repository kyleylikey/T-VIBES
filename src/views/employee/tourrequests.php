<?php
include '../../../includes/auth.php';
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
    <script src="../../../public/assets/scripts/main.js"></script>
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
                                    <tr onclick="showModal()" style="cursor: pointer;">
                                        <td>User</td>
                                        <td>DD MMM YY</td>
                                        <td>2</td>
                                        <td>DD MMM YY</td>
                                        <td>2</td>
                                    </tr>
                                    <tr onclick="showModal()" style="cursor: pointer;">
                                        <td>User</td>
                                        <td>DD MMM YY</td>
                                        <td>2</td>
                                        <td>DD MMM YY</td>
                                        <td>2</td>
                                    </tr>
                                    <tr onclick="showModal()" style="cursor: pointer;">
                                        <td>User</td>
                                        <td>DD MMM YY</td>
                                        <td>2</td>
                                        <td>DD MMM YY</td>
                                        <td>2</td>
                                    </tr>
                                    <tr onclick="showModal()" style="cursor: pointer;">
                                        <td>User</td>
                                        <td>DD MMM YY</td>
                                        <td>2</td>
                                        <td>DD MMM YY</td>
                                        <td>2</td>
                                    </tr>
                                    <tr onclick="showModal()" style="cursor: pointer;">
                                        <td>User</td>
                                        <td>DD MMM YY</td>
                                        <td>2</td>
                                        <td>DD MMM YY</td>
                                        <td>2</td>
                                    </tr>
                                    <tr onclick="showModal()" style="cursor: pointer;">
                                        <td>User</td>
                                        <td>DD MMM YY</td>
                                        <td>2</td>
                                        <td>DD MMM YY</td>
                                        <td>2</td>
                                    </tr>
                                    <tr onclick="showModal()" style="cursor: pointer;">
                                        <td>User</td>
                                        <td>DD MMM YY</td>
                                        <td>2</td>
                                        <td>DD MMM YY</td>
                                        <td>2</td>
                                    </tr>
                                    <tr onclick="showModal()" style="cursor: pointer;">
                                        <td>User</td>
                                        <td>DD MMM YY</td>
                                        <td>2</td>
                                        <td>DD MMM YY</td>
                                        <td>2</td>
                                    </tr>
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
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body d-flex">
                    <div class="d-flex">
                        <div class="stepper">
                            <div class="step">
                                <div class="circle">1</div>
                                <div class="dashed-line"></div>
                            </div>
                            <div class="step">
                                <div class="circle">2</div>
                            </div>
                        </div>

                        <div class="destination-container">
                            <div class="destination-card first-card">
                                <div class="image-placeholder">
                                    <i class="bi bi-image"></i>
                                </div>
                                <div class="destination-info">
                                    <h6>Destination Name</h6>
                                    <p><i class="bi bi-calendar"></i> Date</p>
                                </div>
                            </div>

                            <div class="destination-card second-card">
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
                        <p><strong>Date Created</strong><br>DD M YYYY</p>
                        <p><strong>Number of People</strong><br>2</p>
                        <p><strong>Estimated Fees</strong></p>
                        <div class="estimated-fees">
                            <p>Destination Name <span>x2</span></p>
                            <p>Destination Name </p>
                        </div>
                        <p class="total-price"><strong>₱ 0.00</strong></p>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn-custom">Accept</button>
                    <button class="btn-custom">Decline</button>
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
    <script src="../../../public/assets/scripts/employee/tourrequests.js"></script>
</body>

</html>