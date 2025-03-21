<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .account-container {
            max-width: 800px;
            margin: 50px auto;
        }
        h2 {
            font-weight: bold;
            color: #102E47;
        }
        .card {
            border-radius: 10px;
            background-color: #f1f5f9;
            padding: 20px;
        }
        .edit-btn {
            background-color: #e35d5b;
            border: none;
            color: white;
            padding: 10px 20px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            gap: 5px;
            font-weight: bold;
            cursor: pointer;
        }
        .edit-btn:hover {
            background-color: #d9534f;
        }
        h5 {
            color: #102E47;
            font-weight: bold;
        }
        .label {
            color: #729AB8;
            font-size: 14px;
            margin-bottom: 2px;
        }
        .value {
            color: #102E47;
            font-weight: bold;
            font-size: 16px;
        }
        /* Modal Styling */
        .modal-content {
            border-radius: 10px;
            padding: 20px;
        }
        .form-control {
            border: 2px solid #102E47;
            border-radius: 8px;
        }
        .save-btn {
            background-color: #e35d5b;
            color: white;
            border: none;
            border-radius: 20px;
            padding: 10px 20px;
            font-weight: bold;
            cursor: pointer;
            width: 100%;
            transition: background 0.3s;
        }
        .save-btn:hover {
            background-color: #d9534f;
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

        .swal-custom-btn {
            padding: 10px 20px !important;
            font-size: 16px !important;
            font-weight: bold !important;
            border: 2px solid #102E47 !important;
            border-radius: 25px !important;
            background-color: #FFFFFF !important;
            color: #434343 !important;
            cursor: pointer !important;
            transition: all 0.3s ease !important;
        }

        .swal-custom-btn:hover {
            background-color: #102E47 !important;
            color: #FFFFFF !important;
        }

        .info-box span,
        .info-box h1,
        .info-box i,
        .table,
        .btn-custom,
        .swal2-title-custom,
        .swal-custom-btn {
            font-family: 'Nunito', sans-serif !important;
        }
    </style>
</head>
<body>
<div class="header-container">
<?php include '../../templates/headertours.php'; ?>
</div>

<div class="container account-container">
    <h2>My Account</h2>
    <div class="card shadow-sm">
        <div class="d-flex justify-content-between align-items-start">
            <h5>Personal Information</h5>
            <button class="edit-btn" data-bs-toggle="modal" data-bs-target="#editModal">
                <i class="bi bi-pencil-fill"></i>
                Edit
            </button>
        </div>
        <div class="row mt-3">
            <div class="col-md-6">
                <p class="label">Full Name</p>
                <p class="value">Juan Dela Cruz</p>
            </div>
            <div class="col-md-6">
                <p class="label">Username</p>
                <p class="value">jdc_030993</p>
            </div>
            <div class="col-md-6">
                <p class="label">Contact Number</p>
                <p class="value">+63 123 456 7890</p>
            </div>
            <div class="col-md-6">
                <p class="label">Email Address</p>
                <p class="value">sample@email.com</p>
            </div>
        </div>
    </div>
    <button type="button" class="d-flex align-items-end my-4 btn btn-danger btn-lg rounded-pill ms-auto" onclick="logoutConfirm()">Log Out</button>
</div>


<!-- Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <h5 class="fw-bold mb-3" id="editModalLabel">Edit Personal Information</h5>
            <form id="editForm">
                <div class="mb-3">
                    <input type="text" class="form-control" placeholder="Full Name" value="Juan Dela Cruz">
                </div>
                <div class="mb-3">
                    <input type="text" class="form-control" placeholder="Username" value="jdc_030993">
                </div>
                <div class="mb-3">
                    <input type="email" class="form-control" placeholder="Email Address" value="sample@email.com">
                </div>
                <div class="mb-3">
                    <input type="text" class="form-control" placeholder="Contact Number" value="+63 123 456 7890">
                </div>
                <button type="submit" class="save-btn">Save Changes</button>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../../../../public/assets/scripts/main.js"></script>
<script>
    document.getElementById('editForm').addEventListener('submit', function(event) {
        event.preventDefault(); // Prevent actual form submission
        
        // Close modal
        var modal = bootstrap.Modal.getInstance(document.getElementById('editModal'));
        modal.hide();

        // Show success alert using SweetAlert
        Swal.fire({
            icon: 'success',
            title: 'Personal Information Successfully Updated',
            showConfirmButton: false,
            timer: 2000,
            customClass: {
                popup: 'swal2-popup-custom'
            }
        });
    });
</script>
</body>
</html>
