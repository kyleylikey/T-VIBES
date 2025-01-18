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
            return ["success" => false, "message" => "Email or username already exists."];
        }

        $user = new User($this->conn);
        $user->setUsername($username);
        $user->setPassword($password);

        $query = "INSERT INTO users (name, username, hashedpassword, contactnum, email, usertype, status) 
                  VALUES (:name, :username, :hashedpassword, :contactnum, :email, 'trst', 'active')";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':hashedpassword', $user->getPassword());
        $stmt->bindParam(':contactnum', $contactnum);
        $stmt->bindParam(':email', $email);

        if ($stmt->execute()) {
            return ["success" => true, "message" => "Account successfully created."];
        } else {
            return ["success" => false, "message" => "Failed to create account."];
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['fullname'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $contactnum = $_POST['contact'];
    $email = $_POST['email'];

    if (empty($name) || empty($username) || empty($password) || empty($contactnum) || empty($email)) {
        header('Location: ../../src/views/frontend/signup.php?error=empty_fields');
        exit();
    }

    $signupController = new SignupController();
    $result = $signupController->createAccount($name, $username, $password, $contactnum, $email);

    if ($result["success"]) {
        header('Location: ../../src/views/frontend/login.php?message=account_created');
        exit();
    } else {
        header('Location: ../../src/views/frontend/signup.php?error=' . urlencode($result["message"]));
        exit();
    }
}
?>
