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
            <div class="filter-search-container">
                <div class="search-wrapper">
                    <i class="bi bi-search"></i>
                    <input type="text" class="form-control" id="searchBar" placeholder="Search">
                </div>
            </div>
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
document.addEventListener('DOMContentLoaded', function() {
    // Pagination configuration
    const rowsPerPage = 10;
    let completedCurrentPage = 1;
    let cancelledCurrentPage = 1;
    
    // Track filtered rows
    let filteredCompletedRows = [];
    let filteredCancelledRows = [];
    
    // Initialize pagination for both tabs
    initTabPagination('completed-tours');
    
    // Setup search functionality
    const searchBar = document.getElementById('searchBar');
    if (searchBar) {
        searchBar.addEventListener('input', function() {
            const searchValue = this.value.toLowerCase().trim();
            searchTours(searchValue);
        });
    }
    
    // Function to filter tours based on search input
    function searchTours(searchValue) {
        // Get the currently active tab
        const isCompletedActive = document.getElementById('completed-btn').classList.contains('active');
        const activeTableId = isCompletedActive ? 'completed-tours' : 'cancelled-tours';
        
        // Get all table rows
        const completedRows = document.querySelectorAll('#completed-tours tbody tr');
        const cancelledRows = document.querySelectorAll('#cancelled-tours tbody tr');
        
        // Filter the rows for both tables
        filteredCompletedRows = filterRowsBySearch(completedRows, searchValue);
        filteredCancelledRows = filterRowsBySearch(cancelledRows, searchValue);
        
        // Get current active filtered rows
        const activeFilteredRows = isCompletedActive ? filteredCompletedRows : filteredCancelledRows;
        
        // Update no results message if needed
        updateNoResultsMessage(activeTableId, activeFilteredRows.length, searchValue);
        
        // Reset pagination to first page
        if (isCompletedActive) {
            completedCurrentPage = 1;
            showTabPage('completed-tours', completedCurrentPage, filteredCompletedRows);
        } else {
            cancelledCurrentPage = 1;
            showTabPage('cancelled-tours', cancelledCurrentPage, filteredCancelledRows);
        }
    }
    
    // Helper function to filter rows by search term
    function filterRowsBySearch(rows, searchValue) {
        const filtered = [];
        
        rows.forEach(row => {
            // Get all text content from the row
            const text = row.textContent.toLowerCase();
            
            // Check if the row contains the search value
            if (text.includes(searchValue)) {
                filtered.push(row);
            }
        });
        
        return filtered;
    }
    
    // Function to update or create no results message
    function updateNoResultsMessage(tableId, matchCount, searchValue) {
        const isCompletedTab = tableId === 'completed-tours';
        const noToursSelector = `.no-${isCompletedTab ? 'completed' : 'cancelled'}-tours`;
        const noToursDiv = document.querySelector(`#${tableId} ${noToursSelector}`);
        const infoBox = document.querySelector(`#${tableId} .info-box`);
        
        if (matchCount === 0) {
            // No matches found
            if (!noToursDiv) {
                const noResultsDiv = document.createElement('div');
                noResultsDiv.className = `no-${isCompletedTab ? 'completed' : 'cancelled'}-tours text-center`;
                noResultsDiv.textContent = `No ${isCompletedTab ? 'completed' : 'cancelled'} tours found matching "${searchValue}".`;
                
                if (infoBox) {
                    infoBox.style.display = 'none';
                    infoBox.parentElement.appendChild(noResultsDiv);
                }
            } else {
                noToursDiv.textContent = `No ${isCompletedTab ? 'completed' : 'cancelled'} tours found matching "${searchValue}".`;
                noToursDiv.style.display = 'block';
                
                if (infoBox) infoBox.style.display = 'none';
            }
            
            // Hide pagination when no results
            const paginationId = isCompletedTab ? 'completed-pagination' : 'cancelled-pagination';
            const pagination = document.getElementById(paginationId);
            if (pagination) pagination.style.display = 'none';
        } else {
            // Matches found
            if (infoBox) infoBox.style.display = 'block';
            if (noToursDiv) noToursDiv.style.display = 'none';
            
            // Show pagination if needed
            const paginationId = isCompletedTab ? 'completed-pagination' : 'cancelled-pagination';
            const pagination = document.getElementById(paginationId);
            if (pagination && matchCount > rowsPerPage) pagination.style.display = 'flex';
        }
    }
    
    // Function to initialize pagination for a specific tab
    function initTabPagination(tabId) {
        const tab = document.getElementById(tabId);
        if (!tab) return;
        
        // Get all table rows in this tab
        const tableRows = tab.querySelectorAll('tbody tr');
        if (tableRows.length === 0) return;
        
        // Calculate total pages
        const totalPages = Math.ceil(tableRows.length / rowsPerPage);
        if (totalPages <= 1) return; // No pagination needed for 1 page
        
        // Create pagination container if it doesn't exist
        let paginationId = tabId === 'completed-tours' ? 'completed-pagination' : 'cancelled-pagination';
        
        if (!document.getElementById(paginationId)) {
            const tableContainer = tab.querySelector('.table-responsive');
            if (!tableContainer) return;
            
            const paginationControls = document.createElement('div');
            paginationControls.id = paginationId;
            paginationControls.className = 'pagination-controls d-flex justify-content-start mt-3';
            paginationControls.innerHTML = `
                <div class="pagination-wrapper">
                    <button class="btn btn-sm prev-btn" disabled>&lt;</button>
                    <span class="page-indicator">1/${totalPages}</span>
                    <button class="btn btn-sm next-btn">&gt;</button>
                </div>
            `;
            
            tableContainer.parentNode.appendChild(paginationControls);
            
            // Add event listeners for pagination
            const prevBtn = paginationControls.querySelector('.prev-btn');
            const nextBtn = paginationControls.querySelector('.next-btn');
            
            prevBtn.addEventListener('click', function() {
                if (tabId === 'completed-tours') {
                    if (completedCurrentPage > 1) {
                        completedCurrentPage--;
                        showTabPage(tabId, completedCurrentPage, filteredCompletedRows);
                    }
                } else {
                    if (cancelledCurrentPage > 1) {
                        cancelledCurrentPage--;
                        showTabPage(tabId, cancelledCurrentPage, filteredCancelledRows);
                    }
                }
            });
            
            nextBtn.addEventListener('click', function() {
                const rowsToUse = tabId === 'completed-tours' ? 
                    (filteredCompletedRows.length > 0 ? filteredCompletedRows : tab.querySelectorAll('tbody tr')) : 
                    (filteredCancelledRows.length > 0 ? filteredCancelledRows : tab.querySelectorAll('tbody tr'));
                
                const totalPages = Math.ceil(rowsToUse.length / rowsPerPage);
                
                if (tabId === 'completed-tours') {
                    if (completedCurrentPage < totalPages) {
                        completedCurrentPage++;
                        showTabPage(tabId, completedCurrentPage, filteredCompletedRows);
                    }
                } else {
                    if (cancelledCurrentPage < totalPages) {
                        cancelledCurrentPage++;
                        showTabPage(tabId, cancelledCurrentPage, filteredCancelledRows);
                    }
                }
            });
        }
        
        // Initialize filtered rows arrays
        filteredCompletedRows = Array.from(document.querySelectorAll('#completed-tours tbody tr'));
        filteredCancelledRows = Array.from(document.querySelectorAll('#cancelled-tours tbody tr'));
        
        // Show initial page
        if (tabId === 'completed-tours') {
            showTabPage(tabId, completedCurrentPage, filteredCompletedRows);
        } else {
            showTabPage(tabId, cancelledCurrentPage, filteredCancelledRows);
        }
    }
    
    // Function to show specific page for a tab
    function showTabPage(tabId, page, filteredRows = []) {
        const tab = document.getElementById(tabId);
        if (!tab) return;
        
        // Get all table rows or use filtered rows if provided
        const allRows = tab.querySelectorAll('tbody tr');
        const rowsToUse = filteredRows.length > 0 ? filteredRows : Array.from(allRows);
        
        // Calculate total pages
        const totalPages = Math.ceil(rowsToUse.length / rowsPerPage);
        
        // First hide all rows
        allRows.forEach(row => {
            row.style.display = 'none';
        });
        
        // Calculate start and end index
        const startIndex = (page - 1) * rowsPerPage;
        const endIndex = Math.min(startIndex + rowsPerPage, rowsToUse.length);
        
        // Show only rows for current page
        for (let i = startIndex; i < endIndex; i++) {
            rowsToUse[i].style.display = '';
        }
        
        // Update pagination controls
        const paginationId = tabId === 'completed-tours' ? 'completed-pagination' : 'cancelled-pagination';
        const pagination = document.getElementById(paginationId);
        
        if (pagination) {
            const prevBtn = pagination.querySelector('.prev-btn');
            const nextBtn = pagination.querySelector('.next-btn');
            const pageIndicator = pagination.querySelector('.page-indicator');
            
            prevBtn.disabled = page <= 1;
            nextBtn.disabled = page >= totalPages;
            pageIndicator.textContent = `${page}/${totalPages || 1}`;
            
            // Show/hide pagination based on number of results
            pagination.style.display = totalPages > 1 ? 'flex' : 'none';
        }
    }
    
    // Override the existing functions
    window.showCompleted = function() {
        document.getElementById("completed-tours").style.display = "block";
        document.getElementById("cancelled-tours").style.display = "none";
        document.getElementById("completed-btn").classList.add("active");
        document.getElementById("cancelled-btn").classList.remove("active");
        
        // Show pagination for completed tab if needed
        const searchValue = document.getElementById('searchBar').value.toLowerCase().trim();
        if (searchValue) {
            showTabPage('completed-tours', completedCurrentPage, filteredCompletedRows);
        } else {
            showTabPage('completed-tours', completedCurrentPage);
        }
    };
    
    window.showCancelled = function() {
        document.getElementById("completed-tours").style.display = "none";
        document.getElementById("cancelled-tours").style.display = "block";
        document.getElementById("completed-btn").classList.remove("active");
        document.getElementById("cancelled-btn").classList.add("active");
        
        // Initialize cancelled pagination if not already done
        if (!document.getElementById("cancelled-pagination")) {
            initTabPagination('cancelled-tours');
        }
        
        // Show pagination for cancelled tab if needed
        const searchValue = document.getElementById('searchBar').value.toLowerCase().trim();
        if (searchValue) {
            showTabPage('cancelled-tours', cancelledCurrentPage, filteredCancelledRows);
        } else {
            showTabPage('cancelled-tours', cancelledCurrentPage);
        }
    };
});
</script>
</body>
</html>