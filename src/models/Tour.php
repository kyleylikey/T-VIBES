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

    public function getToursForToday() {
        $today = date('Y-m-d');
        $query = "SELECT t.tourid, u.name, t.date, t.companions, GROUP_CONCAT(s.sitename SEPARATOR ', ') as sites
                  FROM tour t
                  JOIN users u ON t.userid = u.userid
                  JOIN sites s ON t.siteid = s.siteid
                  WHERE DATE(t.date) = :today AND t.status = 'accepted'
                  GROUP BY t.tourid, u.name, t.date, t.companions";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':today', $today);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllUpcomingTours() {
        $query = "SELECT t.tourid, u.name, t.date, t.companions, GROUP_CONCAT(s.sitename SEPARATOR ', ') as sites
                    FROM tour t
                    JOIN users u ON t.userid = u.userid
                    JOIN sites s ON t.siteid = s.siteid
                    WHERE t.status = 'accepted' AND DATE(t.date) >= CURDATE()
                    GROUP BY t.tourid, u.name, t.date, t.companions
                    ORDER BY t.date ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateTour($tourId, $date, $companions) {
        $query = "UPDATE tour SET date = :date, companions = :companions WHERE tourid = :tourid";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':companions', $companions, PDO::PARAM_INT);
        $stmt->bindParam(':tourid', $tourId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function cancelTour($tourId) {
        $query = "UPDATE tour SET status = 'cancelled' WHERE tourid = :tourid";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':tourid', $tourId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function getPendingToursCount() {
        $query = "SELECT COUNT(*) AS pending_count FROM tour WHERE status = 'request'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['pending_count'];
    }

    public function getUpcomingToursCount() {
        $query = "SELECT COUNT(*) AS upcoming_count FROM tour WHERE status = 'accepted' AND date >= CURDATE()";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['upcoming_count'];
    }

    public function getLatestTourRequests() {
        $query = "SELECT users.name, tour.date AS travel_date, COUNT(tour.siteid) AS destinations, tour.created_at 
                  FROM tour 
                  JOIN users ON tour.userid = users.userid 
                  GROUP BY tour.tourid 
                  ORDER BY tour.created_at DESC 
                  LIMIT 6";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}


