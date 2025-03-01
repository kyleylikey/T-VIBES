<?php
require_once  __DIR__ .'/../config/dbconnect.php';
require_once  __DIR__ .'/../models/Site.php';

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

