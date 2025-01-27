<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Reset Password</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
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

        <div class="card shadow-lg" style="width: 30rem; height: 25rem; padding: 2rem;">
            <div class="card-body">
                <h1 class="text-center mb-2">Reset Password</h1>
                <p class="text-center text-muted mb-4">Please enter your registered email</p><br>
                
                <form onsubmit="handleSubmit(event)">
                    <div class="mb-3">
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            class="form-control rounded-pill"
                            placeholder=" Email"
                            required
                            >
                    </div>
                    <br>

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
                        <h1 class="modal-title fs-5" id="staticBackdropLabel">Email Sent</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" class="bi bi-envelope-check mb-3" viewBox="0 0 16 16">
                            <path d="M2 2a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H2zm0 1h12a1 1 0 0 1 1 1v.217l-7 4.2-7-4.2V4a1 1 0 0 1 1-1zm13 2.383V10a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V5.383l6.646 3.984a.5.5 0 0 0 .708 0L15 5.383z"/>
                            <path d="M16 12.5a.5.5 0 0 0-.5-.5h-5a.5.5 0 0 0 0 1h5a.5.5 0 0 0 .5-.5zm-2.354-2.146a.5.5 0 1 0-.707-.708l-2 2a.5.5 0 0 0 .708.708l1.146-1.147 1.146 1.147a.5.5 0 0 0 .708-.708l-1.147-1.146z"/>
                        </svg>
                        <p>Please check your email for a verification link.</p>
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
                const modal = new bootstrap.Modal(document.getElementById('staticBackdrop'));
                modal.show();
            }
        </script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    </body>
    </html>