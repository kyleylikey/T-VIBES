<?php
require_once '../../config/dbconnect.php';
require_once '../../models/User.php';

session_start();

if (!isset($_SESSION['userid'])) {
    header('Location: ../frontend/login.php'); 
    exit();
}

if ($_SESSION['usertype'] !== 'mngr') {
    echo "<!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Access Denied</title>
        <link rel='stylesheet' href='../../../public/assets/styles/main.css'>
        <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css'>
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        <script>
            setTimeout(function() {
                Swal.fire({
                    iconHtml: '<i class=\"fas fa-exclamation-circle\"></i>',
                    customClass: {
                        icon: 'swal2-icon swal2-error-icon',
                    },
                    html: '<p style=\"font-size: 24px; font-weight: bold;\">Access Denied! You do not have permission to access this page.</p>',
                    showConfirmButton: false,
                    timer: 3000
                }).then(() => {
                    window.location.href = '../frontend/login.php';
                });
            }, 100);
        </script>
        <style>
            .swal2-popup {
                border-radius: 12px;
                padding: 20px;
            }
            .swal2-icon.swal2-error-icon {
                border: none;
                font-size: 10px;
                display: flex;
                align-items: center;
                justify-content: center;
                width: 60px;
                height: 60px;
                color: #333;
            }
        </style>
    </head>
    <body></body>
    </html>";
    exit();
}

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");

$database = new Database();
$db = $database->getConnection();
$userModel = new User($db);
$accounts = $userModel->getUserList();
$usertypes = ['mngr' => 'Manager', 'emp' => 'Employee', 'trst' => 'Tourist'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'addEmpAccount') {
    $name = $_POST['name'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $contactnum = $_POST['contactnum'];
    $plainPassword = $_POST['password'];

    if ($userModel->doesUserExist($email, $username)) {
        echo json_encode(['status' => 'error', 'message' => 'User already exists.']);
        exit();
    }

    $result = $userModel->addEmpAccount($name, $username, $email, $contactnum, $plainPassword);

    if ($result) {
        echo json_encode(['status' => 'success', 'message' => 'Employee account created successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to create employee account.']);
    }
    exit();
}