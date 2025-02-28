function showModal(row) {
    var tourid = row.getAttribute('data-tourid');
    var userid = row.getAttribute('data-userid');

    fetch('/T-VIBES/src/controllers/employee/tourrequestscontroller.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ tourid: tourid, userid: userid })
    })
    .then(response => response.text()) // Log raw response
    .then(text => {
        console.log("Raw Response:", text);  // Check if it's valid JSON
        return JSON.parse(text); // Try parsing manually
    })
    .then(data => {
        document.getElementById('tourRequestModalLabel').innerText = 'Tour Request of ' + data.name;
        document.getElementById('dateCreated').innerText = data.created_at;
        document.getElementById('numberOfPeople').innerText = data.companions;

        // Clear previous content
        const destinationContainer = document.querySelector('.destination-container');
        destinationContainer.innerHTML = '';

        const stepper = document.querySelector('.stepper');
        stepper.innerHTML = '';

        const estimatedFeesContainer = document.querySelector('.estimated-fees');
        estimatedFeesContainer.innerHTML = '';

        let totalPrice = 0; // Initialize total price
        const pax = parseInt(data.companions) || 1; // Get number of people

        // Add sites to the modal
        if (data.sites && data.sites.length > 0) {
            data.sites.forEach((site, index) => {
                // Stepper
                const step = document.createElement('div');
                step.classList.add('step');
                step.innerHTML = `
                    <div class="circle">${index + 1}</div>
                    ${index < data.sites.length - 1 ? '<div class="dashed-line"></div>' : ''}`
                ;
                stepper.appendChild(step);

                // Destination card
                const card = document.createElement('div');
                card.classList.add('destination-card');
                card.innerHTML = `
                    <div class="image-placeholder">
                        <img src="/T-VIBES/public/uploads/${site.siteimage}"></img>
                    </div>
                    <div class="destination-info">
                        <h6>${site.sitename}</h6>
                    </div>`
                ;
                destinationContainer.appendChild(card);

                // Estimated Fees Section
                const feeItem = document.createElement('p');
                feeItem.innerText = `${site.sitename}: ₱${site.price}`;
                estimatedFeesContainer.appendChild(feeItem);

                totalPrice += parseFloat(site.price); // Add to total price
            });

            // Calculate final total price (total sites cost * pax)
            const finalTotal = totalPrice * pax;

            // Update total price in modal
            document.querySelector('.total-price').innerHTML = `₱${totalPrice.toFixed(2)} x ${pax} Pax = <strong id="estimatedFees">₱${finalTotal.toFixed(2)}*</strong>`;
        } else {
            stepper.innerHTML = "<p>No destinations found.</p>";
            estimatedFeesContainer.innerHTML = "<p>No fees available.</p>";
            document.querySelector('.total-price').innerHTML = "₱ 0.00 x 0 Pax = <strong id='estimatedFees'>₱ 0.00 *</strong>";
        }
        document.querySelector(".btn-custom.accept").setAttribute("data-tourid", tourid);
        document.querySelector(".btn-custom.accept").setAttribute("data-userid", userid);
        document.querySelector(".btn-custom.decline").setAttribute("data-tourid", tourid);
        document.querySelector(".btn-custom.decline").setAttribute("data-userid", userid);

        // Show the modal
        var modal = new bootstrap.Modal(document.getElementById('tourRequestModal'));
        modal.show();
    })
    .catch(error => console.error('Error:', error));
}


document.addEventListener("DOMContentLoaded", function () {
    document.querySelector(".btn-custom:nth-child(1)").addEventListener("click", function () {

        
        const tourid = this.getAttribute("data-tourid");
        const userid = this.getAttribute("data-userid");

         Swal.fire({
            iconHtml: '<i class="fas fa-thumbs-up"></i>',
            title: "Accept Tour Request?",
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
                // Show loading spinner
                Swal.fire({
                    title: 'Processing Request',
                    html: 'Sending confirmation email... Do not close this window.',
                    allowOutsideClick: false,
                    customClass: {
                        title: "swal2-title-custom",
                        icon: "swal2-icon-custom",
                        popup: "swal-custom-popup"
                    },
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                fetch("/T-VIBES/src/controllers/employee/tourrequestscontroller.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                    },
                    body: JSON.stringify({ action: "accept", tourid: tourid, userid: userid }),
                })
                .then(response => response.text()) // Log raw response
                .then(text => {
                    console.log("Raw Response:", text);  // Check if it's valid JSON
                    return JSON.parse(text); // Try parsing manually
                })
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            iconHtml: '<i class="fas fa-circle-check"></i>',
                            title: "Successfully Accepted Tour Request!",
                            timer: 3000,
                            showConfirmButton: false,
                            customClass: {
                                title: "swal2-title-custom",
                                icon: "swal2-icon-custom",
                                popup: "swal-custom-popup"
                            }
                        }).then(() => {
                            // Reload the page to update the list
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            iconHtml: '<i class="fas fa-exclamation-circle"></i>',
                            title: "Failed to Accept Tour Request. Please try again.",
                            timer: 3000,
                            showConfirmButton: false,
                            customClass: {
                                title: "swal2-title-custom",
                                icon: "swal2-icon-custom",
                                popup: "swal-custom-popup"
                            }
                        });
                    }
                })
                .catch(error => console.error("Error:", error));
            }
        });
    });

    // Decline Button with Reason for Cancellation
    document.querySelector(".btn-custom:nth-child(2)").addEventListener("click", function () {
        const tourid = this.getAttribute("data-tourid");
        const userid = this.getAttribute("data-userid");

        Swal.fire({
            iconHtml: '<i class="fas fa-thumbs-down"></i>',
            title: "Decline Tour Request?",
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
                // Store tourid and userid in the cancel modal
            document.getElementById("cancelReasonModal").setAttribute("data-tourid", tourid);
            document.getElementById("cancelReasonModal").setAttribute("data-userid", userid);
            
            // Show Reason for Cancellation modal
            var cancelModal = new bootstrap.Modal(document.getElementById("cancelReasonModal"));
            cancelModal.show();
            }
        });
    });

    // Submit Reason for Cancellation
    document.getElementById("submitCancelReason").addEventListener("click", function () {
        let reason = document.getElementById("cancelReasonInput").value.trim();

        const tourid = document.getElementById("cancelReasonModal").getAttribute("data-tourid");
        const userid = document.getElementById("cancelReasonModal").getAttribute("data-userid");

        if (reason.trim() === "") {
            Swal.fire({
                iconHtml: '<i class="fas fa-exclamation-circle"></i>',
                title: "Please enter a reason!",
                timer: 3000,
                showConfirmButton: false,
                customClass: {
                    title: "swal2-title-custom",
                    icon: "swal2-icon-custom",
                    popup: "swal-custom-popup"
                }
            });
            return;
        }
        Swal.fire({
            title: 'Processing Request',
            html: 'Sending decline notification... Do not close this window.',
            allowOutsideClick: false,
            customClass: {
                title: "swal2-title-custom",
                icon: "swal2-icon-custom",
                popup: "swal-custom-popup"
            },
            didOpen: () => {
                Swal.showLoading();
            }
        });
        fetch("/T-VIBES/src/controllers/employee/tourrequestscontroller.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify({ action: "decline", tourid: tourid, userid: userid, reason: reason }),
        })
        .then(response => response.text()) // Log raw response
        .then(text => {
            console.log("Raw Response:", text);  // Check if it's valid JSON
            return JSON.parse(text); // Try parsing manually
        })
        .then(data => {
            if (data.success) {
                Swal.fire({
                    iconHtml: '<i class="fas fa-circle-check"></i>',
                    title: "Successfully Declined Tour Request.",
                    timer: 3000,
                    showConfirmButton: false,
                    customClass: {
                        title: "swal2-title-custom",
                        icon: "swal2-icon-custom",
                        popup: "swal-custom-popup"
                    }
                }).then(() => {
                    // Reload the page to update the list
                    location.reload();
                });
            } else {
                Swal.fire({
                    iconHtml: '<i class="fas fa-exclamation-circle"></i>',
                    title: "Failed to Decline Tour Request. Please try again.",
                    timer: 3000,
                    showConfirmButton: false,
                    customClass: {
                        title: "swal2-title-custom",
                        icon: "swal2-icon-custom",
                        popup: "swal-custom-popup"
                    }
                });
            }
        })

        // Close the modal
        var cancelModal = bootstrap.Modal.getInstance(document.getElementById("cancelReasonModal"));
        cancelModal.hide();

    });
});
