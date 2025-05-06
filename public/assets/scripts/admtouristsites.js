function showModal(siteName, image, price, schedule, description, siteId) {
    document.getElementById('touristSitesModalLabel').innerText = siteName;
    document.getElementById('modalImage').src = 'https://tourtaal.azurewebsites.net/public/uploads/' + image;
    document.getElementById('modalPrice').innerText = '₱' + price;
    document.getElementById('modalSchedule').innerText = schedule;
    document.getElementById('modalDescription').innerText = description;
    document.getElementById('modalDeleteBtn').setAttribute('data-siteid', siteId); 

    var modal = new bootstrap.Modal(document.getElementById('touristSitesModal'));
    modal.show();
}

document.addEventListener("DOMContentLoaded", function () {
    document.querySelector("#modalDeleteBtn").addEventListener("click", function () {
        let siteId = this.getAttribute('data-siteid');

        Swal.fire({
            iconHtml: '<i class="fas fa-trash-alt"></i>',
            title: "Delete This Destination?",
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
                fetch('../../controllers/sitecontroller.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `delete_site=${siteId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            iconHtml: '<i class="fas fa-check-circle"></i>',
                            title: "Tourist Site Deleted Successfully!",
                            timer: 3000,
                            showConfirmButton: false,
                            customClass: {
                                title: "swal2-title-custom",
                                icon: "swal2-icon-custom",
                                popup: "swal-custom-popup"
                            }
                        }).then(() => {
                            location.reload();
                            document.querySelector(`.siteitem[data-siteid="${siteId}"]`).remove();
                        });
                    } else {
                        Swal.fire("Error!", "Failed to delete the tourist site.", "error");
                    }
                });
            }
        });
    });
});