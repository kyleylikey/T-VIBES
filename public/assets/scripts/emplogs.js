document.addEventListener('DOMContentLoaded', function() {
    const searchBar = document.getElementById("searchBar");
    const tableBody = document.querySelector("#logTable tbody");
    const paginationContainer = document.querySelector(".pagination");
    const noLogsMessage = document.querySelector(".no-logs") || document.createElement("div");
    let searchTimeout;
    
    if (!document.querySelector(".no-logs")) {
        noLogsMessage.className = "no-logs text-center";
        noLogsMessage.textContent = "No matching employee logs found.";
        document.querySelector(".table-responsive").appendChild(noLogsMessage);
        noLogsMessage.style.display = "none";
    }

    // Add event listener for search input
    searchBar.addEventListener("keyup", function() {
        // Clear previous timeout to prevent multiple requests
        if (searchTimeout) {
            clearTimeout(searchTimeout);
        }
        
        // Add delay to prevent requests on every keystroke
        searchTimeout = setTimeout(function() {
            const searchTerm = searchBar.value.trim();
            performSearch(searchTerm);
        }, 300);
    });

    // Function to perform search
    function performSearch(searchTerm) {
        // Show loading indicator
        const tableContainer = document.querySelector(".table-responsive");
        const loadingSpinner = document.createElement("div");
        loadingSpinner.className = "text-center mt-3 mb-3";
        loadingSpinner.innerHTML = '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>';
        
        document.getElementById("logTable").style.display = "none";
        if (tableContainer.querySelector(".spinner-border")) {
            tableContainer.querySelector(".spinner-border").parentNode.remove();
        }
        tableContainer.prepend(loadingSpinner);
        paginationContainer.parentElement.style.display = "none";

        // Fetch search results from server
        fetch(`employeelogs.php?search=${encodeURIComponent(searchTerm)}&page=1`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            // Remove loading spinner
            loadingSpinner.remove();
            
            // Process response
            if (data.logs.length > 0) {
                // Update table with search results
                updateTable(data.logs);
                
                // Update pagination
                updatePagination(data.currentPage, data.totalPages, searchTerm);
                
                document.getElementById("logTable").style.display = "table";
                noLogsMessage.style.display = "none";
            } else {
                // Show no results message
                document.getElementById("logTable").style.display = "none";
                noLogsMessage.style.display = "block";
                paginationContainer.parentElement.style.display = "none";
            }
        })
        .catch(error => {
            console.error('Error:', error);
            loadingSpinner.remove();
            
            // Show error message
            noLogsMessage.textContent = "Error loading search results. Please try again.";
            noLogsMessage.style.display = "block";
            document.getElementById("logTable").style.display = "none";
            paginationContainer.parentElement.style.display = "none";
        });
    }

    // Function to update table with new data
    function updateTable(logs) {
        tableBody.innerHTML = '';
        
        logs.forEach(log => {
            const row = document.createElement('tr');
            
            // Format date and time
            const datetime = new Date(log.datetime);
            const formattedDate = datetime.toLocaleDateString('en-GB', {
                day: 'numeric',
                month: 'short',
                year: 'numeric'
            });
            const formattedTime = datetime.toLocaleTimeString('en-GB', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
            
            row.innerHTML = `
                <td>${log.name}</td>
                <td>${log.action}</td>
                <td>${formattedDate}</td>
                <td>${formattedTime}</td>
            `;
            
            tableBody.appendChild(row);
        });
    }

    // Function to update pagination
    function updatePagination(currentPage, totalPages, searchTerm) {
        paginationContainer.innerHTML = '';
        
        if (totalPages > 1) {
            paginationContainer.parentElement.style.display = "flex";
            
            // Previous button
            if (currentPage > 1) {
                const prevLi = document.createElement('li');
                prevLi.className = 'page-item';
                prevLi.innerHTML = `<a class="page-link" href="?search=${encodeURIComponent(searchTerm)}&page=${currentPage - 1}" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a>`;
                prevLi.addEventListener('click', function(e) {
                    e.preventDefault();
                    fetchPage(currentPage - 1, searchTerm);
                });
                paginationContainer.appendChild(prevLi);
            }
            
            // Page numbers
            for (let i = 1; i <= totalPages; i++) {
                const li = document.createElement('li');
                li.className = `page-item ${i === currentPage ? 'active' : ''}`;
                li.innerHTML = `<a class="page-link" href="?search=${encodeURIComponent(searchTerm)}&page=${i}">${i}</a>`;
                li.addEventListener('click', function(e) {
                    e.preventDefault();
                    fetchPage(i, searchTerm);
                });
                paginationContainer.appendChild(li);
            }
            
            // Next button
            if (currentPage < totalPages) {
                const nextLi = document.createElement('li');
                nextLi.className = 'page-item';
                nextLi.innerHTML = `<a class="page-link" href="?search=${encodeURIComponent(searchTerm)}&page=${currentPage + 1}" aria-label="Next"><span aria-hidden="true">&raquo;</span></a>`;
                nextLi.addEventListener('click', function(e) {
                    e.preventDefault();
                    fetchPage(currentPage + 1, searchTerm);
                });
                paginationContainer.appendChild(nextLi);
            }
        } else {
            paginationContainer.parentElement.style.display = "none";
        }
    }

    // Function to fetch a specific page
    function fetchPage(page, searchTerm) {
        const tableContainer = document.querySelector(".table-responsive");
        const loadingSpinner = document.createElement("div");
        loadingSpinner.className = "text-center mt-3 mb-3";
        loadingSpinner.innerHTML = '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>';
        
        document.getElementById("logTable").style.display = "none";
        if (tableContainer.querySelector(".spinner-border")) {
            tableContainer.querySelector(".spinner-border").parentNode.remove();
        }
        tableContainer.prepend(loadingSpinner);

        fetch(`employeelogs.php?search=${encodeURIComponent(searchTerm)}&page=${page}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            loadingSpinner.remove();
            updateTable(data.logs);
            updatePagination(data.currentPage, data.totalPages, searchTerm);
            document.getElementById("logTable").style.display = "table";
        })
        .catch(error => {
            console.error('Error:', error);
            loadingSpinner.remove();
        });
    }
});