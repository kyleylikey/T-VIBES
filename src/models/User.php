<?php
class User {
    private $conn;
    private $table = 'Users';

    private $id;
    private $username;
    private $password;
    private $usertype;
    private $status;
    private $emailveriftoken;
    private $token_expiry;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getId() {
        return $this->id;
    }

    public function getUsername() {
        return $this->username;
    }

    public function setUsername($username) {
        $this->username = $username;
    }

    public function getUsertype() {
        return $this->usertype;
    }

    public function getStatus() {
        return $this->status;
    }

    public function setPassword($password) {
        $options = ['cost' => 10];
        $this->password = password_hash($password, PASSWORD_BCRYPT, $options);
    }

    public function doesUserExist($email, $username) {
        $query = "SELECT * FROM " . $this->table . " WHERE email = :email OR username = :username";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function createUser($name, $username, $hashedPassword, $contactnum, $email, $verificationToken, $tokenExpiry) {
        $query = "INSERT INTO " . $this->table . " (name, username, hashedpassword, contactnum, email, usertype, status, emailveriftoken, token_expiry) 
                  VALUES (:name, :username, :hashedpassword, :contactnum, :email, 'trst', 'inactive', :emailveriftoken, :token_expiry)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':hashedpassword', $hashedPassword);
        $stmt->bindParam(':contactnum', $contactnum);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':emailveriftoken', $verificationToken);
        $stmt->bindParam(':token_expiry', $tokenExpiry);
        return $stmt->execute();
    }

    public function login($plainPassword) {
        $query = "SELECT userid, hashedpassword, usertype, status FROM " . $this->table . " WHERE username = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->username);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || !password_verify($plainPassword, $user['hashedpassword'])) {
            return 'Invalid username or password.';
        }

        $this->id = $user['userid'];
        $this->usertype = $user['usertype'];
        $this->status = $user['status'];

        return true;
    }

    public function isActive() {
        return strtolower($this->status) === 'active';
    }
}