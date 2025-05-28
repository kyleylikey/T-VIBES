<?php
class Site {
    private $conn;
    private $table = '[taaltourismdb].[sites]';

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
        $opdays = str_pad(substr($opdays, 0, 7), 7, '0', STR_PAD_RIGHT);
        $opdaysByte = pack('C', bindec($opdays));  // convert to binary byte
        
        $query = "INSERT INTO [taaltourismdb].[sites] (sitename, siteimage, description, opdays, price, status, rating, rating_cnt)
                  VALUES (?, ?, ?, ?, ?, 'displayed', 0, 0)";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([
            $siteName,
            $siteImage,
            $siteDescription,
            $opdaysByte,
            $sitePrice
        ]);
    }

    public function getSiteImage($siteId) {
        $query = "SELECT siteimage FROM [taaltourismdb].[sites] WHERE siteid = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$siteId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['siteimage'] : null;
    }

    public function editSite($siteId, $siteName, $sitePrice, $siteDescription, $opdays, $imageName = null) {
        $opdays = str_pad(substr($opdays, 0, 7), 7, '0', STR_PAD_RIGHT);
        $opdaysByte = pack('C', bindec($opdays));  // convert to binary byte
        
        if ($imageName) {
            $query = "UPDATE [taaltourismdb].[sites] SET 
                    sitename = ?, 
                    siteimage = ?, 
                    description = ?, 
                    opdays = ?, 
                    price = ? 
                    WHERE siteid = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                $siteName,
                $imageName,
                $siteDescription,
                $opdaysByte,
                $sitePrice,
                $siteId
            ]);
        } else {
            $query = "UPDATE [taaltourismdb].[sites] SET 
                    sitename = ?, 
                    description = ?, 
                    opdays = CONVERT(BINARY(7), ?), 
                    price = ? 
                    WHERE siteid = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                $siteName,
                $siteDescription,
                $opdays,
                $sitePrice,
                $siteId
            ]);
        }
    }

    public function getSites() {
        $query = "SELECT siteid, sitename, siteimage, description, opdays, rating, price, rating_cnt FROM [taaltourismdb].[sites] WHERE status = 'displayed'";
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
    function getSiteDetails($siteid) {
        $db = new Database();
        $conn = $db->getConnection();
    
        $query = "SELECT * FROM [taaltourismdb].[sites] WHERE siteid = :siteid";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':siteid', $siteid, PDO::PARAM_INT);
        $stmt->execute();
    
        return $stmt->fetch(PDO::FETCH_ASSOC);
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
        $query = "SELECT TOP 3 s.siteid, s.sitename, s.siteimage, s.description, s.opdays, s.rating as ratings, s.rating_cnt, s.price, s.status, SUM(t.companions) as visitor_count 
                  FROM [taaltourismdb].[sites] s 
                  LEFT JOIN [taaltourismdb].[tour] t ON s.siteid = t.siteid AND t.status = 'accepted'
                  WHERE YEAR(t.date) = :currentYear 
                  GROUP BY s.siteid, s.sitename, s.siteimage, s.description, s.opdays, s.rating, s.price, s.status, s.rating_cnt
                  ORDER BY visitor_count DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':currentYear', $currentYear, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }

    public function deleteSite($siteId) {
        $imageFilename = $this->getSiteImage($siteId);
        
        // Delete the database record
        $deleteQuery = "DELETE FROM [taaltourismdb].[sites] WHERE siteid = ?";
        $stmt = $this->conn->prepare($deleteQuery);
        $success = $stmt->execute([$siteId]);
        
        // If database deletion was successful and we have an image filename, delete the file
        if ($success && $imageFilename) {
            $imagePath = $_SERVER['DOCUMENT_ROOT'] . '/public/uploads/' . $imageFilename;
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }
        
        return $success;
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

    public function rateSite($siteId, $rating) {
        $rating = min(5, max(0, $rating));
        
        $query = "UPDATE [taaltourismdb].[sites] SET rating = rating + :rating, rating_cnt = rating_cnt + 1 WHERE siteid = :siteid";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([
            ":rating" => $rating,
            ":siteid" => $siteId
        ]);
    }
}
?>