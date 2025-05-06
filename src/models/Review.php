<?php
class Review {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getPendingReviewsCount() {
        $query = "SELECT COUNT(*) AS pending_reviews FROM [taaltourismdb].[rev] WHERE status = 'submitted'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['pending_reviews'];
    }

    public function getRecentReviews() {
        $query = "SELECT TOP 6 users.name AS author, sites.rating, rev.date, rev.review, sites.sitename FROM [taaltourismdb].[rev] 
                  JOIN [taaltourismdb].[users] ON rev.userid = users.userid 
                  JOIN [taaltourismdb].[sites] ON rev.siteid = sites.siteid 
                  WHERE rev.status = 'displayed' 
                  ORDER BY rev.date DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSiteReviews($siteid) {
        $query = "SELECT users.name AS author, rev.date, rev.review, sites.sitename FROM [taaltourismdb].[rev] 
                  JOIN [taaltourismdb].[users] ON rev.userid = users.userid 
                  JOIN [taaltourismdb].[sites] ON rev.siteid = sites.siteid 
                  WHERE rev.status = 'displayed' AND rev.siteid = :siteid
                  ORDER BY rev.date DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':siteid', $siteid, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function countSiteReviews($siteid) {
        $query = "SELECT COUNT(*) AS review_count FROM [taaltourismdb].[rev] 
                  WHERE status = 'displayed' AND siteid = :siteid";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':siteid', $siteid, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['review_count'];
    }

    public function getRatingDistribution($siteid) {
        $query = "SELECT rating, COUNT(*) as count FROM [taaltourismdb].[user_ratings] 
                  WHERE site_id = :siteid 
                  GROUP BY rating 
                  ORDER BY rating DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':siteid', $siteid, PDO::PARAM_INT);
        $stmt->execute();
        
        $distribution = [
            5 => 0,
            4 => 0,
            3 => 0,
            2 => 0,
            1 => 0
        ];
        
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $total = 0;
        
        foreach ($results as $row) {
            $distribution[$row['rating']] = $row['count'];
            $total += $row['count'];
        }
        
        if ($total > 0) {
            foreach ($distribution as $rating => $count) {
                $distribution[$rating] = round(($count / $total) * 100);
            }
        }
        
        return $distribution;
    }
}
?>