<?php
require_once  __DIR__ .'/../config/dbconnect.php';
require_once  __DIR__ .'/../models/Site.php';
require_once  __DIR__ .'/../models/Logs.php';

$database = new Database();
$conn = $database->getConnection();
$siteModel = new Site($conn);
$sites = $siteModel->getSiteList();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"])) {
    if ($_POST["action"] == "addSite") {
        $siteName = $_POST["siteName"] ?? null;
        $sitePrice = $_POST["sitePrice"] ?? null;
        $siteDescription = $_POST["siteDescription"] ?? null;
    
        // Initialize opdays as an array of "0"s
        $opdaysArray = array_fill(0, 7, "0");
        if (!empty($_POST["adays"])) {
            foreach ($_POST["adays"] as $day) {
                $opdaysArray[$day] = "1"; // Update the corresponding index
            }
        }
        $opdays = implode("", $opdaysArray); // Convert array to binary string
    
        
        // Fix the file upload handling
        $siteImage = "";
        if (!empty($_FILES["imageUpload"]["name"])) {
            $targetDir = $_SERVER['DOCUMENT_ROOT'] . '/public/uploads/';
            $siteImage = basename($_FILES["imageUpload"]["name"]);
            $targetFilePath = $targetDir . $siteImage;
            
            if (!move_uploaded_file($_FILES["imageUpload"]["tmp_name"], $targetFilePath)) {
                echo json_encode(['status' => 'error', 'message' => 'Failed to upload image.']);
                exit();
            }
        }
        
        if ($siteName && $sitePrice && $siteDescription) {
            $siteModel->addSite($siteName, $sitePrice, $siteDescription, $opdays, $siteImage);
            $logs = new Logs();
            $logs->logAddSite($_SESSION['userid'], $siteName);
            header("Location: touristsites.php");
            exit();
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Missing required fields.']);
            exit();
        }
    }

    if ($_POST["action"] == "editSite") {
        $siteId = $_POST["editSiteId"] ?? null;
        $siteName = $_POST["siteName"] ?? null;
        $sitePrice = $_POST["sitePrice"] ?? null;
        $siteDescription = $_POST["siteDescription"] ?? null;

        // Initialize opdays as an array of "0"s
        $opdaysArray = array_fill(0, 7, "0");
        if (!empty($_POST["editDays"])) {
            foreach ($_POST["editDays"] as $day) {
                $opdaysArray[$day] = "1"; // Update the corresponding index
            }
        }
        $opdays = implode("", $opdaysArray); // Convert array to binary string

        $imageName = null;
        if (!empty($_FILES["imageUpload"]["name"])) {
            $targetDir = $_SERVER['DOCUMENT_ROOT'] . '/public/uploads/';
            $imageName = basename($_FILES["imageUpload"]["name"]);
            $targetFilePath = $targetDir . $imageName;

            if (move_uploaded_file($_FILES["imageUpload"]["tmp_name"], $targetFilePath)) {
                $siteModel->editSite($siteId, $siteName, $sitePrice, $siteDescription, $opdays, $imageName);
                $logs = new Logs();
                $logs->logEditSite($_SESSION['userid'], $siteName);
                header("Location: touristsites.php");
            }
        } else {
            $siteModel->editSite($siteId, $siteName, $sitePrice, $siteDescription, $opdays);
            $logs = new Logs();
            $logs->logEditSite($_SESSION['userid'], $siteName);
        }

    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_site'])) {
    $siteId = intval($_POST['delete_site']);
    
    $siteDetails = $siteModel->getSiteDetails($siteId);    
    $success = $siteModel->deleteSite($siteId);

    if ($success) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
    exit;
}
?>