<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '..\..\vendor\autoload.php';
require_once '../config/dbconnect.php';

ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', '/T-VIBES/temp/error.log');
error_reporting(E_ALL);

function sendconfirmationEmail($username, $email, $verificationToken) {
    $verificationLink = "localhost/T-VIBES/src/controllers/verify.php?token=" . urlencode($verificationToken);

    $mail = new PHPMailer(true);

    try {
        // Server settings
        //$mail->SMTPDebug = SMTP::DEBUG_SERVER;
        $mail->isSMTP();
        $mail->SMTPAuth   = true;
        
        $mail->Host       = 'smtp.gmail.com';
        $mail->Username   = 'kyleashleighbaldoza.tomcat@gmail.com';
        $mail->Password   = 'ikkt npxt cghd dhbj';
        
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        // Recipients
        $mail->setFrom('kyleashleighbaldoza.tomcat@gmail.com', 'Taal Tourist Site');
        $mail->addAddress($email);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Verify Email Address';
        $mail->Body = "
            <html>
            <head>
                <title>Verify Your Email - Taal Tourism</title>
                <link href='https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;700&family=Raleway:wght@300;400;700&display=swap' rel='stylesheet'>
                <style>
                    body {
                        font-family: 'Nunito', sans-serif;
                        background-color: #ffffff;
                        margin: 0;
                        padding: 0;
                    }
                    .email-container {
                        max-width: 600px;
                        margin: 20px auto;
                        background-color: #ffffff;
                        border-radius: 10px;
                        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                        overflow: hidden;
                    }
                    .header {
                        background-color: #102E47;
                        color: #ffffff;
                        text-align: center;
                        padding: 20px;
                    }
                    .header h1 {
                        margin: 0;
                    }
                    .content {
                        padding: 20px;
                        color: #434343;
                        text-align: justify;
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
                        background-color: #102E47;
                        color: #ffffff !important;
                        text-decoration: none;
                        border-radius: 5px;
                        font-size: 16px;
                        text-align: center;
                        font-weight: bold;
                    }
                    .button:hover {
                        background-color: #729AB8;
                    }
                    .footer {
                        background-color: #E7EBEE;
                        text-align: center;
                        padding: 10px;
                        font-size: 12px;
                        color: #434343;
                    }
                </style>
            </head>
            <body>
                <div class='email-container'>
                    <div class='header'>
                        <h1>Welcome to Taal Tourism</h1>
                    </div>
                    <div class='content'>
                        <h2>Hi $username,</h2>
                        <p>Thank you for signing up at Taal Tourism! Your account has been successfully created.</p>
                        <p>Please verify your email address to activate your account and enjoy exploring the beauty of Taal!</p>
                        <a href='$verificationLink' class='button'>Verify Email Address</a>
                    </div>
                    <div class='footer'>
                        <p>&copy; " . date("Y") . " Taal Tourism. All rights reserved.</p>
                    </div>
                </div>
            </body>
            </html>";

        $mail->send();

        return true;
    } catch (Exception $e) {
        return "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

?>