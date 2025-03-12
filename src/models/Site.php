<?php
class Site {
    private $conn;
    private $table = 'sites';

    private $id;
    private $sitename;
    private $siteimage;
    private $description;
    private $opdays;
    private $rating;
    private $price;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getSiteList() {
        $query = "SELECT * FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function updateSite($siteid, $updateData) {
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
        $params[":siteid"] = $siteid;
        
        $sql = "UPDATE " . $this->table . " SET " . implode(", ", $fields) . " WHERE siteid = :siteid";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($params);
    }

    public function addSite($updateData) {
        if (empty($updateData)) {
            return false;
        }
        
        $fields = [];
        $params = [];
        foreach ($updateData as $column => $value) {
            $fields[] = $column;
            $params[":$column"] = $value;
        }

        $sql = "INSERT INTO " . $this->table . " (" . implode(", ", $fields) . ") VALUES (" . implode(", ", array_keys($params)) . ")";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($params);
    }

    public function getTopSites($currentYear) {
        $query = "SELECT s.siteid, s.sitename, s.siteimage, s.description, s.opdays, s.rating as ratings, s.price, s.status, SUM(t.companions) as visitor_count 
                  FROM sites s 
                  LEFT JOIN tour t ON s.siteid = t.siteid AND t.status = 'accepted'
                  WHERE YEAR(t.date) = :currentYear 
                  GROUP BY s.siteid
                  ORDER BY visitor_count DESC
                  LIMIT 3";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':currentYear', $currentYear, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }
}


