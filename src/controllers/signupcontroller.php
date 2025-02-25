<?php
require_once '../config/dbconnect.php';
require_once '../models/User.php';
include 'emailverification.php';

ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', '/T-VIBES/temp/error.log');
error_reporting(E_ALL);

class SignupController {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function createAccount($name, $username, $password, $contactnum, $email) {
        $user = new User($this->conn);

        if ($user->doesUserExist($email, $username)) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Email or username already exists.'
            ]);
            return;
        }

        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $verificationToken = bin2hex(random_bytes(32)); 
        $tokenExpiry = date('Y-m-d H:i:s', strtotime('+24 hours')); 

        if ($user->createUser($name, $username, $hashedPassword, $contactnum, $email, $verificationToken, $tokenExpiry)) {
            if (sendconfirmationEmail($username, $email, $verificationToken)) {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Please check your email for a verification link.'
                ]);
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Account created, but failed to send the verification email. Please contact support.'
                ]);
            }
            return;
        }

        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to create account. Please try again later.'
        ]);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    $name = $_POST['fullname'] ?? '';
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $contactnum = $_POST['contact'] ?? '';
    $email = $_POST['email'] ?? '';

    if (empty($name) || empty($username) || empty($password) || empty($contactnum) || empty($email)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'All fields are required.'
        ]);
        return;
    }

    $signupController = new SignupController();
    $signupController->createAccount($name, $username, $password, $contactnum, $email);
}