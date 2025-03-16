function showModal(tourData) {
    document.getElementById('destination-container').innerHTML = "";
    document.getElementById('stepper-container').innerHTML = "";
    document.getElementById('estimated-fees').innerHTML = "";

    let totalPrice = 0;
    let companions = tourData[0].companions;
    let dateCreated = new Date(tourData[0].submitted_on).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });

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
                    <p><i class="bi bi-calendar"></i> ${new Date(tour.travel_date).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' })}</p>
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