<?php
require_once '../config/dbconnect.php';
require_once '../models/User.php';
include 'emailverification.php';

ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

class SignupController {
    private $conn;

    public function __construct() {
        // ✅ Checkpoint 1: Initialize DB connection
        echo 'Checkpoint: Initializing database connection.<br>';
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function createAccount($name, $username, $password, $contactnum, $email) {
        // ✅ Checkpoint 2: Instantiate User model
        echo 'Checkpoint: Creating User model.<br>';
        $user = new User($this->conn);

        // ✅ Checkpoint 3: Check if user already exists
        echo 'Checkpoint: Checking if user already exists.<br>';
        if ($user->doesUserExist($email, $username)) {
            echo 'Checkpoint: User already exists.<br>';
            echo json_encode([
                'status' => 'error',
                'message' => 'Email or username already exists.'
            ]);
            return;
        }

        // ✅ Checkpoint 4: Hash password and generate token
        echo 'Checkpoint: Hashing password and generating verification token.<br>';
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $verificationToken = bin2hex(random_bytes(32)); 
        $tokenExpiry = date('Y-m-d H:i:s', strtotime('+24 hours')); 

        // ✅ Checkpoint 5: Attempt to create user in database
        echo 'Checkpoint: Attempting to create user in database.<br>';
        if ($user->createUser($name, $username, $hashedPassword, $contactnum, $email, $verificationToken, $tokenExpiry)) {
            echo 'Checkpoint: User created successfully. Sending email.<br>';

            // ✅ Checkpoint 6: Attempt to send confirmation email
            if (sendconfirmationEmail($username, $email, $verificationToken)) {
                echo 'Checkpoint: Confirmation email sent.<br>';
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Please check your email for a verification link.'
                ]);
            } else {
                echo 'Checkpoint: Failed to send confirmation email.<br>';
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Account created, but failed to send the verification email. Please contact support.'
                ]);
            }
            return;
        }

        // ✅ Checkpoint 7: Failed to create user
        echo 'Checkpoint: Failed to create user.<br>';
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to create account. Please try again later.'
        ]);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    // ✅ Checkpoint 8: Validate POST inputs
    echo 'Checkpoint: Validating POST data.<br>';
    $name = $_POST['fullname'] ?? '';
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $contactnum = $_POST['contact'] ?? '';
    $email = $_POST['email'] ?? '';

    if (empty($name) || empty($username) || empty($password) || empty($contactnum) || empty($email)) {
        echo 'Checkpoint: Validation failed - missing fields.<br>';
        echo json_encode([
            'status' => 'error',
            'message' => 'All fields are required.'
        ]);
        return;
    }

    // ✅ Checkpoint 9: Start account creation
    echo 'Checkpoint: Starting account creation.<br>';
    $signupController = new SignupController();
    $signupController->createAccount($name, $username, $password, $contactnum, $email);
}
