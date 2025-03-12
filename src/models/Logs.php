<?php

class Logs {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function getAllLogs() {
        $query = "SELECT logs.action, logs.datetime, users.name 
                  FROM logs 
                  INNER JOIN users ON logs.userid = users.userid 
                  ORDER BY logs.datetime DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

?>