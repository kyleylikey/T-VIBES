function filterReviews(status) {
    window.location.href = '?status=' + status;
}

let currentReviewId;

function showModal(review) {
    currentReviewId = review.revid;
    document.querySelector(".modal-title").innerText = review.sitename;
    document.querySelector(".modal-body p").innerText = review.review;
    document.querySelector(".user-text h5").innerText = review.username;
    document.querySelector(".user-text p").innerText = review.date;

    const displayBtn = document.getElementById("displayBtn");
    const archiveBtn = document.getElementById("archiveBtn");

    if (review.status === "submitted") {
        displayBtn.style.display = "inline-block";
        archiveBtn.style.display = "inline-block";
    } else {
        displayBtn.style.display = "none";
        archiveBtn.style.display = "none";
    }

    var modal = new bootstrap.Modal(document.getElementById('reviewsModal'));
    modal.show();
}

function updateReviewStatus(status) {
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
                body: "review_id=" + currentReviewId + "&status=" + status
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