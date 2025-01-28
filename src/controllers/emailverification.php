<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '..\..\vendor\autoload.php';

function sendconfirmationEmail($username, $email) {

$verificationLink = "localhost/T-VIBES/src/controllers/verify.php?email=" . urlencode($email);

//Create an instance; passing true enables exceptions
$mail = new PHPMailer(true);

try {
    //Server settings
    //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                    //Enable verbose debug output
    $mail->isSMTP();                                            //Send using SMTP
    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    
    $mail->Host       = 'smtp.gmail.com';                       //Set the SMTP server to send through
    $mail->Username   = 'kyleashleighbaldoza.tomcat@gmail.com';   //SMTP username
    $mail->Password   = 'otlg tqtz gpwv kqjn';                  //SMTP password
    
    $mail->SMTPSecure = 'tls';                                  //Enable implicit TLS encryption
    $mail->Port       = 587;                                    //TCP port to connect to; use 587 if you have set SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS

    //Recipients
    $mail->setFrom('kyleashleighbaldoza.tomcat@gmail.com', 'Taal Tourist Site');
    $mail->addAddress($email);     //Add a recipient

    //Content
    $mail->isHTML(true);           //Set email format to HTML
    $mail->Subject = 'Verify Email Address';
    $mail->Body = "
        <html>
        <head>
            <title>Verify Your Email - Taal Tourism</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    background-color: #f4f4f4;
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
                    background-color: #3a4989;
                    color: #ffffff;
                    text-align: center;
                    padding: 20px;
                }
                .header h1 {
                    margin: 0;
                }
                .content {
                    padding: 20px;
                    color: #333333;
                    text-align: justify;
                }
                .content h2 {
                    color: #3a4989;
                    text-align: center;
                }
                .button {
                    display: block;
                    width: fit-content;
                    margin: 20px auto;
                    padding: 10px 20px;
                    background-color: #3a4989;
                    color: #ffffff !important;
                    text-decoration: none;
                    border-radius: 5px;
                    font-size: 16px;
                    text-align: center;
                    font-weight: bold;
                }
                .button:hover {
                    background-color: #2f3c6d;
                }
                .footer {
                    background-color: #f4f4f4;
                    text-align: center;
                    padding: 10px;
                    font-size: 12px;
                    color: #666666;
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
    return "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";}

}

?>