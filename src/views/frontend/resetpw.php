<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet"> <!-- Add this link for Bootstrap Icons -->
    <link rel="stylesheet" href="../../../public/assets/styles/resetpw.css">

</head>
<body class="d-flex align-items-center justify-content-center vh-100">
    <div class="position-absolute top-0 start-0 m-3">
        <a href="login.php" class="btn btn-secondary d-flex align-items-center rounded-circle p-2" style="width: 40px; height: 40px; justify-content: center;">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left" viewBox="0 0 16 16">
            <path fill-rule="evenodd" d="M15 8a.5.5 0 0 1-.5.5H3.707l3.147 3.146a.5.5 0 0 1-.708.708l-4-4a.5.5 0 0 1 0-.708l4-4a.5.5 0 0 1 .708.708L3.707 7.5H14.5A.5.5 0 0 1 15 8z"/>
        </svg>
         </a>

    </div>

    <div class="card shadow-lg" style="width: 30rem; height: 30rem; padding: 2rem;">
        <div class="card-body">
            <h1 class="text-center mb-2">Reset Password</h1>
            <p class="text-center text-muted mb-4">Please enter and re-enter your new password</p><br>
            
            <form onsubmit="handleSubmit(event)">
                <div class="mb-3">
                    <input 
                        type="password" 
                        id="newPassword" 
                        name="newPassword" 
                        class="form-control rounded-pill"
                        placeholder="New Password"
                        required
                    >
                </div>

                <div class="mb-3">
                    <input 
                        type="password" 
                        id="retypeNewPassword" 
                        name="retypeNewPassword" 
                        class="form-control rounded-pill"
                        placeholder="Re-type New Password"
                        required
                    >
                </div>

                <button 
                    type="submit" 
                    class="btn btn-submit w-auto px-4 py-2 rounded-pill d-block mx-auto"
                >
                    Submit
                </button>
            </form>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Password Reset Successful</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <i class="bi bi-check2-circle" style="font-size: 48px; color: green;"></i> <!-- Check icon -->
                    <p>Password Sucessfully Changed</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function handleSubmit(event) {
            event.preventDefault();
            const newPassword = document.getElementById('newPassword').value;
            const retypeNewPassword = document.getElementById('retypeNewPassword').value;

            if (newPassword === retypeNewPassword) {
                const modal = new bootstrap.Modal(document.getElementById('staticBackdrop'));
                modal.show();
            } else {
                alert("Passwords do not match. Please try again.");
            }
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
