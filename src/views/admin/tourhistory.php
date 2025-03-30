<?php
include '../../../includes/auth.php';
require_once '../../config/dbconnect.php';
require_once '../../controllers/admin/tourhistorycontroller.php';

$userid = $_SESSION['userid'];
$query = "SELECT name FROM Users WHERE userid = :userid LIMIT 1";
$stmt = $conn->prepare($query);
$stmt->bindParam(':userid', $userid);
$stmt->execute();
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

$adminName = $admin ? htmlspecialchars($admin['name']) : "Admin";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Tour History</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;700&family=Raleway:wght@300;400;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="../../../public/assets/styles/admin/tourhistory.css">
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
                <a href="monthlyperformance.php" class="nav-link">
                    <i class="bi bi-bar-chart-line"></i>
                    <span class="d-none d-sm-inline">Monthly Performance</span>
                </a>
            </li>
            <li class="nav-item mb-4">
                <a href="" class="nav-link active">
                    <i class="bi bi-map-fill"></i>
                    <span class="d-none d-sm-inline">Tour History</span>
                </a>
            </li>
            <li class="nav-item mb-4">
                <a href="touristsites.php" class="nav-link">
                    <i class="bi bi-image"></i>
                    <span class="d-none d-sm-inline">Tourist Sites</span>
                </a>
            </li>
            <li class="nav-item mb-4">
                <a href="accounts.php?usertype=mngr" class="nav-link">
                    <i class="bi bi-people"></i>
                    <span class="d-none d-sm-inline">Accounts</span>
                </a>
            </li>
            <li class="nav-item mb-4">
                <a href="employeelogs.php" class="nav-link">
                    <i class="bi bi-person-vcard"></i>
                    <span class="d-none d-sm-inline">Employee Logs</span>
                </a>
            </li>
        </ul>
    </div>
        
    <ul class="nav nav-pills flex-column mb-4">
        <li class="nav-item mb-3">
            <a href="javascript:void(0);" class="nav-link admin-name active">
                <i class="bi bi-person-circle"></i>
                <span class="d-none d-sm-inline"><?= $adminName; ?></span>
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
            <h2>Tour History</h2>
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
            <button type="button" id="completed-btn" class="btn-custom active" onclick="showCompleted()">Completed</button>
            <button type="button" id="cancelled-btn" class="btn-custom" onclick="showCancelled()">Cancelled</button>
        </div>

        <div class="row mt-3 d-flex justify-content-center">
            <div class="col-lg-12 col-md-12 col-12 mb-3" id="completed-tours">
                <?php if (count($completed_tours) > 0): ?>
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
                                <?php foreach ($completed_tours as $tourid => $tourGroup): 
                                    $firstTour = $tourGroup[0];
                                    $destinationCount = count($tourGroup);
                                    $totalPrice = array_sum(array_column($tourGroup, 'price')) * $firstTour['companions'];
                                    $tourData = htmlspecialchars(json_encode($tourGroup)); 
                                ?>
                                    <tr onclick="showModal(<?php echo $tourData; ?>)" style="cursor: pointer;">
                                        <td><?php echo htmlspecialchars($firstTour['name']); ?></td>
                                        <td><?php echo date('d M Y | g:i A', strtotime($firstTour['submitted_on'])); ?></td>
                                        <td><?php echo $destinationCount; ?></td>
                                        <td><?php echo date('d M Y', strtotime($firstTour['travel_date'])); ?></td>
                                        <td><?php echo $firstTour['companions']; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php else: ?>
                    <div class="no-completed-tours text-center">No completed tours found.</div>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-lg-12 col-md-12 col-12 mb-3" id="cancelled-tours" style="display: none;">
            <?php if (count($cancelled_tours) > 0): ?>
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
                            <?php foreach ($cancelled_tours as $tourid => $tourGroup): 
                                $firstTour = $tourGroup[0];
                                $destinationCount = count($tourGroup);
                                $tourData = htmlspecialchars(json_encode($tourGroup)); 
                            ?>
                                <tr onclick="showModal(<?php echo $tourData; ?>)" style="cursor: pointer; background-color: #E7EBEE;">
                                    <td><?php echo htmlspecialchars($firstTour['name']); ?></td>
                                    <td><?php echo date('d M Y | g:i A', strtotime($firstTour['submitted_on'])); ?></td>
                                    <td><?php echo $destinationCount; ?></td>
                                    <td><?php echo date('d M Y', strtotime($firstTour['travel_date'])); ?></td>
                                    <td><?php echo $firstTour['companions']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php else: ?>
                <div class="no-cancelled-tours text-center">No cancelled tours found.</div>
            <?php endif; ?>
        </div>

    </div>
</div>

<div class="modal fade" id="tourHistoryModal" tabindex="-1" aria-labelledby="tourHistoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content p-4">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold" id="tourHistoryModalLabel">Tour History</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body d-flex">
                <div class="d-flex">
                    <div class="stepper" id="stepper-container"></div>
                    <div class="destination-container" id="destination-container"></div>
                </div>

                <div class="summary-container">
                    <p><strong>Date Created</strong><br><span id="date-created"></span></p>
                    <p><strong>Number of People</strong><br><span id="num-people"></span></p>
                    <p><strong>Estimated Fees</strong></p>
                    <div class="estimated-fees" id="estimated-fees"></div>
                    <p class="total-price"><strong>₱ <span id="total-price"></span></strong></p>
                </div>
            </div>
            <div class="modal-footer">
                <p>*Fee is only an estimate and subject to change if the destination can accommodate special discounts.</p>
            </div>
            <p class="tour-status" id="tour-status"></p>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/js/all.min.js"></script>
<script src="../../../public/assets/scripts/main.js"></script>
<script>
function showModal(tourData) {
    document.getElementById('destination-container').innerHTML = "";
    document.getElementById('stepper-container').innerHTML = "";
    document.getElementById('estimated-fees').innerHTML = "";

    let totalPrice = 0;
    let companions = tourData[0].companions;
    let userName = tourData[0].name;

    let dateObj = new Date(tourData[0].submitted_on);
    let dateFormatted = dateObj.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
    let timeFormatted = dateObj.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
    let dateCreated = `${dateFormatted} | ${timeFormatted}`;

    document.getElementById('tourHistoryModalLabel').textContent = `Tour History of ${userName}`;
    document.getElementById('date-created').textContent = dateCreated;
    document.getElementById('num-people').textContent = companions;
    document.getElementById('tour-status').textContent = "Tour has been " + tourData[0].status;

    tourData.forEach((tour, index) => {
        let stepperItem = `
            <div class="step">
                <div class="circle">${index + 1}</div>
                ${index < tourData.length - 1 ? '<div class="dashed-line"></div>' : ''}
            </div>
        `;
        document.getElementById('stepper-container').innerHTML += stepperItem;

        let destinationCard = `
            <div class="destination-card d-flex align-items-center" style="margin-bottom: 15px;">
                <div class="image-placeholder">
                    <img src="/T-VIBES/public/uploads/${tour.siteimage}" alt="${tour.sitename}" 
                        style="width: 100px; height: 100px; object-fit: cover; border-radius: 8px;">
                </div>
                <div class="destination-info ms-3">
                    <h6>${tour.sitename}</h6>
                    <p style="color: #757575; font-size: 16px;">
                        <i class="bi bi-calendar"></i> ${new Date(tour.travel_date).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' })}
                    </p>
                    <p style="color: #555; font-size: 16px;">
                        <i class="bi bi-cash"></i> ₱${parseFloat(tour.price).toFixed(2)} per pax
                    </p>
                </div>
            </div>
        `;
        document.getElementById('destination-container').innerHTML += destinationCard;

        let feeItem = `<p>${tour.sitename} <span>x${companions}</span></p>`;
        document.getElementById('estimated-fees').innerHTML += feeItem;

        totalPrice += tour.price * companions;
    });

    document.getElementById('total-price').textContent = totalPrice.toFixed(2);

    let modal = new bootstrap.Modal(document.getElementById('tourHistoryModal'));
    modal.show();
}

function showCompleted() {
    document.getElementById("completed-tours").style.display = "block";
    document.getElementById("cancelled-tours").style.display = "none";

    document.getElementById("completed-btn").classList.add("active");
    document.getElementById("cancelled-btn").classList.remove("active");
}

function showCancelled() {
    document.getElementById("completed-tours").style.display = "none";
    document.getElementById("cancelled-tours").style.display = "block";

    document.getElementById("completed-btn").classList.remove("active");
    document.getElementById("cancelled-btn").classList.add("active");
}
</script>
</body>
</html>