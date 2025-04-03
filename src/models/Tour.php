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

    public function doesTourRequestExist($userid) {
        $query = "SELECT COUNT(*) as count FROM " . $this->table . " 
                  WHERE userid = :userid AND status = 'request'";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':userid', $userid, PDO::PARAM_INT);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }

    public function getExistingTourRequestId($userid) {
        $query = "SELECT tourid FROM " . $this->table . " 
                  WHERE userid = :userid AND status = 'request'
                  LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':userid', $userid, PDO::PARAM_INT);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['tourid'] : null;
    }

    public function addToExistingTour($tourid, $siteid, $userid) {
        $query = "INSERT INTO tour (tourid, siteid, userid, status) VALUES (:tourid, :siteid, :userid, 'request')";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':tourid', $tourid, PDO::PARAM_INT);
        $stmt->bindParam(':siteid', $siteid, PDO::PARAM_INT);
        $stmt->bindParam(':userid', $userid, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function addToNewTour($siteid, $userid) {
        $query = "INSERT INTO tour (siteid, userid, status) VALUES (:siteid, :userid, 'request')";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':siteid', $siteid, PDO::PARAM_INT);
        $stmt->bindParam(':userid', $userid, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function getTourRequestList() {
        $query = "SELECT t.*, u.name, COUNT(*) AS total_sites 
                  FROM " . $this->table . " t 
                  JOIN Users u ON t.userid = u.userid 
                  WHERE t.status = 'submitted' 
                  GROUP BY tourid, userid 
                  ORDER BY t.created_at DESC";  
    
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

    public function getTourRequestByUser($userid) {
        $query = "SELECT t.*, u.name, u.email, s.sitename, s.price, s.siteimage, s.opdays 
                FROM " . $this->table . " t 
                JOIN Users u ON t.userid = u.userid
                JOIN sites s ON t.siteid = s.siteid
                WHERE t.userid = ? AND t.status = 'request'";
                
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $userid);
        $stmt->execute();
        
        return $stmt;
    }

    public function getTourRequestAvailability($userid) {
       $query = "SELECT 
            t.*, 
            u.name, 
            u.email, 
            s.sitename, 
            s.price, 
            s.siteimage, 
            s.opdays,
            BIN(BIT_AND(s.opdays) & 127) AS all_opdays_and_binary
        FROM 
            tour t 
        JOIN 
            Users u ON t.userid = u.userid
        JOIN 
            sites s ON t.siteid = s.siteid
        WHERE 
            t.userid = ?
            AND t.status = 'request'
        GROUP BY
            t.userid;";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $userid);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
        }

    public function getUpcomingTour($tourid, $userid) {
        $query = "SELECT t.*, u.name, u.email, u.username 
                  FROM " . $this->table . " t 
                  JOIN Users u ON t.userid = u.userid
                  WHERE t.tourid = ? AND t.userid = ? AND t.status = 'accepted'";
    
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $tourid);
        $stmt->bindParam(2, $userid);
        $stmt->execute();
        
        return $stmt;
    }

    public function getPendingTourByUser($userid) {
        $query = "SELECT t.*, u.name, COUNT(*) AS total_sites 
                  FROM " . $this->table . " t 
                  JOIN Users u ON t.userid = u.userid 
                  WHERE t.userid = ? AND t.status = 'submitted' 
                  GROUP BY tourid, userid 
                  ORDER BY t.created_at DESC";
                
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $userid);
        $stmt->execute();
        
        return $stmt;
    }

    public function getPendingTourSitesByUser($tourid, $userid) {
        $query = "SELECT s.* FROM tour t JOIN sites s ON t.siteid = s.siteid WHERE t.tourid = ? and t.userid = ? and t.status = 'submitted'";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $tourid);
        $stmt->bindParam(2, $userid);
        $stmt->execute();
        return $stmt;
    }

    public function getApprovedTourByUser($userid) {
        $query = "SELECT t.*, u.name, COUNT(*) AS total_sites 
                  FROM " . $this->table . " t 
                  JOIN Users u ON t.userid = u.userid 
                  WHERE t.userid = ? AND t.status = 'accepted' AND t.date >= CURDATE() 
                  GROUP BY tourid, userid 
                  ORDER BY t.created_at DESC";
                
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $userid);
        $stmt->execute();
        
        return $stmt;
    }

    public function getApprovedTourSitesByUser($tourid, $userid) {
        $query = "SELECT s.* FROM tour t JOIN sites s ON t.siteid = s.siteid WHERE t.date >= CURDATE() and t.tourid = ? and t.userid = ? and t.status = 'accepted'";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $tourid);
        $stmt->bindParam(2, $userid);
        $stmt->execute();
        return $stmt;
    }

    public function getTourHistoryByUser($userid) {
        $query = "SELECT t.*, u.name, COUNT(*) AS total_sites 
                  FROM " . $this->table . " t 
                  JOIN Users u ON t.userid = u.userid 
                  WHERE t.userid = ? AND t.status = 'accepted' AND t.date < CURDATE() 
                  GROUP BY tourid, userid 
                  ORDER BY t.created_at DESC";
                
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $userid);
        $stmt->execute();
        
        return $stmt;
    }

    public function getTourHistorySitesByUser($tourid, $userid) {
        $query = "SELECT s.* FROM tour t JOIN sites s ON t.siteid = s.siteid WHERE t.date < CURDATE() and t.tourid = ? and t.userid = ? and t.status = 'accepted'";
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

    public function acceptTourRequest($tourid, $userid, $empid) {
        $query = "UPDATE " . $this->table . " SET status = 'accepted' WHERE tourid = ? AND userid = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $tourid);
        $stmt->bindParam(2, $userid);
        $result = $stmt->execute();
    
        if ($result) {
            $logs = new Logs();
            $logs->logAcceptTourRequest($empid, $tourid);
        }
    
        return $result;
    }    

    public function declineTourRequest($tourid, $userid, $empid) {
        $query = "UPDATE " . $this->table . " SET status = 'cancelled' WHERE tourid = ? AND userid = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $tourid);
        $stmt->bindParam(2, $userid);
        $result = $stmt->execute();

        if ($result) {
            $logs = new Logs();
            $logs->logDeclineTourRequest($empid, $tourid);
        }

        return $result;
    }

    public function getToursForToday() {
        $today = date('Y-m-d');
        $query = "SELECT t.tourid, u.userid, u.name, t.date, t.companions, t.created_at,
                GROUP_CONCAT(s.sitename ORDER BY s.siteid SEPARATOR '||') as sites
                FROM tour t
                JOIN users u ON t.userid = u.userid
                JOIN sites s ON t.siteid = s.siteid
                WHERE DATE(t.date) = :today AND t.status = 'accepted'
                GROUP BY t.tourid, u.name, t.date, t.companions, t.created_at";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':today', $today);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllUpcomingTours() {
        $query = "SELECT t.tourid, u.userid, u.name, t.date, t.companions, t.created_at,
                GROUP_CONCAT(s.sitename ORDER BY s.siteid SEPARATOR '||') as sites
                FROM tour t
                JOIN users u ON t.userid = u.userid
                JOIN sites s ON t.siteid = s.siteid
                WHERE t.status = 'accepted' AND DATE(t.date) >= CURDATE()
                GROUP BY t.tourid, u.name, t.date, t.companions, t.created_at
                ORDER BY t.date ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateTour($tourId, $date, $companions, $userId) {
        $query = "UPDATE tour SET date = :date, companions = :companions WHERE tourid = :tourid";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':companions', $companions, PDO::PARAM_INT);
        $stmt->bindParam(':tourid', $tourId, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            $logs = new Logs();
            $logs->logEditTour($userId, $tourId);
    
            return true;
        }
    
        return false;
    }    

    public function cancelTour($tourId) {
        $query = "UPDATE tour SET status = 'cancelled' WHERE tourid = :tourid";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':tourid', $tourId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function getPendingToursCount() {
        $query = "SELECT COUNT(DISTINCT tourid) AS pending_count FROM tour WHERE status = 'submitted'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['pending_count'];
    }    

    public function getUpcomingToursCount() {
        $query = "SELECT COUNT(DISTINCT CONCAT(userid, '_', date)) AS upcoming_count 
                  FROM tour 
                  WHERE status = 'accepted' AND date >= CURDATE()";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['upcoming_count'];
    }            

    public function getLatestTourRequests() {
        $query = "SELECT users.name, tour.date AS travel_date, COUNT(tour.siteid) AS destinations, 
                         tour.companions, tour.created_at 
                  FROM tour 
                  JOIN users ON tour.userid = users.userid 
                  WHERE tour.status = 'submitted'  
                  GROUP BY tour.tourid 
                  ORDER BY tour.created_at DESC 
                  LIMIT 6";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
       
    public function getTourHistory() {
        $query = "SELECT users.name, tour.tourid, tour.date AS travel_date, tour.companions, tour.status, 
                         tour.created_at AS submitted_on, sites.sitename, sites.price, sites.siteimage 
                  FROM tour
                  JOIN users ON tour.userid = users.userid
                  JOIN sites ON tour.siteid = sites.siteid
                  ORDER BY tour.date DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>