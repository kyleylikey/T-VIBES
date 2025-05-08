<?php
require_once '../config/dbconnect.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['email'], $_POST['token'], $_POST['newPassword'], $_POST['retypeNewPassword'])) {
        die("Invalid request.");
    }

    $email = trim($_POST['email']);
    $token = trim($_POST['token']);
    $newPassword = $_POST['newPassword'];
    $retypeNewPassword = $_POST['retypeNewPassword'];

    // Validate password strength
    if (strlen($newPassword) < 8 || !preg_match('/[A-Z]/', $newPassword) || !preg_match('/[0-9]/', $newPassword)) {
        echo "<script>alert('Password must be at least 8 characters long and include an uppercase letter and a number.'); window.location.href = 'resetpassword.php?email=" . urlencode($email) . "&token=" . urlencode($token) . "';</script>";
        exit();
    }

    // Validate passwords match
    if ($newPassword !== $retypeNewPassword) {
        echo "<script>alert('Passwords do not match.'); window.location.href = 'resetpassword.php?email=" . urlencode($email) . "&token=" . urlencode($token) . "';</script>";
        exit();
    }

    $database = new Database();
    $conn = $database->getConnection();

    if (!$conn) {
        die("Database connection failed.");
    }

    // Verify token and expiry
    $query = "SELECT * FROM [taaltourismdb].[users] WHERE email = CAST(:email AS NVARCHAR(MAX)) AND emailveriftoken = CAST(:token AS NVARCHAR(MAX)) AND token_expiry > GETDATE()";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->bindParam(':token', $token, PDO::PARAM_STR);
    $stmt->execute();

    if ($stmt->rowCount() === 0) {
        echo "<script>alert('Invalid or expired token. Please request a new password reset.'); window.location.href = 'https://tourtaal.azurewebsites.net/src/views/frontend/login.php';</script>";
        exit();
    }

    // Hash new password securely
    $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

    // Update password and invalidate token
    $updateQuery = "UPDATE [taaltourismdb].[users] SET hashedpassword = :password, emailveriftoken = NULL, token_expiry = NULL WHERE email = :email";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
    $updateStmt->bindParam(':email', $email, PDO::PARAM_STR);

    if ($updateStmt->execute()) {
        echo "<script>alert('Password successfully reset. You can now log in.'); window.location.href = 'https://tourtaal.azurewebsites.net/src/views/frontend/login.php';</script>";
    } else {
        echo "<script>alert('Password update failed. Please try again.'); window.location.href = 'resetpassword.php?email=" . urlencode($email) . "&token=" . urlencode($token) . "';</script>";
    }
}
?>