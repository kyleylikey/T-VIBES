<?php
require_once '../config/dbconnect.php';
require_once '../models/User.php';

class SignupController {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function createAccount($name, $username, $password, $contactnum, $email) {
        $query = "SELECT * FROM users WHERE email = :email OR username = :username";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Email or username already exists.'
            ]);
            exit();
        }        

        $user = new User($this->conn);
        $user->setUsername($username);
        $user->setPassword($password);

        $hashedPassword = $user->getPassword();

        $query = "INSERT INTO users (name, username, hashedpassword, contactnum, email, usertype, status) 
                  VALUES (:name, :username, :hashedpassword, :contactnum, :email, 'trst', 'active')";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':hashedpassword', $hashedPassword);
        $stmt->bindParam(':contactnum', $contactnum);
        $stmt->bindParam(':email', $email);

        if ($stmt->execute()) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Account created! Please check your email for a verification link.'
            ]);
            exit();
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Failed to create account. Please try again.'
            ]);
            exit();
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
        exit();
    }

    $signupController = new SignupController();
    $signupController->createAccount($name, $username, $password, $contactnum, $email);
}
?>