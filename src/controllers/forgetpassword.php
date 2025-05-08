<?php
require_once '../config/dbconnect.php';
require_once '../models/User.php';

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

        $query = "SELECT * FROM [taaltourismdb].[users] WHERE email = :email AND emailveriftoken IS NULL";
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

        $query = "UPDATE [taaltourismdb].[users] SET emailveriftoken = :token, token_expiry = DATEADD(HOUR, 1, GETDATE()) WHERE email = :email";
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
        
        // Get Azure Communication Services credentials
        $connectionString = getenv('AZURE_EMAIL_SENDER_CONNECTION_STRING');
        $senderEmail = getenv('AZURE_EMAIL_SENDER');
        
        if (empty($connectionString) || empty($senderEmail)) {
            error_log("Email configuration missing");
            echo json_encode([
                'status' => 'error',
                'message' => 'Email service configuration error.'
            ]);
            exit();
        }
        
        // Extract credentials from connection string
        preg_match('/endpoint=(.*?);accesskey=(.*?)($|;)/', $connectionString, $matches);
        if (count($matches) < 3) {
            error_log("Invalid connection string format");
            echo json_encode([
                'status' => 'error',
                'message' => 'Email service configuration error.'
            ]);
            exit();
        }
        
        $endpoint = rtrim($matches[1], '/');
        $accessKey = $matches[2];
        
        $httpVerb = "POST";
        $timestamp = gmdate('D, d M Y H:i:s T', time()); // RFC1123 format
        $host = parse_url($endpoint, PHP_URL_HOST);
        $uriPathAndQuery = "/emails:send?api-version=2023-03-31";
        
        // Create email payload
        $payload = [
            "senderAddress" => $senderEmail,
            "content" => [
                "subject" => "Reset Your Password - Taal Tourism Office",
                "plainText" => "Click this link to reset your password: {$resetLink}",
                "html" => "<!DOCTYPE html>
                <html lang='en'>
                <head>
                    <meta charset='UTF-8'>
                    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                    <title>Reset Your Password - Taal Tourism Office</title>
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
                            <h2>Hello, {$user['name']}</h2>
                            <p>We received a request to reset your password for your Taal Tourism account.</p>
                            <p>Please click the button below to reset your password.</p>
                            <a href='$resetLink' class='button'>Reset Password</a>
                            <p>If you did not request this, please ignore this email.</p>
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
        error_log("Making password reset request to: " . $url);
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payloadJson);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_FAILONERROR, false);
        
        $response = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        error_log("Password reset email response - Status: $statusCode, Response: " . $response);
        
        if (curl_errno($ch)) {
            error_log("cURL error in password reset: " . curl_error($ch));
            echo json_encode([
                'status' => 'error',
                'message' => 'Failed to send reset email. Please try again later.'
            ]);
        } else if ($statusCode >= 200 && $statusCode < 300) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Password reset link sent. Please check your email.'
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Failed to send reset email. Please try again later.'
            ]);
        }
        
        curl_close($ch);
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