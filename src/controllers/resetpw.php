<?php
require_once '../config/dbconnect.php';

// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_GET['email']) || !isset($_GET['token'])) {
    die("Invalid request.");
}

$email = urldecode($_GET['email']);
$token = $_GET['token'];

$database = new Database();
$conn = $database->getConnection();

if (!$conn) {
    die("Database connection failed.");
}

// Validate token
$query = "SELECT * FROM [taaltourismdb].[users] WHERE email = :email AND emailveriftoken = :token AND token_expiry > GETDATE()";
$stmt = $conn->prepare($query);
$stmt->bindParam(':email', $email);
$stmt->bindParam(':token', $token);
$stmt->execute();

if ($stmt->rowCount() > 0) {
    // Token is valid, set session variable and include the reset password form
    $_SESSION['valid_reset'] = true;
    include '../views/frontend/resetpw.php';
} else {
    echo "<script>alert('Invalid or expired token. Please request a new password reset.'); window.location.href = 'https://tourtaal.azurewebsites.net/src/views/frontend/login.php';</script>";
}
?>