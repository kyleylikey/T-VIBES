<?php
include '../../../../includes/auth.php';
require_once '../../../config/dbconnect.php';

$database = new Database();
$db = $database->getConnection();

$userid = $_SESSION['userid'];
$query = "SELECT name, username, contactnum, email FROM users WHERE userid = :userid LIMIT 1";
$stmt = $db->prepare($query);
$stmt->bindParam(':userid', $userid, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $contactnum = trim($_POST['contactnum']);

    if (empty($name) || empty($username) || empty($email) || empty($contactnum)) {
        echo json_encode(["status" => "error", "message" => "All fields are required."]);
        exit;
    }

    if ($name === $user['name'] && $username === $user['username'] && $email === $user['email'] && $contactnum === $user['contactnum']) {
        echo json_encode(["status" => "nochange"]);
        exit;
    }

    $updateQuery = "UPDATE users SET name = :name, username = :username, email = :email, contactnum = :contactnum WHERE userid = :userid";
    $stmt = $db->prepare($updateQuery);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':contactnum', $contactnum);
    $stmt->bindParam(':userid', $userid, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Update failed."]);
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Account Details - Taal Heritage Town</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;700&family=Raleway:wght@300;400;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .account-container {
            max-width: 800px;
            margin: 50px auto;
        }

        h2 {
            font-weight: bold !important;
            color: #102E47;
            font-family: 'Raleway', sans-serif !important;
            margin-bottom: 20px !important; 
        }

        .nav-link {
            font-size: 20px; 
            font-family: 'Raleway', sans-serif !important;
        }

        header a.nav-link {
            color: #434343 !important;
            font-weight: normal !important;
            transition: color 0.3s ease, font-weight 0.3s ease;
        }

        header a.nav-link:hover {
            color: #729AB8 !important;
        }

        header a.nav-link.active {
            color: #729AB8 !important;
            font-weight: bold !important;
        }

        .navbar-nav .btn-danger {
            background-color: transparent !important;
            border: 2px solid #EC6350 !important;
            color: #EC6350 !important;
            transition: all 0.3s ease;
            font-weight: bold !important;
        }

        .navbar-nav .btn-danger:hover {
            background-color: #EC6350 !important;
            color: #FFFFFF !important;
            font-weight: bold !important;
        }

        .card {
            border-radius: 25px !important;
            background-color: #E7EBEE !important;
            padding: 20px;
        }

        .edit-btn {
            background-color: #E7EBEE; 
            color: #EC6350; 
            border: 2px solid #EC6350; 
            padding: 10px 20px;
            border-radius: 25px;
            display: flex;
            align-items: center;
            gap: 5px;
            width: auto;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease-in-out;
            font-family: 'Nunito', sans-serif !important;
        }

        .edit-btn:hover {
            background-color: #EC6350; 
            color: #FFFFFF;
        }

        h5 {
            color: #102E47;
            font-weight: bold !important;
            font-family: 'Raleway', sans-serif !important;
        }

        .label {
            color: #729AB8;
            font-size: 16px;
            margin-bottom: 2px;
            font-family: 'Nunito', sans-serif !important;
            font-weight: bold;
        }

        .value {
            color: #102E47;
            font-weight: bold;
            font-size: 16px;
            font-family: 'Nunito', sans-serif !important;
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
            font-family: 'Raleway', sans-serif !important;
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

        .modal-dialog {
            max-width: 800px !important; 
            width: 90% !important;
        }

        .modal-content {
            border-radius: 25px !important; 
            padding: 30px;
            font-family: 'Nunito', sans-serif !important;
        }

        .modal-header {
            text-align: center;
            border-bottom: none !important; 
            padding-bottom: 20px;
        }

        .modal-title {
            font-family: 'Raleway', sans-serif; 
            font-weight: bold; 
            color: #434343; 
            width: 100%; 
            text-align: center; 
            font-size: 24px;
            margin-bottom: 10px !important;
        }

        .form-control {
            width: 50% !important; 
            margin: 0 auto; 
            display: block; 
            border: 2px solid #102E47;
            border-radius: 8px;
            margin-top: 10px !important;
        }

        .modal-footer {
            justify-content: center !important;
            gap: 15px;
            padding: 20px;
            border-top: none !important;
        }

        .save-btn {
            background-color: #FFFFFF; 
            color: #EC6350; 
            border: 2px solid #EC6350; 
            border-radius: 25px;
            padding: 10px 20px;
            font-weight: bold;
            cursor: pointer;
            width: auto;
            display: block;
            margin: 0 auto;
            transition: all 0.3s ease-in-out;
            margin-top: 10px !important;
        }
        
        .save-btn:hover {
            background-color: #EC6350; 
            color: #FFFFFF;
        }

        .logout-btn {
            background-color: #FFFFFF; 
            color: #EC6350; 
            border: 2px solid #EC6350; 
            padding: 10px 20px;
            border-radius: 25px;
            display: flex;
            align-items: center;
            width: auto;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease-in-out;
            font-family: 'Nunito', sans-serif !important;
            margin-top: 20px;
        }

        .logout-btn:hover {
            background-color: #EC6350; 
            color: #FFFFFF;
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
                <i class="bi bi-pencil-fill"></i> Edit
            </button>
        </div>
        <div class="row mt-3">
            <div class="col-md-6">
                <p class="label">Full Name</p>
                <p class="value"><?php echo htmlspecialchars($user['name']); ?></p>
            </div>
            <div class="col-md-6">
                <p class="label">Username</p>
                <p class="value"><?php echo htmlspecialchars($user['username']); ?></p>
            </div>
            <div class="col-md-6">
                <p class="label">Contact Number</p>
                <p class="value"><?php echo htmlspecialchars($user['contactnum']); ?></p>
            </div>
            <div class="col-md-6">
                <p class="label">Email Address</p>
                <p class="value"><?php echo htmlspecialchars($user['email']); ?></p>
            </div>
        </div>
    </div>
    <button type="button" class="logout-btn ms-auto" onclick="logoutConfirm()">Log Out</button>
</div>

<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Personal Information</h5>
            </div>
            <form id="editForm">
                <div class="mb-3">
                    <input type="text" class="form-control" id="edit-name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required pattern="^[A-Za-z]+(?:\s+[A-Za-z]+)+$" title="Full name must contain at least two words with only alphabets and spaces.">
                </div>
                <div class="mb-3">
                    <input type="text" class="form-control" id="edit-username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required pattern="^(?!\d+$)[A-Za-z0-9_]{3,20}$" title="Username must be 3-20 characters long, can include letters, numbers, and underscores, but cannot be only numbers.">
                </div>
                <div class="mb-3">
                    <input type="email" class="form-control" id="edit-email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>">
                </div>
                <div class="mb-3">
                    <input type="text" class="form-control" id="edit-contactnum" name="contactnum" value="<?php echo htmlspecialchars($user['contactnum']); ?>" required pattern="^\d{11}$" title="Contact number must be exactly 11 digits." inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                </div>
                <div class="modal-footer">
                    <button type="submit" class="save-btn">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/js/all.min.js"></script>
<script src="../../../../public/assets/scripts/main.js"></script>
<script>
document.getElementById('editForm').addEventListener('submit', function(event) {
    event.preventDefault();

    let name = document.getElementById('edit-name').value.trim();
    let username = document.getElementById('edit-username').value.trim();
    let email = document.getElementById('edit-email').value.trim();
    let contactnum = document.getElementById('edit-contactnum').value.trim();

    let originalName = "<?php echo htmlspecialchars($user['name']); ?>";
    let originalUsername = "<?php echo htmlspecialchars($user['username']); ?>";
    let originalEmail = "<?php echo htmlspecialchars($user['email']); ?>";
    let originalContactnum = "<?php echo htmlspecialchars($user['contactnum']); ?>";

    if (!name || !username || !email || !contactnum) {
        Swal.fire({
            iconHtml: '<i class="fas fa-exclamation-circle"></i>',
            title: 'All fields are required!',
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

    if (name === originalName && username === originalUsername && email === originalEmail && contactnum === originalContactnum) {
        Swal.fire({
            iconHtml: '<i class="fas fa-exclamation-circle"></i>',
            title: 'No changes made!',
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

    Swal.fire({
        title: 'Confirm Changes?',
        iconHtml: '<i class="fas fa-thumbs-up"></i>',
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
            fetch(window.location.href, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({ name, username, email, contactnum })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === "success") {
                    Swal.fire({
                        iconHtml: '<i class="fas fa-circle-check"></i>',
                        title: 'Personal Information Successfully Updated!',
                        showConfirmButton: false,
                        timer: 3000,
                        customClass: {
                            title: "swal2-title-custom",
                            icon: "swal2-icon-custom",
                            popup: "swal-custom-popup"
                        }
                    }).then(() => {
                        location.reload();
                    });
                } else if (data.status === "nochange") {
                    Swal.fire({
                        iconHtml: '<i class="fas fa-exclamation-circle"></i>',
                        title: 'No changes made!',
                        timer: 3000,
                        showConfirmButton: false,
                        customClass: {
                            title: "swal2-title-custom",
                            icon: "swal2-icon-custom",
                            popup: "swal-custom-popup"
                        }
                    });
                } else {
                    Swal.fire({
                        iconHtml: '<i class="fas fa-exclamation-circle"></i>',
                        title: 'Error updating!',
                        text: data.message,
                        timer: 3000,
                        showConfirmButton: false,
                        customClass: {
                            title: "swal2-title-custom",
                            icon: "swal2-icon-custom",
                            popup: "swal-custom-popup"
                        }
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    iconHtml: '<i class="fas fa-exclamation-circle"></i>',
                    title: 'Update failed!',
                    text: 'Please try again later.',
                    timer: 3000,
                    showConfirmButton: false,
                    customClass: {
                        title: "swal2-title-custom",
                        icon: "swal2-icon-custom",
                        popup: "swal-custom-popup"
                    }
                });
            });
        }
    });
});
</script>
</body>
</html>
