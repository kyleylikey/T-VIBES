<?php
class User {
    private $conn;
    private $table = 'Users';

    private $id;
    private $username;
    private $password;

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

    public function getPassword() {
        return $this->password;
    }

    public function setPassword($password) {
        $options = [
            'cost' => 10,
        ];
        $this->password = password_hash($password, PASSWORD_BCRYPT, $options);
    }

    public function login($plainPassword) {
        $query = "SELECT * FROM " . $this->table . " WHERE username = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->username);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            if (password_verify($plainPassword, $user['hashedpassword'])) {
                $this->id = $user['userid'];
                return true;
            } else {
                echo "Password verification failed.";
            }
        } else {
            echo "User not found.";
        }
        return false;
    }

    public function isActive() {
        $query = "SELECT status FROM " . $this->table . " WHERE username = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->username);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && $user['status'] === 'active') {
            return true;
        }
        return false;
    }
}
?>