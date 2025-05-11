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
    $database = new Database();
    $this->conn = $database->getConnection();
    }



    public function createAccount($name, $username, $password, $contactnum, $email) {
        try {
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
            $tokenExpiry = date('Y-m-d\TH:i:s.0000000', strtotime('+24 hours'));
            
            try {
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
            } catch (Exception $e) {
                echo json_encode([
                    'status' => 'error',
                    'message' => $e->getMessage()
                ]);
                return;
            }
            
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => 'An unexpected error occurred: ' . $e->getMessage()
            ]);
        }
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

    if (!preg_match('/^[a-zA-Z]+ [a-zA-Z ]+$/', $name)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Full name must contain at least first and last name, and only alphabetic characters.'
        ]);
        return;
    }

    if (strlen($username) > 20) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Username cannot exceed 20 characters.'
        ]);
        return;
    }

    if (!preg_match('/^[a-zA-Z]/', $username)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Username must start with an alphabetic character.'
        ]);
        return;
    }

    if (!preg_match('/^[a-zA-Z0-9]+$/', $username)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Username can only contain letters and numbers.'
        ]);
        return;
    }

    $signupController = new SignupController();
    $signupController->createAccount($name, $username, $password, $contactnum, $email);
}