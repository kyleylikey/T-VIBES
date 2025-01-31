<?php
require_once '../config/dbconnect.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['email'], $_POST['token'], $_POST['newPassword'], $_POST['retypeNewPassword'])) {
        die("Invalid request.");
    }

    $email = $_POST['email'];
    $token = $_POST['token'];
    $newPassword = $_POST['newPassword'];
    $retypeNewPassword = $_POST['retypeNewPassword'];

    // Validate new password match
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
    $query = "SELECT * FROM users WHERE email = :email AND emailveriftoken = :token AND token_expiry > NOW()";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':token', $token);
    $stmt->execute();

    if ($stmt->rowCount() === 0) {
        echo "<script>alert('Invalid or expired token. Please request a new password reset.');  window.location.href = '/T-VIBES/src/views/frontend/login.php';</script>";
        exit();
    }

    // Hash new password
    $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

    // Update password and invalidate token
    $updateQuery = "UPDATE users SET hashedpassword = :password, emailveriftoken = NULL, token_expiry = NULL WHERE email = :email";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bindParam(':password', $hashedPassword);
    $updateStmt->bindParam(':email', $email);

    if ($updateStmt->execute()) {
        echo "<script>alert('Password successfully reset. You can now log in.'); window.location.href = '/T-VIBES/src/views/frontend/login.php';</script>";
    } else {
        echo "<script>alert('Password update failed. Please try again.'); window.location.href = 'resetpassword.php?email=" . urlencode($email) . "&token=" . urlencode($token) . "';</script>";
    }
}
?>
