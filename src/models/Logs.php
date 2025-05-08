<?php

class Logs {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function getAllLogs($limit, $offset, $searchTerm = '') {
        if (!empty($searchTerm)) {
            $query = "SELECT logs.action, logs.datetime, users.name 
            FROM [taaltourismdb].[logs] 
            INNER JOIN [taaltourismdb].[users] ON logs.userid = users.userid 
            WHERE logs.action LIKE :searchTerm OR users.name LIKE :searchTerm 
            ORDER BY logs.datetime DESC 
            OFFSET :offset ROWS FETCH NEXT :limit ROWS ONLY";
            
            $searchParam = "%{$searchTerm}%";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':searchTerm', $searchParam, PDO::PARAM_STR);
        } else {
            $query = "SELECT logs.action, logs.datetime, users.name 
                     FROM [taaltourismdb].[logs] 
                     INNER JOIN [taaltourismdb].[users] ON logs.userid = users.userid 
                     ORDER BY logs.datetime DESC 
                     LIMIT :limit OFFSET :offset";
            
            $stmt = $this->conn->prepare($query);
        }
        
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getTotalLogsCount($searchTerm = '') {
        if (!empty($searchTerm)) {
            $query = "SELECT COUNT(*) as total 
                     FROM [taaltourismdb].[logs] 
                     INNER JOIN [taaltourismdb].[users] ON logs.userid = users.userid 
                     WHERE logs.action LIKE :searchTerm OR users.name LIKE :searchTerm";
            
            $searchParam = "%{$searchTerm}%";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':searchTerm', $searchParam, PDO::PARAM_STR);
        } else {
            $query = "SELECT COUNT(*) as total FROM [taaltourismdb].[logs]";
            $stmt = $this->conn->prepare($query);
        }
        
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function logAction($userid, $action) {
        $query = "INSERT INTO [taaltourismdb].[logs] (userid, action, datetime) VALUES (:userid, :action, NOW())";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':userid', $userid, PDO::PARAM_INT);
        $stmt->bindParam(':action', $action, PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function logLogin($userid) {
        $this->logAction($userid, 'Logged In');
    }

    public function logLogout($userid) {
        $this->logAction($userid, 'Logged Out');
    }

    public function logAcceptTourRequest($userid, $tourid) {
        $this->logAction($userid, "Accepted Tour Request (Tour ID: $tourid)");
    }

    public function logDeclineTourRequest($userid, $tourid) {
        $this->logAction($userid, "Declined Tour Request (Tour ID: $tourid)");
    }

    public function logEditTour($userid, $tourid) {
        $this->logAction($userid, "Edited Tour (Tour ID: $tourid)");
    }

    public function logCancelTour($userid, $tourid) {
        $this->logAction($userid, "Cancelled Tour (Tour ID: $tourid)");
    }

    public function logDisplayReview($userid, $reviewid) {
        $this->logAction($userid, "Displayed Review (Review ID: $reviewid)");
    }

    public function logArchiveReview($userid, $reviewid) {
        $this->logAction($userid, "Archived Review (Review ID: $reviewid)");
    }

    public function logAddSite($userid, $sitename) {
        $this->logAction($userid, "Added Site (Site Name: $sitename)");
    }

    public function logEditSite($userid, $sitename) {
        $this->logAction($userid, "Edited Site (Site Name: $sitename)");
    }
}

?>