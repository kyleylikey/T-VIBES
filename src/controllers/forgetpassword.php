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
        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Invalid email format.'
            ]);
            exit();
        }

        // Check if email exists and is verified
        $query = "SELECT * FROM users WHERE email = :email AND emailveriftoken IS NULL";
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
        $token = bin2hex(random_bytes(32)); // Secure token

        // Store token with expiration (1 hour)
        $query = "UPDATE users SET emailveriftoken = :token, token_expiry = DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE email = :email";
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

        $resetLink = "http://localhost/T-VIBES/src/controllers/resetpw.php?email=" . urlencode($email) . "&token=$token";

        // Send email using PHPMailer
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->SMTPAuth   = true;
            $mail->Host       = 'smtp.gmail.com';
            $mail->Username   = 'kyleashleighbaldoza.tomcat@gmail.com';
            $mail->Password   = 'otlg tqtz gpwv kqjn';
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;

            $mail->setFrom('kyleashleighbaldoza.tomcat@gmail.com', 'Taal Tourist Site');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Request';
            $mail->Body = "
                <html>
                <head>
                    <title>Reset Your Password</title>
                    <style>
                        body { font-family: Arial, sans-serif; background-color: #f4f4f4; }
                        .email-container { max-width: 600px; margin: 20px auto; background-color: #ffffff; padding: 20px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); }
                        .header { background-color: #3a4989; color: #ffffff; text-align: center; padding: 20px; }
                        .button { display: block; width: fit-content; margin: 20px auto; padding: 10px 20px; background-color: #3a4989; color: #ffffff; text-decoration: none; border-radius: 5px; font-size: 16px; text-align: center; font-weight: bold; }
                        .button:hover { background-color: #2f3c6d; }
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