<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

<<<<<<< HEAD
require_once  __DIR__ .'/../config/dbconnect.php';
require_once  __DIR__ .'/../models/Tour.php';
require __DIR__ . '/../../vendor/autoload.php'; 
=======
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once  __DIR__ .'/../config/dbconnect.php';
require_once  __DIR__ .'/../models/Tour.php';
require_once  __DIR__ .'/../models/Logs.php';
require __DIR__ . '/../../vendor/autoload.php';

>>>>>>> 84496e9cbdaa0e8c884e648f46cf165cb8e7ce98

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

    if (isset($_POST['editTour']) && !empty($_POST['tourId']) && !empty($_POST['tourDate']) && isset($_POST['tourPax'])) {
        $tourId = (int) $_POST['tourId'];
        $date = $_POST['tourDate'];
        $companions = (int) $_POST['tourPax'];
<<<<<<< HEAD
        
        $query = "SELECT u.email, u.username FROM users u JOIN tour t ON u.userid = t.userid WHERE t.tourid = :tourid";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':tourid', $tourId, PDO::PARAM_INT);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($tourModel->updateTour($tourId, $date, $companions, $userId)) {
            if ($user) {
                sendEmailNotification($user['email'], $user['username'], $date, $companions);
            }
=======

        if ($tourModel->updateTour($tourId, $date, $companions)) {
            $logs = new Logs();
            $logs->logEditTour($_SESSION['userid'], $tourId);
>>>>>>> 84496e9cbdaa0e8c884e648f46cf165cb8e7ce98
            $response = ["status" => "success", "message" => "Tour updated successfully."];
        } else {
            $response = ["status" => "error", "message" => "Failed to update tour."];
        }
    }

<<<<<<< HEAD
    if (isset($_POST['cancelTour']) && !empty($_POST['tourId']) && isset($_POST['cancelReason'])) {
        $tourId = (int) $_POST['tourId'];
        $cancelReason = trim($_POST['cancelReason']);
    
        $query = "SELECT u.email, u.username FROM users u JOIN tour t ON u.userid = t.userid WHERE t.tourid = :tourid";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':tourid', $tourId, PDO::PARAM_INT);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if ($tourModel->cancelTour($tourId)) {
            $logQuery = "INSERT INTO logs (action, datetime, userid) VALUES (:action, NOW(), :userid)";
            $logStmt = $conn->prepare($logQuery);
            $action = "Cancelled Tour ID $tourId";
            $logStmt->bindParam(':action', $action);
            $logStmt->bindParam(':userid', $userId, PDO::PARAM_INT);
            $logStmt->execute();
    
            if ($user) {
                sendCancellationEmail($user['email'], $user['username'], $cancelReason);
            }
    
            $response = ["status" => "success", "message" => "Tour successfully cancelled."];
=======

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
>>>>>>> 84496e9cbdaa0e8c884e648f46cf165cb8e7ce98
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

<<<<<<< HEAD
function sendEmailNotification($email, $username, $date, $companions) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'kyleashleighbaldoza.tomcat@gmail.com';
        $mail->Password   = 'ikkt npxt cghd dhbj';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('kyleashleighbaldoza.tomcat@gmail.com', 'Taal Tourist Site');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Your Tour Has Been Updated!';
        $mail->Body    = "<html><head><style>
                    body {
                        background-color: #FFFFFF;
                        margin: 0;
                        padding: 0;
                        font-family: 'Helvetica', Arial, sans-serif !important;
=======
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
>>>>>>> 84496e9cbdaa0e8c884e648f46cf165cb8e7ce98
                    }
                    .email-container {
                        max-width: 600px;
                        margin: 20px auto;
<<<<<<< HEAD
                        background-color: #FFFFFF;
=======
                        background-color: #ffffff;
>>>>>>> 84496e9cbdaa0e8c884e648f46cf165cb8e7ce98
                        border-radius: 10px;
                        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                        overflow: hidden;
                    }
                    .header {
<<<<<<< HEAD
                        background-color: #EC6350;
                        color: #FFFFFF;
                        text-align: center;
                        padding: 20px;
                        font-family: 'Helvetica', Arial, sans-serif !important;
=======
                        background-color: #102E47;
                        color: #ffffff;
                        text-align: center;
                        padding: 20px;
>>>>>>> 84496e9cbdaa0e8c884e648f46cf165cb8e7ce98
                    }
                    .header h1 {
                        margin: 0;
                    }
                    .content {
                        padding: 20px;
                        color: #434343;
                        text-align: justify;
<<<<<<< HEAD
                        font-family: 'Helvetica', Arial, sans-serif !important;
=======
>>>>>>> 84496e9cbdaa0e8c884e648f46cf165cb8e7ce98
                    }
                    .content h2 {
                        color: #102E47;
                        text-align: center;
                    }
<<<<<<< HEAD
=======
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
>>>>>>> 84496e9cbdaa0e8c884e648f46cf165cb8e7ce98
                    .footer {
                        background-color: #E7EBEE;
                        text-align: center;
                        padding: 10px;
                        font-size: 12px;
                        color: #434343;
<<<<<<< HEAD
                        font-family: 'Helvetica', Arial, sans-serif !important;
                    }
                </style></head><body>
                <div class='email-container'>
                    <div class='header'>
                        <h1>Tour Update Notification</h1>
                    </div>
                    <div class='content'>
                        <h2>Hi $username,</h2>
                        <p>Your tour has been updated!</p>
                        <p><strong>New Date:</strong> $date</p>
                        <p><strong>Number of Companions:</strong> $companions</p>
=======
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
>>>>>>> 84496e9cbdaa0e8c884e648f46cf165cb8e7ce98
                        <p>If there are any concerns, please let us know by contacting us through our Facebook Page or (number).</p>
                    </div>
                    <div class='footer'>
                        <p>&copy; " . date("Y") . " Taal Tourism. All rights reserved.</p>
                    </div>
<<<<<<< HEAD
                </div></body></html>";
        $mail->send();
    } catch (Exception $e) {
        error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
    }
}

function sendCancellationEmail($email, $username, $cancelReason) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'kyleashleighbaldoza.tomcat@gmail.com';
        $mail->Password   = 'ikkt npxt cghd dhbj';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('kyleashleighbaldoza.tomcat@gmail.com', 'Taal Tourism');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Your Tour Has Been Cancelled!';
        $mail->Body    = "<html><head><style>
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
                </style></head><body>
                <div class='email-container'>
                    <div class='header'>
                        <h1>Tour Cancellation Notification</h1>
                    </div>
                    <div class='content'>
                        <h2>Hi $username,</h2>
                        <p>We regret to inform you that your tour has been cancelled.</p>
                        <p><strong>Reason:</strong> $cancelReason</p>
                        <p>We apologize for the inconvenience. If you have any concerns, please contact us through our Facebook Page or (number).</p>
                    </div>
                    <div class='footer'>
                        <p>&copy; " . date("Y") . " Taal Tourism. All rights reserved.</p>
                    </div>
                </div></body></html>";

        $mail->send();
    } catch (Exception $e) {
        error_log("Cancellation email could not be sent. Mailer Error: {$mail->ErrorInfo}");
    }
}
?>
=======
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
>>>>>>> 84496e9cbdaa0e8c884e648f46cf165cb8e7ce98
