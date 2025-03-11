function logoutConfirm() {
    Swal.fire({
        iconHtml: '<i class="bi bi-exclamation-circle-fill"></i>',
        title: 'Are you sure you want to sign out?',
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
            window.location.href = "/T-VIBES/src/controllers/logout.php";
        }
    });
}