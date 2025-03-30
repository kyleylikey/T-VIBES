<?php
include '../../../includes/auth.php';
require_once '../../config/dbconnect.php';

$database = new Database();
$conn = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'addAccount') {
    $name = htmlspecialchars($_POST['name']);
    $username = htmlspecialchars($_POST['username']);
    $email = htmlspecialchars($_POST['email']);
    $contactnum = htmlspecialchars($_POST['contactnum']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); 
    $usertype = 'emp'; 
    $status = 'active'; 

    try {
        $query = "INSERT INTO users (name, username, email, contactnum, hashedpassword, usertype, status) 
                  VALUES (:name, :username, :email, :contactnum, :hashedpassword, :usertype, :status)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':contactnum', $contactnum);
        $stmt->bindParam(':hashedpassword', $password);
        $stmt->bindParam(':usertype', $usertype);
        $stmt->bindParam(':status', $status);

        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to add account.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'editAccount') {
    $userid = $_POST['userid'];
    $name = htmlspecialchars($_POST['name']);
    $username = htmlspecialchars($_POST['username']);
    $email = htmlspecialchars($_POST['email']);
    $contactnum = htmlspecialchars($_POST['contactnum']);
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_BCRYPT) : null;

    if (empty($name) || empty($username) || empty($email) || empty($contactnum)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required.']);
        exit;
    }

    try {
        $query = "UPDATE users SET name = :name, username = :username, email = :email, contactnum = :contactnum";
        if ($password) {
            $query .= ", hashedpassword = :password";
        }
        $query .= " WHERE userid = :userid";

        $stmt = $conn->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':contactnum', $contactnum);
        if ($password) {
            $stmt->bindParam(':password', $password);
        }
        $stmt->bindParam(':userid', $userid);

        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update account.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'disableAccount' || $_POST['action'] === 'enableAccount') {
        $userid = $_POST['userid'];
        $newStatus = $_POST['action'] === 'disableAccount' ? 'inactive' : 'active';
        
        try {
            $query = "UPDATE users SET status = :status WHERE userid = :userid";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':status', $newStatus);
            $stmt->bindParam(':userid', $userid);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update account status.']);
            }
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_userid'])) {
    $deleteUserId = $_POST['delete_userid'];
    
    $deleteQuery = "DELETE FROM users WHERE userid = :userid AND usertype = 'trst'";
    $deleteStmt = $conn->prepare($deleteQuery);
    $deleteStmt->bindParam(':userid', $deleteUserId);
    
    if ($deleteStmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
    exit;
}

$defaultType = 'mngr';
$userType = isset($_GET['usertype']) ? $_GET['usertype'] : $defaultType;
$query = "SELECT userid, name, username, contactnum, email, usertype, status FROM users WHERE usertype = :usertype";
$stmt = $conn->prepare($query);
$stmt->bindParam(':usertype', $userType);
$stmt->execute();
$accounts = $stmt->fetchAll(PDO::FETCH_ASSOC);

$userid = $_SESSION['userid'];
$query = "SELECT name FROM Users WHERE userid = :userid LIMIT 1";
$stmt = $conn->prepare($query);
$stmt->bindParam(':userid', $userid);
$stmt->execute();
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

$adminName = $admin ? htmlspecialchars($admin['name']) : "Admin";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Accounts</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;700&family=Raleway:wght@300;400;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="../../../public/assets/styles/admin/accounts.css">
</head>
<body>
    
<div class="sidebar">
    <div class="pt-4 pb-1 px-2 text-center">
        <a href="#" class="text-decoration-none">
            <img src="../../../public/assets/images/headerlogo.jpg" alt="Header Logo" class="img-fluid">
        </a>
    </div>

    <div class="menu-section">
        <ul class="nav nav-pills flex-column mb-4">
            <li class="nav-item mb-4">
                <a href="home.php" class="nav-link">
                    <i class="bi bi-grid"></i>
                    <span class="d-none d-sm-inline">Dashboard</span>
                </a>
            </li>
            <li class="nav-item mb-4">
                <a href="monthlyperformance.php" class="nav-link">
                    <i class="bi bi-bar-chart-line"></i>
                    <span class="d-none d-sm-inline">Monthly Performance</span>
                </a>
            </li>
            <li class="nav-item mb-4">
                <a href="tourhistory.php" class="nav-link">
                    <i class="bi bi-map"></i>
                    <span class="d-none d-sm-inline">Tour History</span>
                </a>
            </li>
            <li class="nav-item mb-4">
                <a href="touristsites.php" class="nav-link">
                    <i class="bi bi-image"></i>
                    <span class="d-none d-sm-inline">Tourist Sites</span>
                </a>
            </li>
            <li class="nav-item mb-4">
                <a href="" class="nav-link active">
                    <i class="bi bi-people-fill"></i>
                    <span class="d-none d-sm-inline">Accounts</span>
                </a>
            </li>
            <li class="nav-item mb-4">
                <a href="employeelogs.php" class="nav-link">
                    <i class="bi bi-person-vcard"></i>
                    <span class="d-none d-sm-inline">Employee Logs</span>
                </a>
            </li>
        </ul>
    </div>
        
    <ul class="nav nav-pills flex-column mb-4">
        <li class="nav-item mb-3">
            <a href="javascript:void(0);" class="nav-link admin-name active">
                <i class="bi bi-person-circle"></i>
                <span class="d-none d-sm-inline"><?= $adminName; ?></span>
            </a>
        </li>
        <li class="nav-item">
            <a href="javascript:void(0);" class="nav-link sign-out active" onclick="logoutConfirm()">
                <i class="bi bi-box-arrow-right"></i>
                <span class="d-none d-sm-inline">Sign Out</span>
            </a>
        </li>
    </ul>
</div>

<div class="main-content">
    <div class="content-container">
        <div class="header">
            <h2>Accounts</h2>
            <span class="date">
                <h2> 
                    <?php date_default_timezone_set('Asia/Manila'); echo date('M d, Y | h:i A'); ?> 
                </h2>
            </span>
        </div>
        
        <div class="btn-group" role="group">
            <button type="button" class="btn-custom <?php echo ($userType == 'mngr') ? 'active' : ''; ?>" onclick="filterAccounts('mngr')">Manager</button>
            <button type="button" class="btn-custom <?php echo ($userType == 'emp') ? 'active' : ''; ?>" onclick="filterAccounts('emp')">Employee</button>
            <button type="button" class="btn-custom <?php echo ($userType == 'trst') ? 'active' : ''; ?>" onclick="filterAccounts('trst')">Tourist</button>
        </div>
        
        <div class="search-container mt-2 mb-3">
            <div class="search-wrapper">
                <i class="bi bi-search"></i>
                <input type="text" class="form-control" id="searchBar" placeholder="Search">
            </div>
        </div>
        
        <div class="mt-3 row justify-content-start">
            <?php if ($userType == 'emp'): ?>
                <div class="d-flex justify-content-center col-12 col-md-6 col-lg-3 mb-3">
                    <div class="add-account-box" onclick="showAddAccountModal()" style="cursor: pointer;">
                        <div class="plus-sign">+</div>
                        <p class="add-text">Add Account</p>
                    </div>
                </div>
            <?php endif; ?>
            
            <?php foreach ($accounts as $account): ?>
                <div class="d-flex justify-content-center col-12 col-md-6 col-lg-3 mb-3" id="user-<?php echo $account['userid']; ?>">
                    <div class="info-box <?php echo $account['status'] === 'inactive' ? 'disabled-account' : ''; ?>" onclick="showAccountModal('<?php echo $account['userid']; ?>', '<?php echo $account['name']; ?>', '<?php echo $account['username']; ?>', '<?php echo $account['contactnum']; ?>', '<?php echo $account['email']; ?>', '<?php echo $account['usertype']; ?>', '<?php echo $account['status']; ?>')">
                        <i class="bi bi-person-circle"></i>
                        <p><?php echo htmlspecialchars($account['name']); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<div class="modal fade" id="addAccountModal" tabindex="-1" aria-labelledby="addAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content p-4">
            <div class="modal-header border-0 text-center">
                <h5 class="modal-title w-100 fw-bold">Add Account</h5>
                <button type="button" class="btn-close position-absolute end-0 me-3" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body d-flex flex-column align-items-center">
                <form id="addAccountForm" class="w-75">
                    <div class="d-flex gap-2 mb-3">
                        <input type="text" class="form-control" name="name" placeholder="Full Name" required pattern="^[A-Za-z\s]+$" title="Full name must contain only alphabets and spaces.">
                        <input type="text" class="form-control" name="username" placeholder="Username" required pattern="^\w{3,20}$" title="Username must be 3-20 characters long and can only include letters, numbers, and underscores.">
                    </div>
                    <div class="mb-3">
                        <input type="email" class="form-control" name="email" placeholder="Email" required>
                    </div>
                    <div class="mb-3">
                        <input type="text" class="form-control" name="contactnum" placeholder="Contact Number" required pattern="^\d{11}$" title="Contact number must be exactly 11 digits." inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                    </div>
                    <div class="mb-3 position-relative">
                        <input type="password" class="form-control" name="password" id="addPassword" placeholder="Password" required pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$" title="Password must be at least 8 characters long, include at least one uppercase letter, one lowercase letter, one number, and one special character.">
                        <span class="position-absolute top-50 end-0 translate-middle-y me-3" onclick="togglePassword('addPassword', 'addPasswordIcon')" style="cursor: pointer;">
                            <i class="bi bi-eye" id="addPasswordIcon"></i>
                        </span>
                    </div>
                    <input type="hidden" name="action" value="addAccount">
                    <div class="modal-footer d-flex justify-content-center">
                        <button type="submit" class="btn-custom">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="accountModal" tabindex="-1" aria-labelledby="accountModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 id="modal-name"></h4>
                <div id="modal-buttons"></div> 
            </div>
            <div class="modal-body">
                <div class="container">
                    <div class="row">
                        <div class="col-md-6">
                            <p class="label-text">Username</p>
                            <p class="info-text" id="modal-username"></p>
                            <br>
                            <p class="label-text">Contact Number</p>
                            <p class="info-text" id="modal-contact"></p>
                            <br>
                        </div>
                        <div class="col-md-6">
                            <p class="label-text">Email</p>
                            <p class="info-text" id="modal-email"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editAccountModal" tabindex="-1" aria-labelledby="editAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content p-4">
            <div class="modal-header border-0 text-center">
                <h5 class="modal-title w-100 fw-bold">Edit Account</h5>
                <button type="button" class="btn-close position-absolute end-0 me-3" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body d-flex flex-column align-items-center">
                <form class="w-75" id="editAccountForm">
                    <input type="hidden" id="userid">
                    <div class="d-flex gap-2 mb-3">
                        <input type="text" class="form-control" id="fullName" placeholder="Full Name" required pattern="^[A-Za-z\s]+$" title="Full name must contain only alphabets and spaces.">
                        <input type="text" class="form-control" id="username" placeholder="Username" required pattern="^\w{3,20}$" title="Username must be 3-20 characters long and can only include letters, numbers, and underscores.">
                    </div>
                    <div class="mb-3">
                        <input type="email" class="form-control" id="email" placeholder="Email" required>
                    </div>
                    <div class="mb-3">
                        <input type="text" class="form-control" id="contactNumber" placeholder="Contact Number" required pattern="^\d{11}$" title="Contact number must be exactly 11 digits." inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                    </div>
                    <div class="mb-3 position-relative">
                        <input type="password" class="form-control" id="password" placeholder="New Password (Leave blank to keep current)" required pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$" title="Password must be at least 8 characters long, include at least one uppercase letter, one lowercase letter, one number, and one special character.">
                        <span class="position-absolute top-50 end-0 translate-middle-y me-3" onclick="togglePassword('password', 'editPasswordIcon')" style="cursor: pointer;">
                            <i class="bi bi-eye" id="editPasswordIcon"></i>
                        </span>
                    </div>
                    <div class="edit-modal-footer d-flex justify-content-center">
                        <button type="button" class="btn-custom" id="saveEditChanges">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/js/all.min.js"></script>
<script src="../../../public/assets/scripts/main.js"></script>
<script>
    function showAddAccountModal() {
        var myModal = new bootstrap.Modal(document.getElementById('addAccountModal'));
        myModal.show();
    }

    function togglePassword(inputId, iconId) {
        var passwordInput = document.getElementById(inputId);
        var icon = document.getElementById(iconId);

        if (passwordInput.type === "password") {
            passwordInput.type = "text";
            icon.classList.remove("bi-eye");
            icon.classList.add("bi-eye-slash");
        } else {
            passwordInput.type = "password";
            icon.classList.remove("bi-eye-slash");
            icon.classList.add("bi-eye");
        }
    }

    function showAccountModal(userid, name, username, contact, email, userType, status) {
        document.getElementById('modal-name').innerText = name;
        document.getElementById('modal-username').innerText = username;
        document.getElementById('modal-contact').innerText = contact;
        document.getElementById('modal-email').innerText = email;
        
        document.getElementById('userid').value = userid;
        document.getElementById('fullName').value = name;
        document.getElementById('username').value = username;
        document.getElementById('email').value = email;
        document.getElementById('contactNumber').value = contact;

        let modalButtons = document.getElementById('modal-buttons');
        modalButtons.innerHTML = '';

        if (userType === 'emp') {
            if (status === 'active') {
                modalButtons.innerHTML = `
                    <button class="btn-custom" onclick="showEditAccountModal()">
                        <i class="bi bi-pencil-square"></i> Edit
                    </button>
                    <button type="button" class="btn-custom" onclick="confirmDisable('${userid}')">
                        <i class="bi bi-person-dash"></i> Disable
                    </button>`;
            } else {
                modalButtons.innerHTML = `
                    <button class="btn-custom" onclick="showEditAccountModal()">
                        <i class="bi bi-pencil-square"></i> Edit
                    </button>
                    <button type="button" class="btn-custom" onclick="confirmEnable('${userid}')">
                        <i class="bi bi-person-check"></i> Enable
                    </button>`;
            }
        } else if (userType === 'trst') {
            modalButtons.innerHTML = `
                <button class="btn-custom" onclick="showEditAccountModal()"> 
                    <i class="bi bi-pencil-square"></i> Edit 
                </button>
                <button type="button" class="btn-custom" onclick="confirmDelete('${userid}')">
                    <i class="fas fa-trash-alt"></i> Delete 
                </button>
            `;
        }

        var modal = new bootstrap.Modal(document.getElementById('accountModal'));
        modal.show();
    }

    function showEditAccountModal() {
        var myModal = new bootstrap.Modal(document.getElementById('editAccountModal'));
        myModal.show();
    }

    function confirmDisable(userid) {
        Swal.fire({
            iconHtml: '<i class="fas fa-exclamation-circle"></i>',
            title: "Disable Employee Account?",
            text: "Are you sure you want to disable this employee?",
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
                updateAccountStatus(userid, 'disableAccount');
            }
        });
    }

    function confirmEnable(userid) {
        Swal.fire({
            iconHtml: '<i class="fas fa-check-circle"></i>',
            title: "Enable Employee Account?",
            text: "Are you sure you want to enable this employee?",
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
                updateAccountStatus(userid, 'enableAccount');
            }
        });
    }

    function updateAccountStatus(userid, action) {
        fetch('accounts.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `action=${action}&userid=${userid}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    iconHtml: '<i class="fas fa-circle-check"></i>',
                    title: action === 'disableAccount' ? "Employee Disabled Successfully!" : "Employee Enabled Successfully!",
                    timer: 3000,
                    showConfirmButton: false,
                    customClass: {
                        title: "swal2-title-custom",
                        icon: "swal2-icon-custom",
                        popup: "swal-custom-popup"
                    }
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire("Error", data.message, "error");
            }
        })
        .catch(error => {
            Swal.fire({
                iconHtml: '<i class="fas fa-exclamation-circle"></i>',
                title: "Error",
                text: "An unexpected error occurred.",
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

    function confirmDelete(userid) {
        Swal.fire({
            iconHtml: '<i class="fas fa-trash-alt"></i>',
            title: "Delete Tourist Account?",
            text: "Are you sure you want to delete this tourist?",
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
                deleteUser(userid);
            }
        });
    }

    function deleteUser(userid) {
        fetch("accounts.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: "delete_userid=" + encodeURIComponent(userid)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    iconHtml: '<i class="fas fa-circle-check"></i>',
                    title: "Tourist Deleted Successfully!",
                    timer: 3000,
                    showConfirmButton: false,
                    customClass: {
                        title: "swal2-title-custom",
                        icon: "swal2-icon-custom",
                        popup: "swal-custom-popup"
                    }
                }).then(() => {
                    location.reload();
                    document.getElementById("user-" + userid).remove();
                });
            } else {
                Swal.fire({
                    iconHtml: '<i class="fas fa-exclamation-circle"></i>',
                    title: "Error Deleting Tourist",
                    text: "Please try again later.",
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
        .catch(error => console.error("Error:", error));
    }

    document.addEventListener("DOMContentLoaded", function () {
        document.getElementById("addAccountForm").addEventListener("submit", function (e) {
            e.preventDefault(); 

            Swal.fire({
                iconHtml: '<i class="fas fa-thumbs-up"></i>',
                title: "Confirm New Account Details?",
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
                    const formData = new FormData(document.getElementById("addAccountForm"));

                    fetch("accounts.php", {
                        method: "POST",
                        body: formData
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    iconHtml: '<i class="fas fa-circle-check"></i>',
                                    title: "Account Added Successfully!",
                                    timer: 3000,
                                    showConfirmButton: false,
                                    customClass: {
                                        title: "swal2-title-custom",
                                        icon: "swal2-icon-custom",
                                        popup: "swal-custom-popup"
                                    }
                                }).then(() => location.reload());
                            } else {
                                Swal.fire({
                                    iconHtml: '<i class="fas fa-exclamation-circle"></i>',
                                    title: "Error",
                                    text: data.message || "Failed to add account.",
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
                            console.error("Error:", error);
                            Swal.fire({
                                iconHtml: '<i class="fas fa-exclamation-circle"></i>',
                                title: "Error",
                                text: "An unexpected error occurred.",
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
    });

    document.getElementById("saveEditChanges").addEventListener("click", function () {
        const userid = document.getElementById("userid").value;
        const fullName = document.getElementById("fullName").value;
        const username = document.getElementById("username").value;
        const email = document.getElementById("email").value;
        const contactNumber = document.getElementById("contactNumber").value;
        const password = document.getElementById("password").value.trim();

        if (!fullName || !username || !email || !contactNumber) {
            Swal.fire({
                iconHtml: '<i class="fas fa-exclamation-circle"></i>',
                title: "Missing Fields",
                text: "All fields are required.",
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

        const originalFullName = document.getElementById("modal-name").innerText;
        const originalUsername = document.getElementById("modal-username").innerText;
        const originalEmail = document.getElementById("modal-email").innerText;
        const originalContact = document.getElementById("modal-contact").innerText;

        if (
            fullName === originalFullName &&
            username === originalUsername &&
            email === originalEmail &&
            contactNumber === originalContact &&
            password === ""
        ) {
            Swal.fire({
                iconHtml: '<i class="fas fa-exclamation-circle"></i>',
                title: "No Changes Made",
                text: "You haven't updated any details.",
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
            iconHtml: '<i class="fas fa-thumbs-up"></i>',
            title: "Confirm Changes?",
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
                const formData = new FormData();
                formData.append("action", "editAccount");
                formData.append("userid", userid);
                formData.append("name", fullName);
                formData.append("username", username);
                formData.append("email", email);
                formData.append("contactnum", contactNumber);
                formData.append("password", password);

                fetch("accounts.php", {
                    method: "POST",
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            iconHtml: '<i class="fas fa-check-circle"></i>',
                            title: "Account Updated Successfully!",
                            timer: 3000,
                            showConfirmButton: false,
                            customClass: {
                                title: "swal2-title-custom",
                                icon: "swal2-icon-custom",
                                popup: "swal-custom-popup"
                            }
                        }).then(() => location.reload());
                    } else {
                        Swal.fire({
                            iconHtml: '<i class="fas fa-exclamation-circle"></i>',
                            title: "Error",
                            text: data.message || "Failed to update account.",
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
                    console.error("Error:", error);
                    Swal.fire({
                        iconHtml: '<i class="fas fa-exclamation-circle"></i>',
                        title: "Error",
                        text: "An unexpected error occurred.",
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

    function filterAccounts(type) {
        const url = 'accounts.php?usertype=' + type;
        window.location.href = url;
    }

    document.addEventListener("DOMContentLoaded", function () {
        const searchBar = document.getElementById("searchBar");
        const accountBoxes = document.querySelectorAll(".info-box");
        const addAccountBox = document.querySelector(".add-account-box");
        
        const noAccountsMessage = document.createElement("div");
        noAccountsMessage.className = "no-accounts";
        noAccountsMessage.textContent = "No matching accounts found.";
        noAccountsMessage.style.display = "none"; 
        document.querySelector(".content-container").appendChild(noAccountsMessage);

        searchBar.addEventListener("input", function () {
            const searchQuery = searchBar.value.toLowerCase().trim();
            let hasMatches = false;

            accountBoxes.forEach(box => {
                const accountName = box.querySelector("p").textContent.toLowerCase();

                if (accountName.includes(searchQuery)) {
                    box.style.display = "flex"; 
                    hasMatches = true;
                } else {
                    box.style.display = "none"; 
                }
            });

            if (addAccountBox) {
                addAccountBox.style.display = hasMatches ? "flex" : "none";
            }

            noAccountsMessage.style.display = hasMatches ? "none" : "block";
        });
    });
</script>
</body>
</html>