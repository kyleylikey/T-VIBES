<?php
require_once  __DIR__ .'/../models/Logs.php';
require_once  __DIR__ .'/../config/dbconnect.php';


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if ($_SESSION['usertype']==="emp") {
    $logs = new Logs();
    $logs->logLogout($_SESSION['userid']);
}
session_unset();
session_destroy();

// Prevent caching after logout
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");


header("Location: /T-VIBES/public/index.php");
exit();
?>