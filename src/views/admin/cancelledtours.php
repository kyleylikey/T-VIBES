<?php
include '../../../includes/auth.php';
require_once '../../config/dbconnect.php';
require_once '../../controllers/admin/tourhistorycontroller.php';

$userid = $_SESSION['userid'];
$query = "SELECT name FROM Users WHERE userid = :userid SELECT TOP 1";
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
    <title>Admin Dashboard - Cancelled Tours</title>
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
                <a href="#" class="nav-link active">
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
            <h2>Cancelled Tours</h2>
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
            <a href="tourhistory.php" class="btn-custom" style="text-decoration: none;">Completed Tours</a>
            <a href="cancelledtours.php" class="btn-custom active" style="text-decoration: none;">Cancelled Tours</a>
            <div class="filter-search-container">
                <button type="button" class="btn-filter" id="sortButton">
                        <i class="fas fa-filter"></i>
                </button>
                <div class="search-wrapper">
                    <i class="bi bi-search"></i>
                    <input type="text" class="form-control" id="searchBar" placeholder="Search">
                </div>
            </div>
        </div>
        <div class="row mt-3 d-flex justify-content-center">
            <div class="col-lg-12 col-md-12 col-12 mb-3">
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
                    <div id="pagination-controls" class="pagination-controls d-flex justify-content-start mt-3"></div>
                </div>
                <?php else: ?>
                    <div class="no-cancelled-tours text-center">No cancelled tours found.</div>
                <?php endif; ?>
            </div>
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
    // Global variables for pagination and filtering
    const rowsPerPage = 10;
    let currentPage = 1;
    let allRows = Array.from(document.querySelectorAll('tbody tr'));
    let filteredRows = [...allRows]; // Start with all rows
    
    // Set up the filter modal UI
    setupFilterModal();
    
    // Initialize pagination controls
    initPagination();
    
    // Set up search functionality
    const searchBar = document.getElementById('searchBar');
    if (searchBar) {
        searchBar.addEventListener('input', function() {
            const searchValue = this.value.toLowerCase().trim();
            // Apply both search and existing filters
            applySearchAndFilters(searchValue);
        });
    }
    
    /**
     * Sets up the filter modal UI and event handlers
     */
    function setupFilterModal() {
        const sortButton = document.getElementById('sortButton');
        
        // Create modal overlay
        const modalOverlay = document.createElement('div');
        modalOverlay.className = 'modal-overlay';
        modalOverlay.id = 'modalOverlay';
        modalOverlay.style.display = 'none';
        modalOverlay.style.position = 'fixed';
        modalOverlay.style.top = '0';
        modalOverlay.style.left = '0';
        modalOverlay.style.width = '100%';
        modalOverlay.style.height = '100%';
        modalOverlay.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
        modalOverlay.style.zIndex = '9999';
        document.body.appendChild(modalOverlay);
        
        // Create filter modal
        const filterModal = document.createElement('div');
        filterModal.className = 'filter-modal';
        filterModal.id = 'filterModal';
        filterModal.style.display = 'none';
        filterModal.style.position = 'fixed';
        filterModal.style.backgroundColor = '#E7EBEE';
        filterModal.style.border = '1px solid #ddd';
        filterModal.style.borderRadius = '25px';
        filterModal.style.padding = '30px';
        filterModal.style.boxShadow = '0 4px 8px rgba(0, 0, 0, 0.1)';
        filterModal.style.zIndex = '10000';
        filterModal.style.width = '320px';
        filterModal.style.top = '50%';
        filterModal.style.left = '50%';
        filterModal.style.transform = 'translate(-50%, -50%)';
        
        // Modal content HTML
        filterModal.innerHTML = `
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <h4 style="margin: 0; color: #102E47; font-family: 'Raleway', sans-serif !important; font-weight: bold;">Filter Tours</h4>
                <button id="closeModal" style="background: none; border: none; cursor: pointer; font-size: 18px; color: #102E47;">&times;</button>
            </div>
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: bold; color: #434343; font-family: 'Nunito', sans-serif !important;">Date Range:</label>
                <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                    <div style="flex: 1; min-width: 130px;">
                        <label for="startDate" style="display: block; margin-bottom: 3px; font-weight: bold; color: #757575; font-family: 'Nunito', sans-serif !important;">From:</label>
                        <input type="date" id="startDate" class="filter-input" style="padding: 5px; border: 1px solid #ccc; border-radius: 4px; width: 100%;">
                    </div>
                    <div style="flex: 1; min-width: 130px;">
                        <label for="endDate" style="display: block; margin-bottom: 3px; font-weight: bold; color: #757575; font-family: 'Nunito', sans-serif !important;">To:</label>
                        <input type="date" id="endDate" class="filter-input" style="padding: 5px; border: 1px solid #ccc; border-radius: 4px; width: 100%;">
                    </div>
                </div>
            </div>
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: bold; color: #434343; font-family: 'Nunito', sans-serif !important;">Number of Destinations:</label>
                <div style="display: flex; gap: 10px;">
                    <div style="flex: 1;">
                        <label for="minDestinations" style="display: block; margin-bottom: 3px; font-weight: bold; color: #757575; font-family: 'Nunito', sans-serif !important;">Min:</label>
                        <input type="number" id="minDestinations" min="1" class="filter-input" style="padding: 5px; border: 1px solid #ccc; border-radius: 4px; width: 100%;">
                    </div>
                    <div style="flex: 1;">
                        <label for="maxDestinations" style="display: block; margin-bottom: 3px; font-weight: bold; color: #757575; font-family: 'Nunito', sans-serif !important;">Max:</label>
                        <input type="number" id="maxDestinations" min="1" class="filter-input" style="padding: 5px; border: 1px solid #ccc; border-radius: 4px; width: 100%;">
                    </div>
                </div>
            </div>
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: bold; color: #434343; font-family: 'Nunito', sans-serif !important;">Number of Pax:</label>
                <div style="display: flex; gap: 10px;">
                    <div style="flex: 1;">
                        <label for="minPax" style="display: block; margin-bottom: 3px; font-weight: bold; color: #757575; font-family: 'Nunito', sans-serif !important;">Min:</label>
                        <input type="number" id="minPax" min="1" class="filter-input" style="padding: 5px; border: 1px solid #ccc; border-radius: 4px; width: 100%;">
                    </div>
                    <div style="flex: 1;">
                        <label for="maxPax" style="display: block; margin-bottom: 3px; font-weight: bold; color: #757575; font-family: 'Nunito', sans-serif !important;">Max:</label>
                        <input type="number" id="maxPax" min="1" class="filter-input" style="padding: 5px; border: 1px solid #ccc; border-radius: 4px; width: 100%;">
                    </div>
                </div>
            </div>
            <div style="display: flex; justify-content: space-between; margin-top: 20px;">
                <button id="clearFilters" class="filter-btn" style="font-weight: bold;">Clear All</button>
                <button id="applyFilters" class="filter-btn" style="font-weight: bold;">Apply</button>
            </div>
        `;
        
        document.querySelector('.filter-search-container').appendChild(filterModal);
        
        // Add CSS for the modal
        const style = document.createElement('style');
        style.textContent = `
            .modal-overlay {
                transition: opacity 0.3s ease;
                opacity: 0;
            }
            .modal-overlay.visible {
                opacity: 1;
            }
            .filter-modal {
                transition: opacity 0.3s ease;
                opacity: 0;
                border-radius: 25px;
                z-index: 1001; /* Higher than overlay */
            }
            .filter-modal.visible {
                opacity: 1;
            }
            .active-filter {
                background-color: #102E47 !important;
                color: white !important;
            }
            .filter-btn {
                font-family: Nunito, sans-serif !important;
                background-color: #FFFFFF;
                color: #434343;
                border: 1px solid #102E47;
                padding: 8px 15px;
                border-radius: 25px;
                cursor: pointer;
                transition: all 0.3s ease;
                font-weight: bold;
            }
            .filter-btn:hover {
                background-color: #102E47;
                color: #FFFFFF;
            }
            @media (max-width: 768px) {
                .filter-modal {
                    width: 90% !important;
                    max-width: 320px;
                }
            }
        `;
        document.head.appendChild(style);
        
        // Modal open/close functions
        sortButton.addEventListener('click', function(e) {
            e.stopPropagation();
            const modal = document.getElementById('filterModal');
            
            if (modal.style.display === 'none') {
                openModal();
            } else {
                closeModal();
            }
        });
        
        modalOverlay.addEventListener('click', function() {
            closeModal();
        });
        
        document.getElementById('closeModal').addEventListener('click', function() {
            closeModal();
        });
        
        document.getElementById('filterModal').addEventListener('click', function(e) {
            e.stopPropagation();
        });
        
        // Apply filters button
        document.getElementById('applyFilters').addEventListener('click', function() {
            // Get search value if any
            const searchValue = document.getElementById('searchBar')?.value.toLowerCase().trim() || '';
            
            // Apply both search and filters
            applySearchAndFilters(searchValue);
            closeModal();
            
            // Update filter button appearance
            const hasActiveFilters = checkIfFiltersActive();
            if (hasActiveFilters) {
                sortButton.classList.add('active-filter');
            } else {
                sortButton.classList.remove('active-filter');
            }
        });
        
        // Clear filters button
        document.getElementById('clearFilters').addEventListener('click', function() {
            clearFilters();
            
            // Get search value if any
            const searchValue = document.getElementById('searchBar')?.value.toLowerCase().trim() || '';
            
            // Apply just search after clearing filters
            applySearchAndFilters(searchValue);
            sortButton.classList.remove('active-filter');
        });
    }
    
    /**
     * Opens the filter modal with animation
     */
    function openModal() {
        const modal = document.getElementById('filterModal');
        const overlay = document.getElementById('modalOverlay');
        
        overlay.style.display = 'block';
        modal.style.display = 'block';
        document.body.style.overflow = 'hidden'; 
        
        setTimeout(() => {
            modal.classList.add('visible');
            overlay.classList.add('visible');
        }, 10);
    }
    
    /**
     * Closes the filter modal with animation
     */
    function closeModal() {
        const modal = document.getElementById('filterModal');
        const overlay = document.getElementById('modalOverlay');
        
        modal.classList.remove('visible');
        overlay.classList.remove('visible');
        document.body.style.overflow = ''; 
        
        setTimeout(() => {
            modal.style.display = 'none';
            overlay.style.display = 'none';
        }, 300);
    }
    
    /**
     * Initializes pagination controls
     */
    function initPagination() {
        // If no rows or only one page, don't add pagination
        if (allRows.length === 0 || allRows.length <= rowsPerPage) return;
        
        // Calculate total pages
        const totalPages = Math.ceil(allRows.length / rowsPerPage);
        
        // Create pagination container if it doesn't exist
        const paginationControls = document.getElementById('pagination-controls');
        if (paginationControls) {
            paginationControls.innerHTML = `
                <div class="pagination-wrapper">
                    <button class="btn btn-sm prev-btn" disabled>&lt;</button>
                    <span class="page-indicator">1/${totalPages}</span>
                    <button class="btn btn-sm next-btn">&gt;</button>
                </div>
            `;
            
            // Add event listeners for pagination
            const prevBtn = paginationControls.querySelector('.prev-btn');
            const nextBtn = paginationControls.querySelector('.next-btn');
            
            prevBtn.addEventListener('click', function() {
                if (currentPage > 1) {
                    currentPage--;
                    showPage(currentPage);
                }
            });
            
            nextBtn.addEventListener('click', function() {
                const totalPages = Math.ceil(filteredRows.length / rowsPerPage);
                
                if (currentPage < totalPages) {
                    currentPage++;
                    showPage(currentPage);
                }
            });
        }
        
        // Show initial page
        showPage(currentPage);
    }
    
    /**
     * Checks if any filters are active
     * @returns {boolean} True if any filters are active
     */
    function checkIfFiltersActive() {
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;
        const minDestinations = document.getElementById('minDestinations').value;
        const maxDestinations = document.getElementById('maxDestinations').value;
        const minPax = document.getElementById('minPax').value;
        const maxPax = document.getElementById('maxPax').value;
        
        return startDate || endDate || minDestinations || maxDestinations || minPax || maxPax;
    }
    
    /**
     * Clears all filter inputs
     */
    function clearFilters() {
        document.getElementById('startDate').value = '';
        document.getElementById('endDate').value = '';
        document.getElementById('minDestinations').value = '';
        document.getElementById('maxDestinations').value = '';
        document.getElementById('minPax').value = '';
        document.getElementById('maxPax').value = '';
    }
    
    /**
     * Applies both search and filters together
     * @param {string} searchValue - The search term
     */
    function applySearchAndFilters(searchValue) {
        // Get filter values
        const startDate = document.getElementById('startDate').value ? new Date(document.getElementById('startDate').value) : null;
        const endDate = document.getElementById('endDate').value ? new Date(document.getElementById('endDate').value) : null;
        const minDestinations = document.getElementById('minDestinations').value ? parseInt(document.getElementById('minDestinations').value) : null;
        const maxDestinations = document.getElementById('maxDestinations').value ? parseInt(document.getElementById('maxDestinations').value) : null;
        const minPax = document.getElementById('minPax').value ? parseInt(document.getElementById('minPax').value) : null;
        const maxPax = document.getElementById('maxPax').value ? parseInt(document.getElementById('maxPax').value) : null;
        
        // Start with all rows
        filteredRows = [...allRows];
        
        // Apply search filter if there's a search term
        if (searchValue) {
            filteredRows = filteredRows.filter(row => {
                const rowText = row.textContent.toLowerCase();
                return rowText.includes(searchValue);
            });
        }
        
        // Apply advanced filters
        filteredRows = filteredRows.filter(row => {
            let shouldShow = true;
            
            // Check travel date filter (column index 3)
            if (shouldShow && (startDate || endDate)) {
                const travelDateText = row.cells[3].textContent.trim();
                // Parse date in format "24 Apr 2023"
                const dateParts = travelDateText.split(' ');
                const monthMap = {
                    'Jan': 0, 'Feb': 1, 'Mar': 2, 'Apr': 3, 'May': 4, 'Jun': 5,
                    'Jul': 6, 'Aug': 7, 'Sep': 8, 'Oct': 9, 'Nov': 10, 'Dec': 11
                };
                const day = parseInt(dateParts[0]);
                const month = monthMap[dateParts[1]];
                const year = parseInt(dateParts[2]);
                const travelDate = new Date(year, month, day);
                
                if (startDate && travelDate < startDate) {
                    shouldShow = false;
                }
                if (endDate && travelDate > endDate) {
                    shouldShow = false;
                }
            }
            
            // Check destinations filter (column index 2)
            if (shouldShow && (minDestinations || maxDestinations)) {
                const numDestinations = parseInt(row.cells[2].textContent.trim());
                if (minDestinations && numDestinations < minDestinations) {
                    shouldShow = false;
                }
                if (maxDestinations && numDestinations > maxDestinations) {
                    shouldShow = false;
                }
            }
            
            // Check pax filter (column index 4)
            if (shouldShow && (minPax || maxPax)) {
                const pax = parseInt(row.cells[4].textContent.trim());
                if (minPax && pax < minPax) {
                    shouldShow = false;
                }
                if (maxPax && pax > maxPax) {
                    shouldShow = false;
                }
            }
            
            return shouldShow;
        });
        
        // Update table UI
        updateTableUI();
        
        // Reset to first page
        currentPage = 1;
        showPage(currentPage);
    }
    
    /**
     * Updates table UI based on filtered results
     */
    function updateTableUI() {
        // Show/hide table header
        const tableHeader = document.querySelector('thead');
        if (tableHeader) {
            tableHeader.style.display = filteredRows.length > 0 ? '' : 'none';
        }
        
        // Show/hide no results message
        let noToursMessage = document.querySelector('.no-filter-search');
        const searchValue = document.getElementById('searchBar')?.value?.trim() || '';
        
        if (filteredRows.length === 0) {
            // No results found
            if (!noToursMessage) {
                const tableContainer = document.querySelector('.table-responsive');
                if (tableContainer) {
                    noToursMessage = document.createElement('div');
                    noToursMessage.className = 'no-filter-search';
                    noToursMessage.style.textAlign = 'center';
                    noToursMessage.style.margin = '20px 0';
                    noToursMessage.style.fontSize = '16px';
                    noToursMessage.style.color = '#666';
                    tableContainer.parentNode.appendChild(noToursMessage);
                }
            }
            
            // Update message text based on whether search or filter was applied
            if (noToursMessage) {
                if (searchValue && checkIfFiltersActive()) {
                    noToursMessage.textContent = `No tours match your search "${searchValue}" and filter criteria.`;
                } else if (searchValue) {
                    noToursMessage.textContent = `No tours match your search "${searchValue}".`;
                } else {
                    noToursMessage.textContent = 'No tours match your filter criteria.';
                }
                noToursMessage.style.display = '';
            }
            
            // Hide the table
            const infoBox = document.querySelector('.info-box');
            if (infoBox) {
                infoBox.style.display = 'none';
            }
        } else {
            // Results found - hide message and show table
            if (noToursMessage) {
                noToursMessage.style.display = 'none';
            }
            
            const infoBox = document.querySelector('.info-box');
            if (infoBox) {
                infoBox.style.display = 'block';
            }
        }
    }
    
    /**
     * Shows specific page of results
     * @param {number} page - The page number to show
     */
    function showPage(page) {
        // Calculate total pages
        const totalPages = Math.ceil(filteredRows.length / rowsPerPage);
        
        // First hide all rows
        allRows.forEach(row => {
            row.style.display = 'none';
        });
        
        // Calculate start and end index
        const startIndex = (page - 1) * rowsPerPage;
        const endIndex = Math.min(startIndex + rowsPerPage, filteredRows.length);
        
        // Show only rows for current page
        for (let i = startIndex; i < endIndex; i++) {
            filteredRows[i].style.display = '';
        }
        
        // Update pagination controls
        const pagination = document.getElementById('pagination-controls');
        
        if (pagination) {
            const prevBtn = pagination.querySelector('.prev-btn');
            const nextBtn = pagination.querySelector('.next-btn');
            const pageIndicator = pagination.querySelector('.page-indicator');
            
            if (prevBtn) prevBtn.disabled = page <= 1;
            if (nextBtn) nextBtn.disabled = page >= totalPages;
            if (pageIndicator) pageIndicator.textContent = `${page}/${totalPages || 1}`;
            
            // Show/hide pagination based on number of results
            pagination.style.display = totalPages > 1 ? 'flex' : 'none';
        }
    }
});

// Tour details modal function (kept unchanged from original)
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
</script>
</body>
</html>