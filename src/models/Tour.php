<?php
class Tour {
    private $conn;
    private $table = '[taaltourismdb].[tour]';

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
        $query = "SELECT TOP 1 tourid FROM " . $this->table . " 
                  WHERE userid = :userid AND status = 'request'";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':userid', $userid, PDO::PARAM_INT);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['tourid'] : null;
    }

    public function addToExistingTour($tourid, $siteid, $userid) {
        $query = "INSERT INTO [taaltourismdb].[tour] (tourid, siteid, userid, status) VALUES (:tourid, :siteid, :userid, 'request')";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':tourid', $tourid, PDO::PARAM_INT);
        $stmt->bindParam(':siteid', $siteid, PDO::PARAM_INT);
        $stmt->bindParam(':userid', $userid, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function getTourRequestList() {
        $query = "SELECT 
                    t.*, 
                    u.name, 
                    COUNT(*) OVER (PARTITION BY t.tourid, t.userid) AS total_sites
                FROM 
                    [taaltourismdb].[tour] t
                JOIN 
                    [taaltourismdb].[users] u ON t.userid = u.userid
                WHERE 
                    t.status = 'submitted'
                ORDER BY 
                    t.created_at DESC;
                ";  
    
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }    

    public function getTourRequest($tourid, $userid) {
        $query = "SELECT t.*, u.name, u.email 
                  FROM " . $this->table . " t 
                  JOIN [taaltourismdb].[users] u ON t.userid = u.userid
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
                JOIN [taaltourismdb].[users] u ON t.userid = u.userid
                JOIN [taaltourismdb].[sites] s ON t.siteid = s.siteid
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
                CONVERT(VARCHAR(10), 
                    (MIN(s.opdays) OVER (PARTITION BY t.userid)) & 127
                ) AS all_opdays_and_binary
            FROM 
                [taaltourismdb].[tour] t 
            JOIN 
                [taaltourismdb].[users] u ON t.userid = u.userid
            JOIN 
                [taaltourismdb].[sites] s ON t.siteid = s.siteid
            WHERE 
                t.userid = ?
                AND t.status = 'request';
            ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $userid);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
        }

    public function getUpcomingTour($tourid, $userid) {
        $query = "SELECT t.*, u.name, u.email, u.username 
                  FROM " . $this->table . " t 
                  JOIN [taaltourismdb].[users] u ON t.userid = u.userid
                  WHERE t.tourid = ? AND t.userid = ? AND t.status = 'accepted'";
    
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $tourid);
        $stmt->bindParam(2, $userid);
        $stmt->execute();
        
        return $stmt;
    }

    public function getPendingTourByUser($userid) {
        $query = "SELECT 
            t.tourid,
            t.userid,
            t.status,
            t.date,
            t.companions,
            t.created_at,
            u.name,
            COUNT(t.siteid) AS total_sites
        FROM 
            [taaltourismdb].[tour] t
        JOIN 
            [taaltourismdb].[users] u ON t.userid = u.userid
        WHERE 
            t.userid = ? 
            AND t.status = 'submitted'
        GROUP BY 
            t.tourid,
            t.userid,
            t.status,
            t.date,
            t.companions,
            t.created_at,
            u.name
        ORDER BY 
            t.created_at DESC;
        ";
                
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $userid);
        $stmt->execute();
        
        return $stmt;
    }

    public function getPendingTourSitesByUser($tourid, $userid) {
        $query = "SELECT s.* FROM [taaltourismdb].[tour] t JOIN [taaltourismdb].[sites] s ON t.siteid = s.siteid WHERE t.tourid = ? and t.userid = ? and t.status = 'submitted'";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $tourid);
        $stmt->bindParam(2, $userid);
        $stmt->execute();
        return $stmt;
    }

    public function getApprovedTourByUser($userid) {
        $query = "SELECT 
            t.*, 
            u.name, 
            COUNT(*) OVER (PARTITION BY t.tourid, t.userid) AS total_sites
        FROM 
            " . $this->table . " t
        JOIN 
            [taaltourismdb].[users] u ON t.userid = u.userid
        WHERE 
            t.userid = ? 
            AND t.status = 'accepted' 
            AND t.date >= CONVERT(DATE, GETDATE())
        ORDER BY 
            t.created_at DESC;
        ";
                
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $userid);
        $stmt->execute();
        
        return $stmt;
    }

    public function getApprovedTourSitesByUser($tourid, $userid) {
        $query = "SELECT s.* FROM [taaltourismdb].[tour] t JOIN [taaltourismdb].[sites] s ON t.siteid = s.siteid WHERE t.date >= CONVERT(DATE, GETDATE()) and t.tourid = ? and t.userid = ? and t.status = 'accepted'";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $tourid);
        $stmt->bindParam(2, $userid);
        $stmt->execute();
        return $stmt;
    }

    public function getTourHistoryByUser($userid) {
        $query = "SELECT 
            t.*, 
            u.name, 
            COUNT(*) OVER (PARTITION BY t.tourid, t.userid) AS total_sites
        FROM 
            " . $this->table . " t
        JOIN 
            [taaltourismdb].[users] u ON t.userid = u.userid
        WHERE 
            t.userid = ? 
            AND t.status = 'accepted' 
            AND t.date < CONVERT(DATE, GETDATE())
        ORDER BY 
            t.created_at DESC;
        ";
                
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $userid);
        $stmt->execute();
        
        return $stmt;
    }

    public function getTourHistorySitesByUser($tourid, $userid) {
        $query = "SELECT s.* FROM [taaltourismdb].[tour] t JOIN [taaltourismdb].[sites] s ON t.siteid = s.siteid WHERE t.date < CONVERT(DATE, GETDATE()) and t.tourid = ? and t.userid = ? and t.status = 'accepted'";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $tourid);
        $stmt->bindParam(2, $userid);
        $stmt->execute();
        return $stmt;
    }


    public function getTourRequestSites($tourid) {
        $query = "SELECT s.* FROM [taaltourismdb].[tour] t JOIN [taaltourismdb].[sites] s ON t.siteid = s.siteid WHERE t.tourid = ?";
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
        $query = "SELECT 
                    t.tourid, 
                    u.userid, 
                    u.name, 
                    t.date, 
                    t.companions, 
                    t.created_at,
                    STRING_AGG(s.sitename, '||') WITHIN GROUP (ORDER BY s.sitename) AS sites
                FROM [taaltourismdb].[tour] t
                JOIN [taaltourismdb].[users] u ON t.userid = u.userid
                JOIN [taaltourismdb].[sites] s ON t.siteid = s.siteid
                WHERE CAST(t.date AS DATE) = ? AND t.status = 'accepted'
                GROUP BY t.tourid, u.userid, u.name, t.date, t.companions, t.created_at";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$today]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllUpcomingTours() {
        $query = "SELECT 
                    t.tourid, 
                    u.userid, 
                    u.name, 
                    t.date, 
                    t.companions, 
                    t.created_at,
                    STRING_AGG(s.sitename, '||') AS sites
                FROM [taaltourismdb].[tour] t
                JOIN [taaltourismdb].[users] u ON t.userid = u.userid
                JOIN [taaltourismdb].[sites] s ON t.siteid = s.siteid
                WHERE t.status = 'accepted' AND CONVERT(DATE, t.date) >= CONVERT(DATE, GETDATE())
                GROUP BY t.tourid, u.name, t.date, t.companions, t.created_at, u.userid
                ORDER BY t.date ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateTour($tourId, $date, $companions, $userId) {
        $query = "UPDATE [taaltourismdb].[tour] SET date = :date, companions = :companions WHERE tourid = :tourid";
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
        $query = "UPDATE [taaltourismdb].[tour] SET status = 'cancelled' WHERE tourid = :tourid";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':tourid', $tourId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function getPendingToursCount() {
        $query = "SELECT COUNT(*) AS pending_count
        FROM (
            SELECT 
                t.*, 
                u.name, 
                COUNT(*) OVER (PARTITION BY t.tourid, t.userid) AS total_sites
            FROM 
                [taaltourismdb].[tour] t
            JOIN 
                [taaltourismdb].[users] u ON t.userid = u.userid
            WHERE 
                t.status = 'submitted'
        ) AS subquery;";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['pending_count'];
    }    

    public function getUpcomingToursCount() {
        $query = "SELECT COUNT(DISTINCT CONCAT(userid, '_', date)) AS upcoming_count 
                  FROM [taaltourismdb].[tour] 
                  WHERE status = 'accepted' AND date >= CONVERT(DATE, GETDATE())";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['upcoming_count'];
    }            

    public function getLatestTourRequests() {
        $query = "SELECT TOP 6 
                    users.name, 
                    tour.date AS travel_date, 
                    COUNT(tour.siteid) AS destinations, 
                    tour.companions, 
                    tour.created_at
                FROM [taaltourismdb].[tour]
                JOIN [taaltourismdb].[users] ON tour.userid = users.userid
                WHERE tour.status = 'submitted'
                GROUP BY users.name, tour.date, tour.companions, tour.created_at
                ORDER BY tour.created_at DESC;
                ";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
       
    public function getTourHistory() {
        $query = "SELECT users.name, tour.tourid, tour.date AS travel_date, tour.companions, tour.status, 
                         tour.created_at AS submitted_on, sites.sitename, sites.price, sites.siteimage 
                  FROM [taaltourismdb].[tour]
                  JOIN [taaltourismdb].[users] ON tour.userid = users.userid
                  JOIN [taaltourismdb].[sites] ON tour.siteid = sites.siteid
                  ORDER BY tour.date DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>