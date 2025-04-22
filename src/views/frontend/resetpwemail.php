<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Taal Heritage Town</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../../public/assets/styles/main.css">
    <link rel="stylesheet" href="../../../public/assets/styles/resetpw.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;700&family=Raleway:wght@300;400;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        * {
            font-family: 'Nunito', sans-serif;
        }

        .title {
            font-size: 35px !important;
            font-family: 'Raleway', sans-serif !important;
            font-weight: bold !important;
            color: #102E47 !important;
        }

        .text-muted {
            color: #434343;
            font-family: 'Nunito', sans-serif !important;
        }

        .card {
            border-radius: 25px;
            transition: opacity 1s ease-in-out !important;
        }

        .btn-custom {
            font-weight: bold !important;
            border: 2px solid #102E47 !important;
            background-color: #FFFFFF !important;
            color: #434343 !important;
            cursor: pointer !important;
            transition: all 0.3s ease !important;
            font-family: 'Nunito', sans-serif !important;
        }

        .btn-custom:hover {
            background-color: #102E47 !important;
            color: #FFFFFF !important;
            font-weight: bold !important;
        }

        .taalbgpic {
            background-image: url('../../../public/assets/images/taal_welcome.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        .taalbgpic::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to bottom, rgba(0, 0, 0, 0.8), rgba(0, 0, 0, 0));
            z-index: -1;
        }

        .swal2-icon {
            background: none !important;
            border: none !important;
            box-shadow: none !important;
        }

        .swal2-icon-custom {
            font-size: 10px; 
            color: #EC6350; 
        }

        .swal2-title-custom {
            font-size: 24px !important;
            font-weight: bold;
            color: #434343 !important;
        }

        .swal-custom-popup {
            padding: 20px;
            border-radius: 25px;
            font-family: 'Nunito', sans-serif !important;
        }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center vh-100 taalbgpic">
    <div class="position-absolute top-0 start-0 m-3">
        <a href="login.php" class="btn btn-secondary d-flex align-items-center rounded-circle p-2" style="width: 40px; height: 40px; justify-content: center;">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M15 8a.5.5 0 0 1-.5.5H3.707l3.147 3.146a.5.5 0 0 1-.708.708l-4-4a.5.5 0 0 1 0-.708l4-4a.5.5 0 0 1 .708.708L3.707 7.5H14.5A.5.5 0 0 1 15 8z"/>
            </svg>
        </a>
    </div>

    <div class="card shadow-lg" style="width: 30rem; height: 25rem; padding: 2rem;">
        <div class="card-body text-center">
            <h1 class="title text-center mb-2">Reset Password</h1>
            <p class="text-muted mb-4">Please enter your registered email</p>
            
            <form id="resetForm">
                <div class="mb-3">
                    <input type="email" id="email" name="email" class="form-control rounded-pill" placeholder=" Email" required>
                </div>
                <button type="submit" class="btn btn-custom w-auto px-4 py-2 rounded-pill d-block mx-auto">Submit</button>
            </form>
        </div>
    </div>

    <script>
    document.getElementById('resetForm').addEventListener('submit', function(event) {
        event.preventDefault();

        const formData = new FormData(this);
        const submitButton = document.querySelector('button[type="submit"]');
        submitButton.disabled = true;
        submitButton.textContent = "Loading...";

        fetch('../../controllers/forgetpassword.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(text => {
            if (!text) throw new Error("Empty response from server");
            
            let data;
            try {
                data = JSON.parse(text);
            } catch (error) {
                console.error("Invalid JSON response:", text);
                throw new Error("Server returned invalid JSON");
            }

            Swal.fire({
                iconHtml: data.status === 'success' ? '<i class="fas fa-envelope" style="color: #EC6350 !important;"></i>' : '<i class="fas fa-exclamation-circle" style="color: #EC6350 !important;"></i>',
                customClass: { icon: 'swal2-icon' },
                title: data.message,
                showConfirmButton: false,
                timer: 3000,
                customClass: {
                    title: "swal2-title-custom",
                    icon: "swal2-icon-custom",
                    popup: "swal-custom-popup"
                },
            }).then(() => {
                if (data.status === 'success') {
                    let timeLeft = 60; 
                    const interval = setInterval(() => {
                        timeLeft--;
                        submitButton.textContent = `Wait ${timeLeft}s`;
                        if (timeLeft <= 0) {
                            clearInterval(interval);
                            submitButton.disabled = false;
                            submitButton.textContent = "Submit";
                        }
                    }, 1000);
                } else {
                    submitButton.disabled = false;
                    submitButton.textContent = "Submit";
                }
            });
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                iconHtml: '<i class="fas fa-exclamation-circle"></i>',
                customClass: { icon: 'swal2-icon' },
                title: 'Something went wrong. Please try again later.',
                showConfirmButton: false,
                timer: 3000,
                customClass: {
                    title: "swal2-title-custom",
                    icon: "swal2-icon-custom",
                    popup: "swal-custom-popup"
                },
            }).then(() => {
                submitButton.disabled = false;
                submitButton.textContent = "Submit";
            });
        });
    });
</script>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>