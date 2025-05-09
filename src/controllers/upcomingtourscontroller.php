<?php
require_once __DIR__ .'/../config/dbconnect.php';
require_once __DIR__ .'/../models/Tour.php';
require_once __DIR__.'/../models/Logs.php';
require __DIR__ . '/../../vendor/autoload.php';
date_default_timezone_set('Asia/Manila');

$database = new Database();
$conn = $database->getConnection();
$tourModel = new Tour($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_start();
    $response = ["status" => "error", "message" => "Invalid request"];
    
    if (!isset($_SESSION['userid'])) {
        $response = ["status" => "error", "message" => "User session expired. Please log in again."];
        echo json_encode($response);
        exit;
    }
    
    $userId = $_SESSION['userid'];
    
    if (isset($_POST['editTour']) && !empty($_POST['tourId']) && !empty($_POST['tourDate']) &&
        isset($_POST['tourPax'])) {
        $tourId = (int) $_POST['tourId'];
        $date = $_POST['tourDate'];
        $companions = (int) $_POST['tourPax'];

        $query = "SELECT u.email, u.username FROM [taaltourismdb].[users] u JOIN [taaltourismdb].[tour] t ON u.userid = t.userid WHERE t.tourid = :tourid";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':tourid', $tourId, PDO::PARAM_INT);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($tourModel->updateTour($tourId, $date, $companions, $userId)) {
            if ($user) {
                $emailResult = sendTourUpdateNotification($user['email'], $user['username'], $date, $companions);
                $response = [
                    "status" => "success", 
                    "message" => "Tour updated successfully.",
                    "emailSent" => $emailResult === true
                ];
            } else {
                $response = ["status" => "success", "message" => "Tour updated successfully, but email notification failed."];
            }
        } else {
            $response = ["status" => "error", "message" => "Failed to update tour."];
        }
    }
    
    if (isset($_POST['cancelTour']) && !empty($_POST['tourId']) && isset($_POST['cancelReason'])) {
        $tourId = (int) $_POST['tourId'];
        $cancelReason = trim($_POST['cancelReason']);
        
        $query = "SELECT u.email, u.username FROM [taaltourismdb].[users] u JOIN [taaltourismdb].[tour] t ON u.userid = t.userid WHERE t.tourid = :tourid";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':tourid', $tourId, PDO::PARAM_INT);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($tourModel->cancelTour($tourId)) {
            $logs = new Logs();
            $logs->logCancelTour($userId, $tourId);
            
            if ($user) {
                $emailResult = sendTourCancellationEmail($user['email'], $user['username'], $cancelReason);
                $response = [
                    "status" => "success", 
                    "message" => "Tour successfully cancelled.",
                    "emailSent" => $emailResult === true,
                    "logAdded" => true
                ];
            } else {
                $response = ["status" => "success", "message" => "Tour successfully cancelled, but email notification failed."];
            }
        } else {
            $response = ["status" => "error", "message" => "Tour not found."];
        }
        echo json_encode($response);
        exit;
    }
    
    echo json_encode($response);
    exit;
}

$toursToday = $tourModel->getToursForToday();
$allTours = $tourModel->getAllUpcomingTours();

function sendTourUpdateNotification($email, $username, $date, $companions) {
    // Get Azure Communication Services credentials
    $connectionString = getenv('AZURE_EMAIL_SENDER_CONNECTION_STRING');
    $senderEmail = getenv('AZURE_EMAIL_SENDER');
    
    if (empty($connectionString) || empty($senderEmail)) {
        error_log("Email configuration missing");
        return false;
    }
    
    // Extract credentials from connection string
    preg_match('/endpoint=(.*?);accesskey=(.*?)($|;)/', $connectionString, $matches);
    if (count($matches) < 3) {
        error_log("Invalid connection string format");
        return false;
    }
    
    $endpoint = rtrim($matches[1], '/');
    $accessKey = $matches[2];
    
    $httpVerb = "POST";
    $timestamp = gmdate('D, d M Y H:i:s T', time()); // RFC1123 format
    $host = parse_url($endpoint, PHP_URL_HOST);
    $uriPathAndQuery = "/emails:send?api-version=2023-03-31";
    
    // Create email payload
    $htmlContent = "<!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Tour Updated - Taal Tourism Office</title>
        <style>
            body {
                background-color: #FFFFFF;
                margin: 0;
                padding: 0;
                font-family: 'Helvetica', Arial, sans-serif !important;
            }

            .email-container {
                max-width: 600px;
                margin: 20px auto;
                background-color: #FFFFFF;
                border-radius: 10px;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                overflow: hidden;
            }

            .header {
                background-color: #EC6350;
                color: #FFFFFF;
                text-align: center;
                padding: 20px;
                font-family: 'Helvetica', Arial, sans-serif !important;
            }

            .header h1 {
                margin: 0;
            }

            .content {
                padding: 20px;
                color: #434343;
                text-align: justify;
                font-family: 'Helvetica', Arial, sans-serif !important;
            }

            .content h2 {
                color: #102E47;
                text-align: center;
            }

            .footer {
                background-color: #E7EBEE;
                text-align: center;
                padding: 10px;
                font-size: 12px;
                color: #434343;
                font-family: 'Helvetica', Arial, sans-serif !important;
            }
        </style>
    </head>
    <body>
        <div class='email-container'>
            <div class='header'>
                <h1>Tour Updated</h1>
            </div>
            <div class='content'>
                <h2>Hi $username,</h2>
                <p>Your tour has been updated!</p>
                <p><strong>New Date:</strong> $date</p>
                <p><strong>Number of Companions:</strong> $companions</p>
                <p>If there are any concerns, please let us know by contacting us through our <a href='https://tourtaal.azurewebsites.net/src/views/frontend/contactus.php'>Contact Page</a>.</p>
            </div>
            <div class='footer'>
                <p>&copy; " . date("Y") . " Taal Tourism Office. All rights reserved.</p>
            </div>
        </div>
    </body>
    </html>";

    $plainTextContent = "Hi $username,\n\nYour tour has been updated!\n\nNew Date: $date\nNumber of Companions: $companions\n\nIf there are any concerns, please let us know by contacting us through our Contact Page at https://tourtaal.azurewebsites.net/src/views/frontend/contactus.php.\n\n© " . date("Y") . " Taal Tourism Office. All rights reserved.";

    $payload = [
        "senderAddress" => $senderEmail,
        "content" => [
            "subject" => "Your Tour Has Been Updated!",
            "plainText" => $plainTextContent,
            "html" => $htmlContent
        ],
        "recipients" => [
            "to" => [["address" => $email]]
        ]
    ];

    $payloadJson = json_encode($payload);
    $contentHash = base64_encode(hash('sha256', $payloadJson, true));
    
    // Construct the string to sign
    $stringToSign = $httpVerb . "\n" . 
                   $uriPathAndQuery . "\n" .
                   $timestamp . ";" . $host . ";" . $contentHash;
    
    // Generate HMAC-SHA256 signature
    $signature = base64_encode(hash_hmac('sha256', $stringToSign, base64_decode($accessKey), true));
    
    // Create Authorization header
    $authorization = "HMAC-SHA256 SignedHeaders=x-ms-date;host;x-ms-content-sha256&Signature=" . $signature;
    
    $headers = [
        'Content-Type: application/json',
        'x-ms-date: ' . $timestamp,
        'x-ms-content-sha256: ' . $contentHash,
        'host: ' . $host,
        'Authorization: ' . $authorization
    ];

    $url = "{$endpoint}/emails:send?api-version=2023-03-31";
    
    // Add debugging
    error_log("Making tour update email request to: " . $url);
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payloadJson);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_FAILONERROR, false);
    
    $response = curl_exec($ch);
    $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    error_log("Tour update email response - Status: $statusCode, Response: " . $response);
    
    if (curl_errno($ch)) {
        error_log("cURL error in tour update email: " . curl_error($ch));
        $result = false;
    } else if ($statusCode >= 200 && $statusCode < 300) {
        $result = true;
    } else {
        $result = false;
    }
    
    curl_close($ch);
    return $result;
}

function sendTourCancellationEmail($email, $username, $cancelReason) {
    // Get Azure Communication Services credentials
    $connectionString = getenv('AZURE_EMAIL_SENDER_CONNECTION_STRING');
    $senderEmail = getenv('AZURE_EMAIL_SENDER');
    
    if (empty($connectionString) || empty($senderEmail)) {
        error_log("Email configuration missing");
        return false;
    }
    
    // Extract credentials from connection string
    preg_match('/endpoint=(.*?);accesskey=(.*?)($|;)/', $connectionString, $matches);
    if (count($matches) < 3) {
        error_log("Invalid connection string format");
        return false;
    }
    
    $endpoint = rtrim($matches[1], '/');
    $accessKey = $matches[2];
    
    $httpVerb = "POST";
    $timestamp = gmdate('D, d M Y H:i:s T', time()); // RFC1123 format
    $host = parse_url($endpoint, PHP_URL_HOST);
    $uriPathAndQuery = "/emails:send?api-version=2023-03-31";
    
    // Create email payload
    $htmlContent = "<!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Tour Cancelled - Taal Tourism Office</title>
        <style>
            body {
                background-color: #FFFFFF;
                margin: 0;
                padding: 0;
                font-family: 'Helvetica', Arial, sans-serif !important;
            }

            .email-container {
                max-width: 600px;
                margin: 20px auto;
                background-color: #FFFFFF;
                border-radius: 10px;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                overflow: hidden;
            }

            .header {
                background-color: #EC6350;
                color: #FFFFFF;
                text-align: center;
                padding: 20px;
                font-family: 'Helvetica', Arial, sans-serif !important;
            }

            .header h1 {
                margin: 0;
            }

            .content {
                padding: 20px;
                color: #434343;
                text-align: justify;
                font-family: 'Helvetica', Arial, sans-serif !important;
            }

            .content h2 {
                color: #102E47;
                text-align: center;
            }

            .footer {
                background-color: #E7EBEE;
                text-align: center;
                padding: 10px;
                font-size: 12px;
                color: #434343;
                font-family: 'Helvetica', Arial, sans-serif !important;
            }
        </style>
    </head>
    <body>
        <div class='email-container'>
            <div class='header'>
                <h1>Tour Cancelled</h1>
            </div>
            <div class='content'>
                <h2>Hi $username,</h2>
                <p>We regret to inform you that your tour has been cancelled.</p>
                <p><strong>Reason:</strong> $cancelReason</p>
                <p>We apologize for the inconvenience. If you have any concerns, please contact us through our <a href='https://tourtaal.azurewebsites.net/src/views/frontend/contactus.php'>Contact Page</a>.</p>
            </div>
            <div class='footer'>
                <p>&copy; " . date("Y") . " Taal Tourism Office. All rights reserved.</p>
            </div>
        </div>
    </body>
    </html>";

    $plainTextContent = "Hi $username,\n\nWe regret to inform you that your tour has been cancelled.\n\nReason: $cancelReason\n\nWe apologize for the inconvenience. If you have any concerns, please contact us through our Contact Page at https://tourtaal.azurewebsites.net/src/views/frontend/contactus.php.\n\n© " . date("Y") . " Taal Tourism Office. All rights reserved.";

    $payload = [
        "senderAddress" => $senderEmail,
        "content" => [
            "subject" => "Your Tour Has Been Cancelled!",
            "plainText" => $plainTextContent,
            "html" => $htmlContent
        ],
        "recipients" => [
            "to" => [["address" => $email]]
        ]
    ];

    $payloadJson = json_encode($payload);
    $contentHash = base64_encode(hash('sha256', $payloadJson, true));
    
    // Construct the string to sign
    $stringToSign = $httpVerb . "\n" . 
                   $uriPathAndQuery . "\n" .
                   $timestamp . ";" . $host . ";" . $contentHash;
    
    // Generate HMAC-SHA256 signature
    $signature = base64_encode(hash_hmac('sha256', $stringToSign, base64_decode($accessKey), true));
    
    // Create Authorization header
    $authorization = "HMAC-SHA256 SignedHeaders=x-ms-date;host;x-ms-content-sha256&Signature=" . $signature;
    
    $headers = [
        'Content-Type: application/json',
        'x-ms-date: ' . $timestamp,
        'x-ms-content-sha256: ' . $contentHash,
        'host: ' . $host,
        'Authorization: ' . $authorization
    ];

    $url = "{$endpoint}/emails:send?api-version=2023-03-31";
    
    // Add debugging
    error_log("Making tour cancellation email request to: " . $url);
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payloadJson);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_FAILONERROR, false);
    
    $response = curl_exec($ch);
    $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    error_log("Tour cancellation email response - Status: $statusCode, Response: " . $response);
    
    if (curl_errno($ch)) {
        error_log("cURL error in tour cancellation email: " . curl_error($ch));
        $result = false;
    } else if ($statusCode >= 200 && $statusCode < 300) {
        $result = true;
    } else {
        $result = false;
    }
    
    curl_close($ch);
    return $result;
}
?>