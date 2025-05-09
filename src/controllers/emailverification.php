<?php
require_once '../config/dbconnect.php';

ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

function sendconfirmationEmail($username, $email, $verificationToken) {
    $verificationLink = "https://tourtaal.azurewebsites.net/src/controllers/verify.php?token=email=" . urlencode($email) . "&token=$verificationToken";
    
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
    

    $payload = [
        "senderAddress" => $senderEmail,
        "content" => [
            "subject" => "Verify Your Email - Taal Tourism Office",
            "plainText" => "Click this link to verify your email: {$verificationLink}",
            "html" => "<!DOCTYPE html>
            <html lang='en'>
            <head>
                <meta charset='UTF-8'>
                <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                <title>Verify Your Email - Taal Tourism Office</title>
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

                    .button {
                        display: block;
                        width: fit-content;
                        margin: 20px auto;
                        padding: 10px 20px;
                        border: 2px solid #102E47;
                        background-color: #FFFFFF;
                        text-decoration: none;
                        border-radius: 5px;
                        font-size: 16px;
                        text-align: center;
                        font-weight: bold;
                        border-radius: 25px;
                        color: #434343 !important;
                        cursor: pointer;
                        transition: all 0.3s ease;
                    }

                    .button:hover {
                        background-color: #102E47;
                        color: #FFFFFF !important;
                        border: 2px solid #102E47;
                        font-weight: bold;
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
                        <h1>Welcome to Taal Tourism Office</h1>
                    </div>
                    <div class='content'>
                        <h2>Hi $username,</h2>
                        <p>Thank you for signing up at Taal Tourism! Your account has been successfully created.</p>
                        <p>Please verify your email address to activate your account and enjoy exploring the beauty of Taal!</p>
                        <a href='$verificationLink' class='button'>Verify Email Address</a>
                    </div>
                    <div class='footer'>
                        <p>&copy; " . date("Y") . " Taal Tourism Office. All rights reserved.</p>
                    </div>
                </div>
            </body>
            </html>"
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
    error_log("Making request to: " . $url);
    error_log("Authorization: " . $authorization);
    error_log("Content hash: " . $contentHash);
    

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_VERBOSE, true);
    
    // Add these to your existing cURL options
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    // Add error handling with more details
    curl_setopt($ch, CURLOPT_FAILONERROR, false);
    $response = curl_exec($ch);
    $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    error_log("Email API response - Status: $statusCode, Response: " . $response);
    
    if (curl_errno($ch)) {
        error_log("cURL error: " . curl_error($ch));
    }
    
    curl_close($ch);
    
    return $statusCode >= 200 && $statusCode < 300;
}