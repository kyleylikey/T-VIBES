<?php
require_once '../config/dbconnect.php';
require_once '../models/User.php';
require_once '../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class ForgotPasswordController {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();

        if (!$this->conn) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Database connection failed.'
            ]);
            exit();
        }
    }

    public function sendResetLink($email) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Invalid email format.'
            ]);
            exit();
        }

        $query = "SELECT * FROM [taaltourismdb].[users]  WHERE email = :email AND emailveriftoken IS NULL";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() === 0) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Email not found or not verified.'
            ]);
            exit();
        }

        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        $token = bin2hex(random_bytes(32)); 

        $query = "UPDATE [taaltourismdb].[users] SET emailveriftoken = :token, token_expiry = DATE_ADD(GETDATE(), INTERVAL 1 HOUR) WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':token', $token);
        $stmt->bindParam(':email', $email);

        if (!$stmt->execute()) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Failed to store reset token.'
            ]);
            exit();
        }

        $resetLink = "https://tourtaal.azurewebsites.net/src/controllers/resetpw.php?email=" . urlencode($email) . "&token=$token";

        // Send email using PHPMailer
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->SMTPAuth   = true;
            $mail->Host       = 'smtp.gmail.com';
            $mail->Username   = 'kyleashleighbaldoza.tomcat@gmail.com';
            $mail->Password   = 'ikkt npxt cghd dhbj';
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;

            $mail->setFrom('kyleashleighbaldoza.tomcat@gmail.com', 'Taal Tourism Office');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Request';
            $mail->Body = "
                <!DOCTYPE html>
                <html lang='en'>
                <head>
                    <meta charset='UTF-8'>
                    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                    <title>Reset Your Password - Taal Tourism Office</title>
                    <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css'>
                    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css' rel='stylesheet' integrity='sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH' crossorigin='anonymous'>
                    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
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
                            <h1>Reset Your Password</h1>
                        </div>
                        <div class='content'>
                            <p>Hello, {$user['name']}.</p>
                            <p>Please click the button below to reset your password.</p>
                            <a href='$resetLink' class='button'>Reset Password</a>
                            <p>If you did not request this, please ignore this email.</p>
                        </div>
                        <div class='footer'>
                            <p>&copy; " . date("Y") . " Taal Tourism Office. All rights reserved.</p>
                        </div>
                    </div>
                </body>
                </html>";

            $mail->send();
            echo json_encode([
                'status' => 'success',
                'message' => 'Password reset link sent. Please check your email.'
            ]);
            exit();

        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Failed to send reset email: ' . $mail->ErrorInfo
            ]);
            exit();
        }
    }
}

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';

    if (empty($email)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Email is required.'
        ]);
        exit();
    }

    $forgotPasswordController = new ForgotPasswordController();
    $forgotPasswordController->sendResetLink($email);
}
?>