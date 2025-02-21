function logoutConfirm() {
    Swal.fire({
        title: 'Are you sure you want to sign out?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, sign out'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "../../controllers/logout.php";
        }
    });
}
            