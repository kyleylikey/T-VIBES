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

    // Check if this is an accept action
    if (isset($data->action) && $data->action === 'accept') {
        // Get tour request details before updating
        $stmt = $tourModel->getTourRequest($tourid, $userid);
        $requestDetails = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($requestDetails) {
            // Accept the tour request
            $result = $tourModel->acceptTourRequest($tourid, $userid);

            $logs = new Logs();
            $logs->logAcceptTourRequest($userid, $tourid);
            
            if ($result) {
                // Send confirmation email
                $emailResult = sendTourConfirmation(
                    $requestDetails['email'],
                    $requestDetails['name'],
                    $requestDetails['date']
                );
                
                echo json_encode(['success' => true, 'emailSent' => $emailResult === true]);
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
    // Check if this is an accept action
    if (isset($data->action) && $data->action === 'decline') {
        // Get tour request details before updating
        $stmt = $tourModel->getTourRequest($tourid, $userid);
        $requestDetails = $stmt->fetch(PDO::FETCH_ASSOC);

        $reason = isset($data->reason) ? $data->reason : 'No reason provided';
        
        if ($requestDetails) {
            // Decline the tour request
            $result = $tourModel->declineTourRequest($tourid, $userid);

            $logs = new Logs();
            $logs->logDeclineTourRequest($userid, $tourid);
            
            if ($result) {
                // Send confirmation email
                $emailResult = sendTourDecline(
                    $requestDetails['email'],
                    $requestDetails['name'],
                    $requestDetails['date'],
                    $reason
                );
                
                echo json_encode(['success' => true, 'emailSent' => $emailResult === true]);
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
    
    // Get tour request details for display
    $stmt = $tourModel->getTourRequest($tourid, $userid);
    $requestdet = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Get sites for this tour request
    $siteStmt = $tourModel->getTourRequestSites($tourid);
    $sites = $siteStmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Include sites in the response
    $requestdet['sites'] = $sites;

    echo json_encode($requestdet);
    exit;
}

function sendTourConfirmation($email, $username, $date) {
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
        $mail->Subject = 'Your Tour Request Has Been Accepted.';
        $mail->Body = "
            <html>
            <head>
                <title>Your Tour Request Has Been Accepted.</title>
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
                        <h1>Request Accepted.</h1>
                    </div>
                    <div class='content'>
                        <h2>Hi $username,</h2>
                        <p>Your tour scheduled on $date is now accepted!</p>
                        <p>Should there be cancellations or special accommodations needed, please let us know by contacting us through our Facebook Page or (number).</p>
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


function sendTourDecline($email, $username, $date, $message) {
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
        $mail->Subject = 'Your Tour Request Has Been Declined.';
        $mail->Body = "
            <html>
            <head>
                <title>Your Tour Request Has Been Declined.</title>
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
                        <h1>Request Declined.</h1>
                    </div>
                    <div class='content'>
                        <h2>Hi $username,</h2>
                        <p>Your tour scheduled on $date has been declined.</p>
                        <p>We regret to inform you that your request has been declined due to the following reason(s):</p>
                        <p>$message</p>
                        <p>If there are any concerns, please let us know by contacting us through our Facebook Page or (number).</p>
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