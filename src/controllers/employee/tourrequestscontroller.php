<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../../config/dbconnect.php';
require_once  __DIR__ .'/../../models/Tour.php';
require_once  __DIR__ .'/../../models/Logs.php';
require __DIR__ . '/../../../vendor/autoload.php';


$database = new Database();
$conn = $database->getConnection();
$tourModel = new Tour($conn);
$requests = $tourModel->getTourRequestList();

$data = json_decode(file_get_contents("php://input"));

if (isset($data->tourid) && isset($data->userid)) {
    $tourid = $data->tourid;
    $userid = $data->userid;

    if (isset($data->action) && $data->action === 'accept') {
        $stmt = $tourModel->getTourRequest($tourid, $userid);
        $requestDetails = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($requestDetails) {
            session_start();
            $empid = $_SESSION['userid']; 
    
            $result = $tourModel->acceptTourRequest($tourid, $userid, $empid);
            
            if ($result) {
                $emailResult = sendTourConfirmation(
                    $requestDetails['email'],
                    $requestDetails['name'],
                    $requestDetails['date']
                );
    
                echo json_encode([
                    'success' => true, 
                    'emailSent' => $emailResult === true,
                    'logAdded' => true
                ]);
                exit;
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update tour status']);
                exit;
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Tour request not found']);
            exit;
        }
    }    
    
    if (isset($data->action) && $data->action === 'decline') {
        $stmt = $tourModel->getTourRequest($tourid, $userid);
        $requestDetails = $stmt->fetch(PDO::FETCH_ASSOC);

        $reason = isset($data->reason) ? $data->reason : 'No reason provided';
        
        if ($requestDetails) {
            session_start();
            $empid = $_SESSION['userid']; 
            
            $result = $tourModel->declineTourRequest($tourid, $userid, $empid);
            
            if ($result) {
                $emailResult = sendTourDecline(
                    $requestDetails['email'],
                    $requestDetails['name'],
                    $requestDetails['date'],
                    $reason
                );
                
                echo json_encode([
                    'success' => true, 
                    'emailSent' => $emailResult === true,
                    'logAdded' => true
                ]);
                exit;
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update tour status']);
                exit;
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Tour request not found']);
            exit;
        }
    }
    
    $stmt = $tourModel->getTourRequest($tourid, $userid);
    $requestdet = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $siteStmt = $tourModel->getTourRequestSites($tourid);
    $sites = $siteStmt->fetchAll(PDO::FETCH_ASSOC);
    
    $requestdet['sites'] = $sites;

    echo json_encode($requestdet);
    exit;
}

function sendTourConfirmation($email, $username, $date) {
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
        <title>Accepted Tour Request - Taal Tourism Office</title>
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
                <h1>Request Accepted</h1>
            </div>
            <div class='content'>
                <h2>Hi $username,</h2>
                <p>Your tour scheduled on $date is now accepted!</p>
                <p>Should there be cancellations or special accommodations needed, please let us know by contacting us through our Facebook Page or (number).</p>
            </div>
            <div class='footer'>
                <p>&copy; " . date("Y") . " Taal Tourism Office. All rights reserved.</p>
            </div>
        </div>
    </body>
    </html>";

    $plainTextContent = "Hi $username,\n\nYour tour scheduled on $date is now accepted!\n\nShould there be cancellations or special accommodations needed, please let us know by contacting us through our Facebook Page or (number).\n\n© " . date("Y") . " Taal Tourism Office. All rights reserved.";

    $payload = [
        "senderAddress" => $senderEmail,
        "content" => [
            "subject" => "Your Tour Request Has Been Accepted!",
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
    error_log("Making tour confirmation email request to: " . $url);
    
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
    
    error_log("Tour confirmation email response - Status: $statusCode, Response: " . $response);
    
    if (curl_errno($ch)) {
        error_log("cURL error in tour confirmation email: " . curl_error($ch));
        $result = false;
    } else if ($statusCode >= 200 && $statusCode < 300) {
        $result = true;
    } else {
        $result = false;
    }
    
    curl_close($ch);
    return $result;
}

function sendTourDecline($email, $username, $date, $message) {
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
        <title>Declined Tour Request - Taal Tourism Office</title>
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
                <h1>Request Declined</h1>
            </div>
            <div class='content'>
                <h2>Hi $username,</h2>
                <p>Your tour scheduled on $date has been declined!</p>
                <p>We regret to inform you that your request has been declined due to the following reason(s):</p>
                <p>$message</p>
                <p>If there are any concerns, please let us know by contacting us through our Facebook Page or (number).</p>
            </div>
            <div class='footer'>
                <p>&copy; " . date("Y") . " Taal Tourism Office. All rights reserved.</p>
            </div>
        </div>
    </body>
    </html>";

    $plainTextContent = "Hi $username,\n\nYour tour scheduled on $date has been declined!\n\nWe regret to inform you that your request has been declined due to the following reason(s):\n\n$message\n\nIf there are any concerns, please let us know by contacting us through our Facebook Page or (number).\n\n© " . date("Y") . " Taal Tourism Office. All rights reserved.";

    $payload = [
        "senderAddress" => $senderEmail,
        "content" => [
            "subject" => "Your Tour Request Has Been Declined!",
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
    error_log("Making tour decline email request to: " . $url);
    
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
    
    error_log("Tour decline email response - Status: $statusCode, Response: " . $response);
    
    if (curl_errno($ch)) {
        error_log("cURL error in tour decline email: " . curl_error($ch));
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