function filterReviews(status) {
    window.location.href = '?status=' + status;
}

let currentReviewId;

function updateReviewStatus(status, reviewId) {
    Swal.fire({
        iconHtml: status === 'displayed' ? '<i class="fas fa-thumbs-up"></i>' : '<i class="fas fa-thumbs-down"></i>',
        title: status === 'displayed' ? "Display User Review?" : "Archive User Review?",
        showCancelButton: true,
        confirmButtonText: "Yes",
        cancelButtonText: "No",
        customClass: {
            title: "swal2-title-custom",
            icon: "swal2-icon-custom",
            popup: "swal-custom-popup",
            confirmButton: "swal-custom-btn",
            cancelButton: "swal-custom-btn"
        }
    }).then((result) => {
        if (result.isConfirmed) {
            fetch("../../controllers/reviewscontroller.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: "review_id=" + reviewId + "&status=" + status
            }).then(() => {
                Swal.fire({
                    iconHtml: '<i class="fas fa-circle-check"></i>',
                    title: status === 'displayed' ? "Successfully Displayed User Review!" : "Successfully Archived User Review!",
                    timer: 3000,
                    showConfirmButton: false,
                    customClass: {
                        title: "swal2-title-custom",
                        icon: "swal2-icon-custom",
                        popup: "swal-custom-popup"
                    }
                }).then(() => {
                    window.location.reload();
                });
            });
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    const rowsPerPage = 10;
    let currentPage = 1;
    let filteredRows = [];
    
    function initPagination() {
        const tableRows = document.querySelectorAll('tbody tr');
        filteredRows = Array.from(tableRows).filter(row => row.style.display !== 'none');
        const totalPages = Math.ceil(filteredRows.length / rowsPerPage);
        
        const noReviewsMessage = document.querySelector('.no-filter-search');
        const noReviewsVisible = noReviewsMessage && noReviewsMessage.style.display !== 'none';
        
        if (!document.querySelector('.pagination-controls')) {
            const tableContainer = document.querySelector('.table-responsive');
            if (tableContainer) {
                const paginationControls = document.createElement('div');
                paginationControls.className = 'pagination-controls';
                paginationControls.style.display = noReviewsVisible ? 'none' : 'flex';
                paginationControls.style.justifyContent = 'center';
                paginationControls.style.alignItems = 'center';
                paginationControls.style.marginTop = '-5px'; 
                paginationControls.style.userSelect = 'none';
                
                const prevBtn = document.createElement('button');
                prevBtn.innerHTML = '<strong>&lt;</strong>'; 
                prevBtn.className = 'pagination-btn';
                prevBtn.id = 'prevPage';
                prevBtn.style.backgroundColor = 'transparent';
                prevBtn.style.color = '#EC6350'; 
                prevBtn.style.border = '1px solid #EC6350'; 
                prevBtn.style.borderRadius = '50%';
                prevBtn.style.width = '32px';
                prevBtn.style.height = '32px';
                prevBtn.style.margin = '0 10px';
                prevBtn.style.cursor = 'pointer';
                prevBtn.style.display = 'flex';
                prevBtn.style.justifyContent = 'center';
                prevBtn.style.alignItems = 'center';
                prevBtn.style.fontSize = '16px';
                prevBtn.style.fontWeight = 'bold';
                prevBtn.style.transition = 'all 0.2s ease'; 
                
                prevBtn.addEventListener('mouseover', function() {
                    if (!this.disabled) {
                        this.style.backgroundColor = '#EC6350';
                        this.style.color = '#FFFFFF';
                    }
                });
                
                prevBtn.addEventListener('mouseout', function() {
                    if (!this.disabled) {
                        this.style.backgroundColor = 'transparent';
                        this.style.color = '#EC6350';
                    }
                });
                
                const pageInfo = document.createElement('div');
                pageInfo.id = 'pageInfo';
                pageInfo.style.margin = '0 15px';
                pageInfo.style.fontFamily = 'Nunito, sans-serif';
                pageInfo.style.color = '#434343';
                
                const nextBtn = document.createElement('button');
                nextBtn.innerHTML = '<strong>&gt;</strong>';
                nextBtn.className = 'pagination-btn';
                nextBtn.id = 'nextPage';
                nextBtn.style.backgroundColor = 'transparent';
                nextBtn.style.color = '#EC6350';
                nextBtn.style.border = '1px solid #EC6350'; 
                nextBtn.style.borderRadius = '50%';
                nextBtn.style.width = '32px';
                nextBtn.style.height = '32px';
                nextBtn.style.margin = '0 10px';
                nextBtn.style.cursor = 'pointer';
                nextBtn.style.display = 'flex';
                nextBtn.style.justifyContent = 'center';
                nextBtn.style.alignItems = 'center';
                nextBtn.style.fontSize = '16px';
                nextBtn.style.fontWeight = 'bold';
                nextBtn.style.transition = 'all 0.2s ease';
                
                nextBtn.addEventListener('mouseover', function() {
                    if (!this.disabled) {
                        this.style.backgroundColor = '#EC6350';
                        this.style.color = '#FFFFFF';
                    }
                });
                
                nextBtn.addEventListener('mouseout', function() {
                    if (!this.disabled) {
                        this.style.backgroundColor = 'transparent';
                        this.style.color = '#EC6350';
                    }
                });
                
                paginationControls.appendChild(prevBtn);
                paginationControls.appendChild(pageInfo);
                paginationControls.appendChild(nextBtn);
                
                tableContainer.parentNode.appendChild(paginationControls);
                
                prevBtn.addEventListener('click', function() {
                    if (currentPage > 1) {
                        currentPage--;
                        showPage(currentPage);
                    }
                });
                
                nextBtn.addEventListener('click', function() {
                    if (currentPage < totalPages) {
                        currentPage++;
                        showPage(currentPage);
                    }
                });
            }
        } else {
            const paginationControls = document.querySelector('.pagination-controls');
            if (paginationControls) {
                paginationControls.style.display = noReviewsVisible ? 'none' : 'flex';
            }
        }
        
        if (currentPage > totalPages && totalPages > 0) {
            currentPage = 1;
        }
        
        showPage(currentPage);
    }
    
    function showPage(page) {
        const startIndex = (page - 1) * rowsPerPage;
        const endIndex = startIndex + rowsPerPage;
        const totalPages = Math.ceil(filteredRows.length / rowsPerPage);
        
        filteredRows.forEach(row => {
            row.style.display = 'none';
        });
        
        for (let i = startIndex; i < endIndex && i < filteredRows.length; i++) {
            filteredRows[i].style.display = '';
        }
        
        const pageInfo = document.getElementById('pageInfo');
        if (pageInfo) {
            pageInfo.textContent = page + ' / ' + (totalPages || 1);
        }
        
        const prevBtn = document.getElementById('prevPage');
        const nextBtn = document.getElementById('nextPage');
        
        if (prevBtn) {
            prevBtn.disabled = page === 1;
            prevBtn.style.opacity = page === 1 ? '0.5' : '1';
            prevBtn.style.cursor = page === 1 ? 'default' : 'pointer';
            
            if (page === 1) {
                prevBtn.style.backgroundColor = 'transparent';
                prevBtn.style.color = '#EC6350';
                prevBtn.style.border = '1px solid #EC6350';
            }
        }
        
        if (nextBtn) {
            nextBtn.disabled = page === totalPages || totalPages === 0;
            nextBtn.style.opacity = (page === totalPages || totalPages === 0) ? '0.5' : '1';
            nextBtn.style.cursor = (page === totalPages || totalPages === 0) ? 'default' : 'pointer';
            
            if (page === totalPages || totalPages === 0) {
                nextBtn.style.backgroundColor = 'transparent';
                nextBtn.style.color = '#EC6350';
                nextBtn.style.border = '1px solid #EC6350';
            }
        }
        
        updateRowNumbers();
    }
    
    function updateRowNumbers() {
        const visibleRows = Array.from(document.querySelectorAll('tbody tr')).filter(row => row.style.display !== 'none');
        const startIndex = (currentPage - 1) * rowsPerPage;
        
        visibleRows.forEach((row, index) => {
            const rowNumberCell = row.cells[0];
            if (rowNumberCell) {
                rowNumberCell.textContent = startIndex + index + 1;
            }
        });
    }
    
    function updatePaginationVisibility() {
        currentPage = 1;
        
        setTimeout(() => {
            const noReviewsMessage = document.querySelector('.no-filter-search');
            const noReviewsVisible = noReviewsMessage && noReviewsMessage.style.display !== 'none';
            const paginationControls = document.querySelector('.pagination-controls');
            
            if (paginationControls) {
                paginationControls.style.display = noReviewsVisible ? 'none' : 'flex';
            }
            
            initPagination();
        }, 100);
    }
    
    initPagination();
    
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();
            const tableRows = document.querySelectorAll('tbody tr');
            const tableHeader = document.querySelector('thead');
            
            let visibleRowCount = 0;
            
            tableRows.forEach(row => {
                let matchFound = false;
                const rowNumber = row.cells[0].textContent.toLowerCase();
                const tourSite = row.cells[1].textContent.toLowerCase();
                const author = row.cells[2].textContent.toLowerCase();
                const submittedOn = row.cells[3].textContent.toLowerCase();
                const review = row.cells[4].textContent.toLowerCase();
                const rate = row.cells[5].textContent.toLowerCase();
                
                if (rowNumber.includes(searchTerm) ||
                    tourSite.includes(searchTerm) ||
                    author.includes(searchTerm) ||
                    submittedOn.includes(searchTerm) ||
                    review.includes(searchTerm) ||
                    rate.includes(searchTerm)) {
                    matchFound = true;
                }
                
                if (matchFound) {
                    row.style.display = '';
                    visibleRowCount++;
                } else {
                    row.style.display = 'none';
                }
            });
            
            if (tableHeader) {
                tableHeader.style.display = visibleRowCount > 0 ? '' : 'none';
            }
            
            const noReviewsMessage = document.querySelector('.no-filter-search');
            if (noReviewsMessage) {
                if (visibleRowCount === 0) {
                    noReviewsMessage.textContent = 'No matching reviews found.';
                    noReviewsMessage.style.display = '';
                } else {
                    noReviewsMessage.style.display = 'none';
                }
            } else if (visibleRowCount === 0) {
                const tableContainer = document.querySelector('.table-responsive');
                if (tableContainer) {
                    const noResults = document.createElement('div');
                    noResults.className = 'no-filter-search';
                    noResults.textContent = 'No matching reviews found.';
                    noResults.style.cssText = 'text-align: center; padding: 20px; font-weight: bold;';
                    tableContainer.parentNode.appendChild(noResults);
                }
            }
            
            updatePaginationVisibility();
        });
        
        const searchContainer = document.querySelector('.search-container');
        if (searchContainer) {
            const clearButton = document.createElement('i');
            clearButton.className = 'fas fa-times clear-search';
            clearButton.style.display = 'none';
            clearButton.style.position = 'absolute';
            clearButton.style.right = '30px';
            clearButton.style.top = '50%';
            clearButton.style.transform = 'translateY(-50%)';
            clearButton.style.cursor = 'pointer';
            clearButton.style.color = '#666';

            searchContainer.appendChild(clearButton);
            searchInput.addEventListener('input', function() {
                clearButton.style.display = this.value ? 'block' : 'none';
            });
            clearButton.addEventListener('click', function() {
                searchInput.value = '';
                searchInput.dispatchEvent(new Event('input'));
                this.style.display = 'none';
            });
        }
    }
    
    const sortButton = document.getElementById('sortButton');
    
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
    filterModal.innerHTML = `
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
            <h4 style="margin: 0; color: #102E47; font-family: 'Raleway', sans-serif !important; font-weight: bold;">Filter Reviews</h4>
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
            <label for="tourSiteFilter" style="display: block; margin-bottom: 5px; font-weight: bold; color: #434343; font-family: 'Nunito', sans-serif !important;">Tour Site:</label>
            <input type="text" id="tourSiteFilter" placeholder="Enter Site Name" class="filter-input" style="padding: 5px; border: 1px solid #ccc; border-radius: 4px; width: 100%;">
        </div>
        <div style="margin-bottom: 15px;">
            <label for="authorFilter" style="display: block; margin-bottom: 5px; font-weight: bold; color: #434343; font-family: 'Nunito', sans-serif !important;">Author:</label>
            <input type="text" id="authorFilter" placeholder="Enter Author Name" class="filter-input" style="padding: 5px; border: 1px solid #ccc; border-radius: 4px; width: 100%;">
        </div>
        <div style="margin-bottom: 15px;">
            <label style="display: block; margin-bottom: 5px; font-weight: bold; color: #434343; font-family: 'Nunito', sans-serif !important;">Rating:</label>
            <div style="display: flex; gap: 10px;">
                <div style="flex: 1;">
                    <label for="minRating" style="display: block; margin-bottom: 3px; font-weight: bold; color: #757575; font-family: 'Nunito', sans-serif !important;">Min:</label>
                    <input type="number" id="minRating" min="1" max="5" class="filter-input" style="padding: 5px; border: 1px solid #ccc; border-radius: 4px; width: 100%;">
                </div>
                <div style="flex: 1;">
                    <label for="maxRating" style="display: block; margin-bottom: 3px; font-weight: bold; color: #757575; font-family: 'Nunito', sans-serif !important;">Max:</label>
                    <input type="number" id="maxRating" min="1" max="5" class="filter-input" style="padding: 5px; border: 1px solid #ccc; border-radius: 4px; width: 100%;">
                </div>
            </div>
        </div>
        <div style="display: flex; justify-content: space-between; margin-top: 20px;">
            <button id="clearFilters" class="filter-btn" style="font-weight: bold;">Clear All</button>
            <button id="applyFilters" class="filter-btn" style="font-weight: bold;">Apply</button>
        </div>
    `;
    document.querySelector('.filter-search-container').appendChild(filterModal);
    
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
    
    document.getElementById('applyFilters').addEventListener('click', function() {
        applyFilters();
        closeModal();
        
        const hasActiveFilters = checkIfFiltersActive();
        if (hasActiveFilters) {
            sortButton.classList.add('active-filter');
        } else {
            sortButton.classList.remove('active-filter');
        }
        
        updatePaginationVisibility();
    });
    
    document.getElementById('clearFilters').addEventListener('click', function() {
        clearFilters();
        applyFilters();
        sortButton.classList.remove('active-filter');
        
        updatePaginationVisibility();
    });
    
    function updateNoResultsMessage(visibleRowCount) {
        const noReviewsMessage = document.querySelector('.no-filter-search');
        if (visibleRowCount === 0) {
            if (noReviewsMessage) {
                noReviewsMessage.textContent = 'No matching reviews found.';
                noReviewsMessage.style.display = '';
            } else {
                const tableContainer = document.querySelector('.table-responsive');
                if (tableContainer) {
                    const noResults = document.createElement('div');
                    noResults.className = 'no-filter-search';
                    noResults.textContent = 'No matching reviews found.';
                    noResults.style.cssText = 'text-align: center; padding: 20px; font-weight: bold;';
                    tableContainer.parentNode.appendChild(noResults);
                }
            }
        } else if (noReviewsMessage) {
            noReviewsMessage.style.display = 'none';
        }
    }
    
    function checkIfFiltersActive() {
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;
        const tourSiteFilter = document.getElementById('tourSiteFilter').value.trim();
        const authorFilter = document.getElementById('authorFilter').value.trim();
        const minRating = document.getElementById('minRating').value;
        const maxRating = document.getElementById('maxRating').value;
        
        return startDate || endDate || tourSiteFilter || authorFilter || minRating || maxRating;
    }
    
    function clearFilters() {
        document.getElementById('startDate').value = '';
        document.getElementById('endDate').value = '';
        document.getElementById('tourSiteFilter').value = '';
        document.getElementById('authorFilter').value = '';
        document.getElementById('minRating').value = '';
        document.getElementById('maxRating').value = '';
    }
    
    function applyFilters() {
        const startDate = document.getElementById('startDate').value ? new Date(document.getElementById('startDate').value) : null;
        const endDate = document.getElementById('endDate').value ? new Date(document.getElementById('endDate').value) : null;
        const tourSiteFilter = document.getElementById('tourSiteFilter').value.trim().toLowerCase();
        const authorFilter = document.getElementById('authorFilter').value.trim().toLowerCase();
        const minRating = document.getElementById('minRating').value ? parseInt(document.getElementById('minRating').value) : null;
        const maxRating = document.getElementById('maxRating').value ? parseInt(document.getElementById('maxRating').value) : null;
        
        const tableRows = document.querySelectorAll('tbody tr');
        const tableHeader = document.querySelector('thead');
        let visibleRowCount = 0;
        
        tableRows.forEach(row => {
            let shouldShow = true;
            
            if (shouldShow && (startDate || endDate)) {
                const dateCell = row.cells[3].textContent.trim();
                const dateParts = dateCell.split(' ');
                const monthMap = {
                    'Jan': 0, 'Feb': 1, 'Mar': 2, 'Apr': 3, 'May': 4, 'Jun': 5,
                    'Jul': 6, 'Aug': 7, 'Sep': 8, 'Oct': 9, 'Nov': 10, 'Dec': 11
                };
                const day = parseInt(dateParts[0]);
                const month = monthMap[dateParts[1]];
                const year = parseInt(dateParts[2]);
                const rowDate = new Date(year, month, day);
                
                if (startDate && rowDate < startDate) {
                    shouldShow = false;
                }
                if (endDate && rowDate > endDate) {
                    shouldShow = false;
                }
            }
            
            if (shouldShow && tourSiteFilter) {
                const tourSite = row.cells[1].textContent.toLowerCase();
                if (!tourSite.includes(tourSiteFilter)) {
                    shouldShow = false;
                }
            }
            
            if (shouldShow && authorFilter) {
                const author = row.cells[2].textContent.toLowerCase();
                if (!author.includes(authorFilter)) {
                    shouldShow = false;
                }
            }
            
            if (shouldShow && (minRating !== null || maxRating !== null)) {
                const ratingCell = row.cells[5].textContent.trim();
                if (ratingCell !== 'N/A') {
                    const rating = parseFloat(ratingCell);
                    if (minRating !== null && rating < minRating) {
                        shouldShow = false;
                    }
                    if (maxRating !== null && rating > maxRating) {
                        shouldShow = false;
                    }
                } else if (minRating !== null) {
                    shouldShow = false;
                }
            }
            
            if (shouldShow) {
                row.style.display = '';
                visibleRowCount++;
            } else {
                row.style.display = 'none';
            }
        });
        
        if (tableHeader) {
            tableHeader.style.display = visibleRowCount > 0 ? '' : 'none';
        }
        
        updateNoResultsMessage(visibleRowCount);
        
        currentPage = 1;
        initPagination();
    }
    
    const originalCreateElement = document.createElement;
    document.createElement = function(tag) {
        const element = originalCreateElement.call(document, tag);
        if (tag.toLowerCase() === 'div') {
            const originalSetAttribute = element.setAttribute;
            element.setAttribute = function(name, value) {
                originalSetAttribute.call(this, name, value);
                if (name === 'class' && value === 'no-filter-search') {
                    setTimeout(updatePaginationVisibility, 100);
                }
                return this;
            };
        }
        return element;
    };
});