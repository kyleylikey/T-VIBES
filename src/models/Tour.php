<?php
class Tour {
    private $conn;
    private $table = 'tour';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getTourList() {
        $query = "SELECT * FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function getTourRequestList() {
        $query = "SELECT t.*, u.name, COUNT(*) AS total_sites FROM " . $this->table . " t JOIN Users u on t.userid = u.userid WHERE t.status = 'submitted' GROUP BY tourid, userid";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function getTourRequest($tourid, $userid) {
        $query = "SELECT t.*, u.name, u.email 
                  FROM " . $this->table . " t 
                  JOIN Users u ON t.userid = u.userid
                  WHERE t.tourid = ? AND t.userid = ? AND t.status = 'submitted'";
    
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $tourid);
        $stmt->bindParam(2, $userid);
        $stmt->execute();
        
        return $stmt;
    }
    public function getTourRequestSites($tourid) {
        $query = "SELECT s.* FROM tour t JOIN sites s ON t.siteid = s.siteid WHERE t.tourid = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $tourid);
        $stmt->execute();
        return $stmt;
    }
    public function acceptTourRequest($tourid, $userid) {
        $query = "UPDATE " . $this->table . " SET status = 'accepted' WHERE tourid = ? AND userid = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $tourid);
        $stmt->bindParam(2, $userid);
        $result = $stmt->execute();
        return $result;
    }
    public function declineTourRequest($tourid, $userid) {
        $query = "UPDATE " . $this->table . " SET status = 'cancelled' WHERE tourid = ? AND userid = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $tourid);
        $stmt->bindParam(2, $userid);
        $result = $stmt->execute();
        return $result;
    }
}


