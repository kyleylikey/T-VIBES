<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once  __DIR__ .'/../config/dbconnect.php';
require_once  __DIR__ .'/../models/Tour.php';
require_once  __DIR__ .'/../models/Logs.php';
require __DIR__ . '/../../vendor/autoload.php';


date_default_timezone_set('Asia/Manila');

$database = new Database();
$conn = $database->getConnection();
$tourModel = new Tour($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = ["status" => "error", "message" => "Invalid request"];

    if (isset($_POST['editTour']) && !empty($_POST['tourId']) && !empty($_POST['tourDate']) && isset($_POST['tourPax'])) {
        $tourId = (int) $_POST['tourId'];
        $date = $_POST['tourDate'];
        $companions = (int) $_POST['tourPax'];

        if ($tourModel->updateTour($tourId, $date, $companions)) {
            $logs = new Logs();
            $logs->logEditTour($_SESSION['userid'], $tourId);
            $response = ["status" => "success", "message" => "Tour updated successfully."];
        } else {
            $response = ["status" => "error", "message" => "Failed to update tour."];
        }
    }


    if (isset($_POST['cancelTour']) && !empty($_POST['tourId'] && !empty($_POST['userId']))) {
        $tourId = (int) $_POST['tourId'];
        $userId = (int) $_POST['userId'];
        $stmt = $tourModel->getUpcomingTour($tourId, $userId);
        $requestDetails = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($requestDetails) {
            $email = $requestDetails['email']; // Ensure this field is fetched in your query
            $username = $requestDetails['username']; // Ensure this field is fetched in your query
            $date = $requestDetails['date']; // Ensure this field is fetched in your query
            $reason = $_POST['cancelReason']; // Get the cancellation reason from the form

            if ($tourModel->cancelTour($tourId)) {
                $logs = new Logs();
                $logs->logCancelTour($_SESSION['userid'], $tourId);

                // Send email notification
                $emailSent = sendTourDecline($email, $username, $date, $reason);

                if ($emailSent) {
                    $response = ["status" => "success", "message" => "Tour successfully cancelled and email sent."];
                } else {
                    $response = ["status" => "success", "message" => "Tour successfully cancelled, but email failed to send."];
                }
            } else {
                $response = ["status" => "error", "message" => "Failed to cancel tour."];
            }
        } else {
            $response = ["status" => "error", "message" => "Tour not found."];
        }
    }

    echo json_encode($response);
    exit;
}

$toursToday = $tourModel->getToursForToday();
$allTours = $tourModel->getAllUpcomingTours();

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
        $mail->Subject = 'Your Upcoming Tour Has Been Cancelled.';
        $mail->Body = "
            <html>
            <head>
                <title>Your Upcoming Tour Has Been Cancelled.</title>
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
                        <h1>Tour Cancelled.</h1>
                    </div>
                    <div class='content'>
                        <h2>Hi $username,</h2>
                        <p>Your tour scheduled on $date has been cancelled.</p>
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
