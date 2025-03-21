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

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function addSite($siteName, $sitePrice, $siteDescription, $opdays, $siteImage) {
        $query = "INSERT INTO sites (sitename, siteimage, description, opdays, price, status, rating, rating_cnt) 
                    VALUES (:sitename, :siteimage, :description, :opdays, :price, 'displayed', 0, 0)";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([
            ":sitename" => $siteName,
            ":siteimage" => $siteImage,
            ":description" => $siteDescription,
            ":opdays" => $opdays,
            ":price" => $sitePrice
        ]);
    }

    public function editSite($siteId, $siteName, $sitePrice, $siteDescription, $opdays, $imageName = null) {
        if ($imageName) {
            $query = "UPDATE sites SET sitename = :sitename, siteimage = :siteimage, description = :description, opdays = :opdays, price = :price WHERE siteid = :siteid";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                ":sitename" => $siteName,
                ":siteimage" => $imageName,
                ":description" => $siteDescription,
                ":opdays" => $opdays,
                ":price" => $sitePrice,
                ":siteid" => $siteId
            ]);
        } else {
            $query = "UPDATE sites SET sitename = :sitename, description = :description, opdays = :opdays, price = :price WHERE siteid = :siteid";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                ":sitename" => $siteName,
                ":description" => $siteDescription,
                ":opdays" => $opdays,
                ":price" => $sitePrice,
                ":siteid" => $siteId
            ]);
        }
    }

    public function getSites() {
        $query = "SELECT siteid, sitename, siteimage, description, opdays, rating, price FROM sites WHERE status = 'displayed'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSiteList() {
        $query = "SELECT * FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
        $params[":siteid"] = $siteid;
        
        $sql = "UPDATE " . $this->table . " SET " . implode(", ", $fields) . " WHERE siteid = :siteid";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($params);
    }

    public function addSiteRecord($updateData) {
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

    public function deleteSite($siteId) {
        $deleteQuery = "DELETE FROM sites WHERE siteid = ?";
        $stmt = $this->conn->prepare($deleteQuery);
        return $stmt->execute([$siteId]);
    }

    public static function binaryToDays($binaryString) {
        $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        $schedule = [];
        for ($i = 0; $i < 7; $i++) {
            if ($binaryString[$i] === '1') {
                $schedule[] = $days[$i];
            }
        }
        return implode(', ', $schedule);
    }
}
?>


