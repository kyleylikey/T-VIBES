<?php
require_once '../config/dbconnect.php';

ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

function sendconfirmationEmail($username, $email, $verificationToken) {
    $verificationLink = "https://tourtaal.azurewebsites.net/verify.php?token=" . urlencode($verificationToken);
    
    $endpoint = getenv('AZURE_EMAIL_ENDPOINT');
    $apiKey = getenv('AZURE_EMAIL_API_KEY');
    $senderEmail = getenv('AZURE_EMAIL_SENDER');

    if (empty($endpoint) || empty($apiKey) || empty($senderEmail)) {
        error_log("Email configuration missing - ENDPOINT: " . (empty($endpoint) ? "MISSING" : "SET") .
                  ", API_KEY: " . (empty($apiKey) ? "MISSING" : "SET") .
                  ", SENDER: " . (empty($senderEmail) ? "MISSING" : "SET"));
        return false;
    }

    // First, get an access token using the API key
    $tokenEndpoint = "https://communication.azure.com/tokens";
    $tokenHeaders = [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $apiKey
    ];
    
    $tokenPayload = [
        "scope" => "https://communication.azure.com/.default"
    ];
    
    $ch = curl_init($tokenEndpoint);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($tokenPayload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $tokenHeaders);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $tokenResponse = curl_exec($ch);
    $tokenStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    error_log("Token API response - Status: $tokenStatusCode, Response: " . $tokenResponse);
    
    if ($tokenStatusCode != 200) {
        error_log("Failed to get access token");
        return false;
    }
    
    $tokenData = json_decode($tokenResponse, true);
    $accessToken = $tokenData['access_token'];
    
    // Now use the access token to send the email
    $url = "{$endpoint}/emails:send?api-version=2023-03-31";

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
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $accessToken
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    error_log("Email API response - Status: $statusCode, Response: " . $response);
    
    if (curl_errno($ch)) {
        error_log("cURL error: " . curl_error($ch));
    }

    curl_close($ch);
    
    return $statusCode >= 200 && $statusCode < 300;
}