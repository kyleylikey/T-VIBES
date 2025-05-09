<?php
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Taal Heritage Town</title>
    <link rel="stylesheet" href="../../../public/assets/styles/main.css">
    <link rel="stylesheet" href="../../../public/assets/styles/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;700&family=Raleway:wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <style>
        * {
            font-family: 'Nunito', sans-serif;
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

        .large-text-overlay-signup {
            position: absolute;
            bottom: 20%;
            left: 5%;
            color: #FFFFFF !important;
            font-family: 'Raleway', sans-serif !important;
            font-size: 30px;
            font-weight: 700 !important;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
            z-index: 2;
            text-align: left;
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

        .font-link {
            color: #102E47 !important;
        }

        .signup-container h1 {
            font-family: 'Raleway', sans-serif !important;
            color: #102E47 !important;
            font-weight: bold !important;
        }

        .signup-container p {
            color: #434343 !important;
        }

        .checkbox-container {
            color: #434343 !important;
        }
    </style>
</head>
<body class="taalbgpic">
    <div class="container">
        <div class="row justify-content-between">
            <div class="col-0 col-md-3"></div>
            <div class="col-12 col-md-5 p-md-4 d-none d-md-block large-text-overlay-signup">
                <h1>Your Lively</h1>
                <h1>Getaway Awaits!</h1>
            </div>
            <div class="col-12 col-md-4 p-md-4 signup-container">
                <h1>Sign Up</h1>
                <p>Please register to log in.</p>
                <form id="signupForm">
                    <div class="form-group">
                        <input type="text" id="fullname" name="fullname" placeholder="Full Name" required class="half-width-input" pattern="^[A-Za-z\s]+$" title="Full name must contain only alphabets and spaces.">
                        <input type="text" id="username" name="username" placeholder="Username" required class="half-width-input" pattern="^\w{3,20}$" title="Username must be 3-20 characters long and can only include letters, numbers, and underscores.">
                    </div>
                    <input type="tel" id="contact" name="contact" placeholder="Contact Number" pattern="^\d{11}$" required class="full-width-input" title="Contact number must be exactly 11 digits." inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                    <input type="email" id="email" name="email" placeholder="Email" required class="full-width-input">
                    <input type="password" id="password" name="password" placeholder="Password" required class="full-width-input" pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$" title="Password must be at least 8 characters long, include at least one uppercase letter, one lowercase letter, one number, and one special character.">

                    <div class="checkbox-container form-check">
                    </div>

                    <button type="submit" id="submitBtn" class="btn-custom">Create Account</button>
                    <p class="login-redirect">Already have an account? <a href="login.php" class="font-link">Login</a></p>
                </form>
            </div>

        </div>

    </div>
    <div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-lg">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="termsModalLabel">Terms and Conditions for Account Creation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><strong>Effective Date: May 10, 2025</strong></p>
                
                <p>Welcome to T-VIBES (Taal Visitor Information and Booking System), operated by the LGU of Taal, Tourism Department. These Terms and Conditions ("Terms") govern your use of our website and services. By creating an account or using the Website, you agree to be bound by these Terms.</p>
                
                <h5>1. Eligibility</h5>
                <ul>
                <li><strong>Age Requirement</strong>: To create an account, you must be at least 18 years old. Minors under 18 are not permitted to use our services.</li>
                <li><strong>Visitor Requirement</strong>: Only individuals visiting Taal, whether from the Philippines or abroad, are eligible to create an account on the Website.</li>
                </ul>
                
                <h5>2. Account Creation</h5>
                <ul>
                <li>You will be required to provide the following personal information to create an account:
                    <ul>
                    <li>Full Name</li>
                    <li>Email Address</li>
                    <li>Contact Number</li>
                    <li>Username and Password (created by you)</li>
                    </ul>
                </li>
                <li>You are responsible for ensuring the accuracy of the information provided during account creation.</li>
                </ul>
                
                <h5>3. Use of Information</h5>
                <ul>
                <li><strong>Analytics:</strong> Information collected may be used by the Taal LGU for analytical purposes such as monitoring visitor trends and improving tourism services.</li>
                <li><strong>Data Privacy:</strong> Your data will not be shared with third parties outside the LGU. All data is securely stored and used solely for operational and analytical purposes in accordance with the <strong>Data Privacy Act of 2012 (RA 10173)</strong>.</li>
                </ul>
                
                <h5>4. Account Deletion and Data Retention</h5>
                <ul>
                <li>If you wish to delete your account and all associated data, you may request deletion by contacting the Taal Tourism Office. Upon verification, your account will be removed from our system.</li>
                <li>Data gathered during your account's activity may still be used for analytics purposes by the Taal LGU.</li>
                </ul>
                
                <h5>5. Changes to Terms</h5>
                <ul>
                <li>We may update or modify these Terms from time to time. Continued use of the Website after such changes indicates your acceptance of the updated Terms.</li>
                </ul>
                
                <h5>6. Limitation of Liability</h5>
                <ul>
                <li>The website and its services are provided "as is" without warranties or representations of any kind. We are not liable for any indirect, incidental, or consequential damages arising from the use of the website.</li>
                </ul>
                
                <h5>7. Governing Law</h5>
                <ul>
                <li>These Terms shall be governed by and construed in accordance with the laws of the Philippines.</li>
                </ul>
                
                <h5>8. Contact Us</h5>
                <ul>
                <li>For inquiries or concerns, you may contact the Taal Tourism Office at: <strong>Email:</strong> tourismtaal1572@gmail.com <strong>Phone:</strong> 09088645669</li>
                </ul>
                
                <h5>Acceptance of Terms</h5>
                <ul>
                <li>By creating an account on the website, you acknowledge that you have read, understood, and agree to be bound by these Terms and Conditions.</li>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-custom" data-bs-dismiss="modal">Close</button>
            </div>
            </div>
        </div>
        </div>

    <script>

    document.getElementById('signupForm').addEventListener('submit', function(event) {
    event.preventDefault();
    
    const formData = new FormData(this);
    const submitButton = document.getElementById('submitBtn');
    submitButton.disabled = true;
    submitButton.textContent = "Loading...";

    fetch('https://tourtaal.azurewebsites.net/src/controllers/signupcontroller.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        // Clone the response so we can read it twice
        const responseClone = response.clone();
        
        // Read the raw response as text and log it
        responseClone.text().then(rawData => {
            console.log('Raw API Response:', rawData);
            try {
                // Try to parse it as JSON to verify format
                const jsonData = JSON.parse(rawData);
                console.log('Parsed JSON Response:', jsonData);
            } catch (e) {
                console.error('Error parsing JSON response:', e);
            }
        });
        
        // Continue with normal response handling
        return response.json();
    })
    .then(data => {
        console.log('Processed API Response:', data);
        Swal.fire({
            iconHtml: data.status === 'success' ? '<i class="fas fa-envelope" style="color: #EC6350 !important;"></i>' : '<i class="fas fa-exclamation-circle" style="color: #EC6350 !important;"></i>',
            title: data.message,
            showConfirmButton: false,
            timer: 3000,
            customClass: {
                title: "swal2-title-custom",
                icon: "swal2-icon-custom",
                popup: "swal-custom-popup"
            }
        });

        if (data.status === 'success') {
            submitButton.textContent = "Email Sent!";
        } else {
            submitButton.disabled = false;
            submitButton.textContent = "Create Account";
        }
    })
    .catch(error => {
        console.error('Fetch Error:', error);
        Swal.fire({
            iconHtml: '<i class="fas fa-exclamation-circle" style="color: #EC6350 !important;"></i>',
            title: 'Something went wrong. Please try again later.',
            showConfirmButton: false,
            timer: 3000,
            customClass: {
                title: "swal2-title-custom",
                icon: "swal2-icon-custom",
                popup: "swal-custom-popup"
            }
        }).then(() => {
            submitButton.disabled = false;
            submitButton.textContent = "Create Account";
        });
    });
});
    document.addEventListener('DOMContentLoaded', function() {
        const checkboxContainer = document.querySelector('.checkbox-container');
        checkboxContainer.innerHTML = `
            <input class="form-check-input float-none" type="checkbox" id="privacyPolicy" name="privacyPolicy" required>
            <label class="form-check-label" for="privacyPolicy">I agree to the <a href="#" id="openTerms" class="font-link">Privacy Policy & Terms of Service</a></label>
        `;
        
        document.getElementById('openTerms').addEventListener('click', function(e) {
            e.preventDefault();
            const termsModal = new bootstrap.Modal(document.getElementById('termsModal'));
            termsModal.show();
        });
    });
</script>

</body>
</html>