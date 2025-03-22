document.addEventListener("DOMContentLoaded", function () {
    var tourItems = document.querySelectorAll(".tour-item");
    var editTourForm = document.getElementById("editTourForm");
    var saveTourChanges = document.getElementById("saveTourChanges");
    var submitCancelReason = document.getElementById("submitCancelReason");
    var cancelTourIdField = document.getElementById("cancelTourId");
    var cancelReasonInput = document.getElementById("cancelReasonInput");

    function updateTourDetails(tourItem) {
        var tourDetails = document.querySelector(".tour-details");
        var tourId = tourItem.getAttribute("data-tourid");
        var tourName = tourItem.querySelector("strong").textContent;
        var tourSites = tourItem.getAttribute("data-sites");
        var tourDate = tourItem.getAttribute("data-date");
        var tourPax = tourItem.getAttribute("data-pax");

        tourDetails.innerHTML = `
            <strong>${tourName}</strong><br>
            <span class="tour-locations">${tourSites.replace(/,/g, '<br>')}</span>
            <br><br>
            <div class="tour-info">
                <div><strong>Tour Date:</strong> <span>${tourDate}</span></div>
                <div><strong>Pax:</strong> <span>${tourPax}</span></div>
            </div>
            <br><br>
            <div class="button-container">
                <button class="btn-custom edit-tour" style="cursor: pointer;">Edit</button>
                <button class="btn-custom cancel-tour" style="cursor: pointer;">Cancel</button>
            </div>
        `;
    }

    if (tourItems.length > 0) {
        var firstTour = tourItems[0];
        firstTour.classList.add("active");
        updateTourDetails(firstTour);
    }

    tourItems.forEach(function (item) {
        item.addEventListener("click", function () {
            tourItems.forEach(i => i.classList.remove("active"));
            this.classList.add("active");
            updateTourDetails(this);
        });
    });

    document.addEventListener("click", function (event) {
        if (event.target.matches(".edit-tour")) {
            var activeTour = document.querySelector(".tour-item.active");
            if (!activeTour) return;

            var tourId = activeTour.getAttribute("data-tourid");
            var tourSites = activeTour.getAttribute("data-sites");
            var tourDate = activeTour.getAttribute("data-date");
            var tourPax = activeTour.getAttribute("data-pax");

            document.getElementById("editTourId").value = tourId;
            document.getElementById("tourSites").value = tourSites;
            document.getElementById("tourDate").value = tourDate;
            document.getElementById("tourPax").value = tourPax;

            var modal = new bootstrap.Modal(document.getElementById("upcomingToursModal"));
            modal.show();
        }
    });

    saveTourChanges.addEventListener("click", function () {
        var tourId = document.getElementById("editTourId").value;
        var tourDate = document.getElementById("tourDate").value;
        var tourPax = document.getElementById("tourPax").value;

        Swal.fire({
            iconHtml: '<i class="fas fa-thumbs-up"></i>',
            title: "Confirm Changes?",
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
                fetch("../../controllers/upcomingtourscontroller.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: `editTour=true&tourId=${encodeURIComponent(tourId)}&tourDate=${encodeURIComponent(tourDate)}&tourPax=${encodeURIComponent(tourPax)}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === "success") {
                        Swal.fire({
                            iconHtml: '<i class="fas fa-circle-check"></i>',
                            title: "Tour Successfully Edited!",
                            timer: 3000,
                            showConfirmButton: false,
                            customClass: {
                                title: "swal2-title-custom",
                                icon: "swal2-icon-custom",
                                popup: "swal-custom-popup"
                            }
                        }).then(() => location.reload());
                    } else {
                        Swal.fire({
                            iconHtml: '<i class="fas fa-exclamation-circle"></i>',
                            title: "Failed to update tour.",
                            timer: 3000,
                            showConfirmButton: false,
                            customClass: {
                                title: "swal2-title-custom",
                                icon: "swal2-icon-custom",
                                popup: "swal-custom-popup"
                            }
                        });
                    }
                });
            }
        });
    });

    document.addEventListener("click", function (event) {
        if (event.target.matches(".cancel-tour")) {
            var activeTour = document.querySelector(".tour-item.active");
            if (!activeTour) return;

            var tourId = activeTour.getAttribute("data-tourid");
            cancelTourIdField.value = tourId; 

            Swal.fire({
                iconHtml: '<i class="fas fa-thumbs-up"></i>',
                title: "Confirm Cancel?",
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
                    var cancelModal = new bootstrap.Modal(document.getElementById("cancelReasonModal"));
                    cancelModal.show();
                }
            });
        }
    });

    submitCancelReason.addEventListener("click", function () {
        let reason = cancelReasonInput.value.trim();
        let tourId = cancelTourIdField.value;

        if (reason === "") {
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

        fetch("../../controllers/upcomingtourscontroller.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `cancelTour=true&tourId=${encodeURIComponent(tourId)}&cancelReason=${encodeURIComponent(reason)}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === "success") {
                Swal.fire({
                    iconHtml: '<i class="fas fa-circle-check"></i>',
                    title: "Tour Successfully Cancelled!",
                    timer: 3000,
                    showConfirmButton: false,
                    customClass: {
                        title: "swal2-title-custom",
                        icon: "swal2-icon-custom",
                        popup: "swal-custom-popup"
                    }
                }).then(() => location.reload());
            } else {
                Swal.fire({
                    iconHtml: '<i class="fas fa-exclamation-circle"></i>',
                    title: "Failed to cancel tour.",
                    timer: 3000,
                    showConfirmButton: false,
                    customClass: {
                        title: "swal2-title-custom",
                        icon: "swal2-icon-custom",
                        popup: "swal-custom-popup"
                    }
                });
            }
        });

        var cancelModal = bootstrap.Modal.getInstance(document.getElementById("cancelReasonModal"));
        cancelModal.hide();
    });
});