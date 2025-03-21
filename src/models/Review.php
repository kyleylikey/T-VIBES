<?php
class Review {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getPendingReviewsCount() {
        $query = "SELECT COUNT(*) AS pending_reviews FROM rev WHERE status = 'submitted'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['pending_reviews'];
    }

    public function getRecentReviews() {
        $query = "SELECT users.name AS author, sites.rating, rev.date, rev.review, sites.sitename FROM rev 
                  JOIN users ON rev.userid = users.userid 
                  JOIN sites ON rev.siteid = sites.siteid 
                  WHERE rev.status = 'displayed' 
                  ORDER BY rev.date DESC 
                  LIMIT 6";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSiteReviews($siteid) {
        $query = "SELECT users.name AS author, rev.date, rev.review, sites.sitename FROM rev 
                  JOIN users ON rev.userid = users.userid 
                  JOIN sites ON rev.siteid = sites.siteid 
                  WHERE rev.status = 'displayed' AND rev.siteid = :siteid
                  ORDER BY rev.date DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':siteid', $siteid, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>