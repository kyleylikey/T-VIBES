<?php
class User {
    private $conn;
    private $table = '[taaltourismdb].[users]';

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
        echo "Checkpoint 5: Running INSERT.<br>";
        $query = "INSERT INTO " . $this->table . " ([name], [username], [hashedpassword], [contactnum], [email], [usertype], [status], [emailveriftoken], [token_expiry])
                  VALUES (:name, :username, :hashedpassword, :contactnum, :email, 'trst', 'inactive', :emailveriftoken, :token_expiry)";
        echo "Query: " . $query . "<br>";
        var_dump($this->conn);
        echo "Final Query: $query<br>";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':hashedpassword', $hashedPassword);
        $stmt->bindParam(':contactnum', $contactnum);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':emailveriftoken', $verificationToken);
        $stmt->bindParam(':token_expiry', $tokenExpiry);
        return $stmt->execute();
        if (!$stmt->execute()) {
            $errorInfo = $stmt->errorInfo();
            echo "SQLSTATE: " . $errorInfo[0] . "<br>";
            echo "Error Code: " . $errorInfo[1] . "<br>";
            echo "Error Message: " . $errorInfo[2] . "<br>";
            return false;
        }
        
    }

    public function editUser($userid, $name, $username, $contactnum, $email) {
        $query = "UPDATE " . $this->table . " SET name = :name, username = :username, contactnum = :contactnum, email = :email WHERE userid = :userid";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':userid', $userid);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':contactnum', $contactnum);
        $stmt->bindParam(':email', $email);
        return $stmt->execute();
    }

    public function login($plainPassword) {
    $query = "SELECT TOP 1 userid, hashedpassword, usertype, status FROM " . $this->table . " WHERE username = ?";
    $stmt = $this->conn->prepare($query);
    $stmt->bindValue(1, $this->username, PDO::PARAM_STR);
    $stmt->execute();
    
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        return 'Invalid username or password.';
    }
    
    $passwordVerified = password_verify($plainPassword, $user['hashedpassword']);
    
    if (!$passwordVerified) {
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

    public function addEmpAccount($name, $username, $email, $contactnum, $plainPassword) {
        $hashedPassword = password_hash($plainPassword, PASSWORD_BCRYPT);
        $query = "INSERT INTO ".$this->table." (name, username, email, contactnum, hashedpassword, usertype, status) VALUES (:name, :username, :email, :contactnum, :hashedpassword, 'emp', 'active')";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':contactnum', $contactnum);
        $stmt->bindParam(':hashedpassword', $hashedPassword);
        return $stmt->execute();
    }

    public function updateEmpAccount($accountid, $updateData) {
        if (empty($updateData)) {
            return false;
        }
        
        $fields = [];
        $params = [];
        foreach ($updateData as $column => $value) {
            $fields[] = "$column = :$column";
            $params[":$column"] = $value;
        }
        // Add the account id for the WHERE clause
        $params[":accountid"] = $accountid;
        
        $sql = "UPDATE " . $this->table . " SET " . implode(", ", $fields) . " WHERE userid = :accountid";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($params);
    }

    public function getUserList() {
        $usertypes = ['mngr' => 'Manager', 'emp' => 'Employee', 'trst' => 'Tourist'];
        $accounts = ['mngr' => [], 'emp' => [], 'trst' => []];
    
        $query = "SELECT userid, name, username, email, contactnum, usertype, status FROM [taaltourismdb].[users] ORDER BY (status = 'active') DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        if ($result) {
            foreach ($result as $row) {
                $usertype = $row['usertype'];
                if (isset($usertypes[$usertype])) {
                    $accounts[$usertype][] = $row;
                }
            }
        }
        return $accounts;
    }

    public function getActiveEmpList() {
        $query = "SELECT userid, name, username, email, contactnum FROM [taaltourismdb].[users] WHERE usertype = 'emp' AND status = 'active'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function disableEmpAcc($userid) {
        $query = "UPDATE " . $this->table . " SET status = 'inactive' WHERE userid = :userid";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':userid', $userid);
        $result = $stmt->execute();
    
        if (!$result) {
            $errorInfo = $stmt->errorInfo();
            error_log("SQL Error: " . print_r($errorInfo, true));
        }
        return $result;
    }

    public function enableEmpAcc($userid) {
        $query = "UPDATE " . $this->table . " SET status = 'active' WHERE userid = :userid";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':userid', $userid);
        $result = $stmt->execute();
    
        if (!$result) {
            $errorInfo = $stmt->errorInfo();
            error_log("SQL Error: " . print_r($errorInfo, true));
        }
        return $result;
    }

    public function deleteTrstAcc($userid) {
        $query = "DELETE FROM " . $this->table . " WHERE userid = :userid";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':userid', $userid);
        $result = $stmt->execute();
    
        if (!$result) {
            $errorInfo = $stmt->errorInfo();
            error_log("SQL Error: " . print_r($errorInfo, true));
        }
        return $result;
    }
}