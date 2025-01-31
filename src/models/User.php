<?php
class User {
    private $conn;
    private $table = 'Users';

    private $id;
    private $username;
    private $password;
    private $usertype;
    private $status;

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
        $options = [
            'cost' => 10,
        ];
        $this->password = password_hash($password, PASSWORD_BCRYPT, $options);
    }

    public function login($plainPassword) {
        $query = "SELECT userid, hashedpassword, usertype, status FROM " . $this->table . " WHERE username = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->username);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            return 'Invalid username or password.';
        }

        if (!password_verify($plainPassword, $user['hashedpassword'])) {
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
?>