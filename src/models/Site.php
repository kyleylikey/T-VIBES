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
}
