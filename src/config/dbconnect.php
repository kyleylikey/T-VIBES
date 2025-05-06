<?php
class Database {
    private $host = "taaltourism.database.windows.net";
    private $username = "adminoftaal";
    private $pass = "4D8b3>BB@)";
    private $dbname = "TaalTourismDB";
    public $conn;

    public function getConnection() {
            $this->conn = null;

            try {
                $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->dbname, $this->username, $this->pass);
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch(PDOException $exception) {
                echo "Connection error: " . $exception->getMessage();
            }

            return $this->conn;
        }
    }
?>