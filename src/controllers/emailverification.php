<?php
require_once '../config/dbconnect.php';

ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

function sendconfirmationEmail($username, $email, $verificationToken) {
    $verificationLink = "https://tourtaal.azurewebsites.net/src/controllers/verify.php?token=" . urlencode($verificationToken);

    try {
        // Azure Email Communication Services credentials
        $endpoint = getenv('AZURE_EMAIL_ENDPOINT');
        $apiKey = getenv('AZURE_EMAIL_API_KEY');   
        $senderEmail = getenv('AZURE_EMAIL_SENDER');
        $senderName = 'Taal Tourism Office';
        
        // Create HTML email content
        $htmlContent = "
            <!DOCTYPE html>
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
            </html>";
        
        // Create plain text alternative
        $plainText = "Hi $username,\n\nThank you for signing up at Taal Tourism! Your account has been successfully created.\n\nPlease verify your email address by clicking the link below:\n$verificationLink\n\n© " . date("Y") . " Taal Tourism Office. All rights reserved.";
        
        // PHP script that executes JavaScript using Node.js
        $jsScript = <<<EOT
        const { EmailClient } = require("@azure/communication-email");
        
        async function sendEmail() {
            try {
                const connectionString = process.env.COMMUNICATION_SERVICES_CONNECTION_STRING;
                const client = new EmailClient(connectionString);
                
                const emailMessage = {
                    senderAddress: "$senderEmail",
                    senderName: "$senderName",
                    content: {
                        subject: "Verify Email Address",
                        plainText: `$plainText`,
                        html: `$htmlContent`,
                    },
                    recipients: {
                        to: [
                            { 
                                address: "$email",
                                displayName: "$username"
                            }
                        ],
                    },
                };
                
                const poller = await client.beginSend(emailMessage);
                const result = await poller.pollUntilDone();
                console.log("Email sent successfully:", result);
                return result;
            } catch (error) {
                console.error("Error sending email:", error);
                throw error;
            }
        }
        
        sendEmail();
        EOT;
        
        // Save the JavaScript to a temporary file
        $tempFile = tempnam(sys_get_temp_dir(), 'email_script_');
        file_put_contents($tempFile, $jsScript);
        
        // Execute the Node.js script
        $command = "node " . escapeshellarg($tempFile);
        $output = [];
        $returnVar = 0;
        exec($command, $output, $returnVar);
        
        // Clean up the temporary file
        unlink($tempFile);
        
        if ($returnVar !== 0) {
            error_log("Node.js execution failed with code: " . $returnVar);
            error_log("Output: " . implode("\n", $output));
            return "Email could not be sent. Node.js Error: " . implode("\n", $output);
        }
        
        return true;
        
    } catch (Exception $e) {
        error_log("Email sending failed: " . $e->getMessage());
        return "Message could not be sent. Error: " . $e->getMessage();
    }
}
?>