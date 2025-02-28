
function showModal() {
    var modal = new bootstrap.Modal(document.getElementById('tourRequestModal'));
    modal.show();
}

document.addEventListener("DOMContentLoaded", function () {
    // Accept Button
    document.querySelector(".btn-custom:nth-child(1)").addEventListener("click", function () {
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
                });
            }
        });
    });

    // Decline Button with Reason for Cancellation
    document.querySelector(".btn-custom:nth-child(2)").addEventListener("click", function () {
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
                // Show Reason for Cancellation modal
                var cancelModal = new bootstrap.Modal(document.getElementById("cancelReasonModal"));
                cancelModal.show();
            }
        });
    });

    // Submit Reason for Cancellation
    document.getElementById("submitCancelReason").addEventListener("click", function () {
        let reason = document.getElementById("cancelReasonInput").value.trim();

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

        // Close the modal
        var cancelModal = bootstrap.Modal.getInstance(document.getElementById("cancelReasonModal"));
        cancelModal.hide();

        // Show success message
        Swal.fire({
            iconHtml: '<i class="fas fa-circle-check"></i>',
            title: "Successfully Declined Tour Request!",
            timer: 3000,
            showConfirmButton: false,
            customClass: {
                title: "swal2-title-custom",
                icon: "swal2-icon-custom",
                popup: "swal-custom-popup"
            }
        });
    });
});
