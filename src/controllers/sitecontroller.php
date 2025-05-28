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

        $opdays = str_repeat("0", 7);
        if (!empty($_POST["adays"])) {
            foreach ($_POST["adays"] as $day) {
                $opdays[$day] = "1";
            }
        }

        $siteImage = $_FILES["imageUpload"]["name"] ?? "";

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

        $opdays = str_repeat("0", 7);
        if (!empty($_POST["editDays"])) {
            foreach ($_POST["editDays"] as $day) {
                $opdays[$day] = "1";
            }
        }

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