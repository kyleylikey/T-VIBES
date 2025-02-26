<?php
require_once  __DIR__ .'/../config/dbconnect.php';
require_once  __DIR__ .'/../models/Site.php';

session_start();

if (!isset($_SESSION['userid'])) {
    header('Location: ../frontend/login.php'); 
    exit();
}

if ($_SESSION['usertype'] !== 'emp') {
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
$siteModel = new Site($db);
$sites = $siteModel->getSiteList();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action === 'editSite') {
        $siteid = $_POST['siteid'];
        $name = !empty(trim($_POST['siteName'])) ? $_POST['siteName'] : null;
        $price = !empty(trim($_POST['sitePrice'])) ? $_POST['sitePrice'] : null;
        $opdays = !empty(trim($_POST['siteOpDays'])) ? $_POST['siteOpDays'] : null;
        $desc = !empty(trim($_POST['siteDescription'])) ? $_POST['siteDescription'] : null;
        $img = !empty($_FILES['editimageUpload']['name']) ? $_FILES['editimageUpload'] : null;
        
        $updateData = [];
        if (!is_null($name)) {
            $updateData['sitename'] = $name;
        }
        if (!is_null($price)) {
            $updateData['price'] = $price;
        }
        if (!is_null($opdays)) {
            $updateData['opdays'] = $opdays;
        }
        if (!is_null($desc)) {
            $updateData['description'] = $desc;
        }
        if (!is_null($img)) {
            // Handle file upload
            $targetDir = __DIR__ . '/../../public/uploads/';
            $targetFile = $targetDir . basename($img['name']);
            if (move_uploaded_file($img['tmp_name'], $targetFile)) {
                $updateData['siteimage'] = basename($img['name']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to upload image.']);
                exit();
            }
        }
        $result = $siteModel->updateSite($siteid, $updateData);
        echo json_encode(['status' => $result ? 'success' : 'error', 'message' => $result ? 'Site updated successfully.' : 'Failed to update site.']);
        exit();
    }
    elseif ($action === 'addSite') {
        $name = !empty(trim($_POST['siteName'])) ? $_POST['siteName'] : null;
        $price = !empty(trim($_POST['sitePrice'])) ? $_POST['sitePrice'] : null;
        $opdays = !empty(trim($_POST['asiteOpDays'])) ? $_POST['asiteOpDays'] : null;
        $desc = !empty(trim($_POST['siteDescription'])) ? $_POST['siteDescription'] : null;
        $img = !empty($_FILES['imageUpload']['name']) ? $_FILES['imageUpload'] : null;
        
        $updateData = [];
        if (!is_null($name)) {
            $updateData['sitename'] = $name;
        }
        if (!is_null($price)) {
            $updateData['price'] = $price;
        }
        if (!is_null($opdays)) {
            $updateData['opdays'] = $opdays;
        }
        if (!is_null($desc)) {
            $updateData['description'] = $desc;
        }
        if (!is_null($img)) {
            // Handle file upload
            $targetDir = __DIR__ . '/../../public/uploads/';
            $targetFile = $targetDir . basename($img['name']);
            if (move_uploaded_file($img['tmp_name'], $targetFile)) {
                $updateData['siteimage'] = basename($img['name']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to upload image.']);
                exit();
            }
        }
        $result = $siteModel->addSite($updateData);
        echo json_encode(['status' => $result ? 'success' : 'error', 'message' => $result ? 'Site added successfully.' : 'Failed to add site.']);
        exit();

    }
}

